<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\User;
use App\Models\Group;
use App\Models\Edition;
use App\Models\Attendee;
use App\Libraries\Jotform;
use App\Models\LoginToken;
use Illuminate\Support\Str;
use App\Models\AttendeeDetail;
use Illuminate\Console\Command;
use App\Notifications\TicketIssued;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Notifications\PaymentNotification;

class SyncAttendees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendees:sync {--edition=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs attendees with JotForm';

    /**
     * Form answers
     */
    protected $fields;

    /**
     * Mappings
     */
    public $mappings;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $editionId = $this->option('edition');

        if ($editionId) {
            $edition = Edition::find($editionId);
        } else {
            $edition = Edition::current()->first();
        }
        
        $jotform = new JotForm();
        $form = $jotform->submissions($edition->form_id);
        $pressForm = $jotform->submissions($edition->press_form_id);
        $submissions = array_merge($form->content, $pressForm->content);
        $this->mappings = $edition->form_mappings;

        $bar = $this->output->createProgressBar(count($submissions));
        $exceptions = [];

        foreach($submissions as $submission) {
            // Normalize submission fields according to mappings
            $fields = $this->normalizeFields($submission->answers, $edition->form_mappings);

            // Find attendee if it already exists (has submission_id)
            $attendee = Attendee::where('form_submission_id', $submission->id)->first();

            if ($attendee) {
                if ($submission->status === 'DELETED' || $submission->status === 'ARCHIVED') {
                    $this->deleteAttendee($attendee);
                    $message = 'Deleted attendee ' . $fields['first_name'] . ' ' . $fields['last_name'] . ' ' . $fields['email'];
                    $exceptions[] = $message;
                    Log::debug('[attendees:sync] ' . $message);
                    continue;
                }

                $this->updateAttendee($attendee, $fields);
            } else {
                if ($submission->status === 'DELETED' || $submission->status === 'ARCHIVED') {
                    continue;
                }

                $user = $this->registerAttendee($edition, $submission->id, $fields);

                if (!$user) {
                    $message = 'Skiped user: ' . $fields['first_name'] . ' ' . $fields['last_name'] . ' ' . $fields['email'];
                    $exceptions[] = $message;
                    Log::debug('[attendees:sync] ' . $message);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n\nFinished processing entries.\n\n");

        $this->deleteMissingAttendees($submissions, $edition);
        $this->deleteUnassignedUsers();
        $this->assignRelationships();
        $this->notifyUsers();

        foreach($exceptions as $exception) {
            $this->warn($exception);
        }
    }

    /** 
     * Convert jotForm answers into standard array
     */
    private function normalizeFields($fields, $mappings): Array {
        $answers = collect($fields);

        // Remove presentational fields
        [$presentationalFields, $dataFields] = $answers->partition(function ($answer) use ($mappings) {
            return in_array($answer->name, $mappings->presentational_fields) || str_starts_with($answer->name, 'present');
        });
        $this->fields = $dataFields;

        // User information
        $name = $this->field($mappings->fields->name);
        $user = [
            'first_name' => $name->first,
            'last_name' => $name->last,
            'email' => $this->field($mappings->fields->email),
            'phone' => $this->field($mappings->fields->phone),
            'country' => $this->field($mappings->fields->country),
            'confirmed' => ($this->field($mappings->fields->confirmed)) ? 1 : 0,
            'notifiable' => ($this->field($mappings->fields->notifiable)) ? 1 : 0,
            'paid' => ($this->field($mappings->fields->paid)) ? 1 : 0,
            'custom_fee' => $this->field($mappings->fields->custom_fee),
            'group_other' => $this->field($mappings->fields->group_other),
            'votes' => $this->field($mappings->fields->votes)
        ];

        // Gender
        $gender = $this->field($mappings->fields->gender);
        $gender = (gettype($gender) === 'string') ? $gender : 'O';
        $user['gender'] = (isset($mappings->genders->$gender)) ? $mappings->genders->$gender : 'O';

        // Asign group
        $groupText = $this->field($mappings->fields->group);
        $group = Group::where('name', '=', $groupText)->first();
        $user['group'] = $group->id ?? $mappings->group_fallback;
        if (!isset($group->id)) {
            $message = 'Resorting to fallback for group ('.$groupText.') on user ' . $user['email'];
            $exceptions[] = $message;
            Log::debug('[attendees:sync] ' . $message);
        }
        
        // Asign type
        $type = $this->field($mappings->fields->type);
        $typeId = ($type && isset($mappings->types->$type)) ? $mappings->types->$type : $mappings->type_fallback;

        if (!isset($mappings->types->$type)) {
            $message = 'Resorting to generic fallback for type ('.$type.') on user ' . $user['email'];
            $exceptions[] = $message;
            Log::debug('[attendees:sync] ' . $message);
        }

        if (!is_numeric($typeId)) {
            $typeRefs = $mappings->subtypes->$typeId;
            $typeResponse = $this->field($typeId);
            if (!isset($typeRefs->$typeResponse)) {
                $message = 'Resorting to fallback for type ('.$typeResponse.') on user ' . $user['email'];
                $exceptions[] = $message;
                Log::debug('[attendees:sync] ' . $message);
                $typeId = $mappings->subtypes->$typeId->fallback;
            } else {
                $typeId = $typeRefs->$typeResponse;
            }
        }
        $user['type'] = $typeId ?? $mappings->type_fallback;

        // Is delegate?
        $user['is_delegate'] = in_array($user['type'], $mappings->delegates);

        // Other fields
        [$basicFields, $otherFields] = $dataFields->partition(function ($answer) use ($mappings) {
            return in_array($answer->name, array_values((array) $mappings->fields));
        });
        $user['other_fields'] = $otherFields;

        // Subdelegates
        $subdelegates = $this->field($mappings->fields->subdelegates);
        $subdelegateList = [];
        if ($subdelegates) {
            $subdelegates = json_decode($subdelegates);
            foreach($subdelegates as $subdelegate) {
                $subdelegateBasicFields = collect($subdelegate);
                $subdelegateList[] = [
                    'first_name' => $this->subfield($subdelegateBasicFields, $mappings->subdelegates->first_name),
                    'last_name' => $this->subfield($subdelegateBasicFields, $mappings->subdelegates->last_name),
                    'email' => $this->subfield($subdelegateBasicFields, $mappings->subdelegates->email),
                ];
            }
        }
        $user['subdelegates'] = $subdelegateList;

        return $user;
    }

    /**
     * Creates a user and associated attendee, with details
     */
    private function registerAttendee(Edition $edition, $submissionId, $fields): User | bool
    {
        // Check if email has already been registered
        $existingUser = User::where('email', $fields['email'])->first();

        if ($existingUser) {
            if ($fields['first_name'] !== $existingUser->first_name && $fields['last_name'] !== $existingUser->last_name) {
                $fields['email'] = $this->getTempEmail($fields['email']);
            } else {
                return false;
            }
        }

        $user = $this->createUser($edition, $fields);
        $attendee = $this->createAttendee($edition, $user, $submissionId, $fields);
        $this->createAttendeeDetails($attendee, $fields);

        return $user;
    }

    /**
     * Update attendee info and details
     */
    private function updateAttendee(Attendee $attendee, $fields)
    {
        // Check if email has already been registered
        $existingUser = User::where('email', $fields['email'])->first();

        if ($existingUser && $existingUser->id !== $attendee->user->id) {
            $fields['email'] = $this->getTempEmail($fields['email']);
        }

        $this->updateUser($attendee->user, $fields);
        $this->updateAttendeeRow($attendee, $fields);
        $this->updateAttendeeDetails($attendee, $fields);
    }

    /**
     * Delte an attendee and related user
     */
    private function deleteAttendee(Attendee $attendee): bool
    {
        return $attendee->delete();
    }

    /**
     * Creates a user
     */
    private function createUser(Edition $edition, $fields): User
    {
        $user = new User;
        $user->group_id = $fields['group'];
        $user->first_name = $fields['first_name'];
        $user->last_name = $fields['last_name'];
        $user->email = $fields['email'];
        $user->password = Hash::make(Str::random(40));
        $user->phone = $fields['phone'];
        $user->country = $fields['country'];
        $user->gender = $fields['gender'];
        $user->group_other = $fields['group_other'];

        $user->save();

        // Add a LoginToken
        $editionEndDate = new Carbon($edition->date_end);
        $loginToken = new LoginToken;
        $loginToken->user_id = $user->id;
        $loginToken->token = Str::random(60);
        $loginToken->expires_at = $editionEndDate->addDays(2);
        $loginToken->save();

        return $user;
    }

    /**
     * Updates user info
     */
    private function updateUser(User $user, $fields): User
    {
        $user->group_id = $fields['group'];
        $user->first_name = $fields['first_name'];
        $user->last_name = $fields['last_name'];
        $user->email = $fields['email'];
        $user->phone = $fields['phone'];
        $user->country = $fields['country'];
        $user->gender = $fields['gender'];
        $user->group_other = $fields['group_other'];

        $user->save();

        return $user;
    }
    

    /**
     * Creates an associated attedee
     */
    private function createAttendee(Edition $edition, User $user, $submissionId, $fields): Attendee
    {
        $type = Type::find($fields['type']);

        $attendee = new Attendee;
        $attendee->edition_id = $edition->id;
        $attendee->user_id = $user->id;
        $attendee->type_id = $type->id;
        $attendee->qr_code = Str::random(16);
        $attendee->confirmed = $fields['confirmed'];
        $attendee->notifiable = $fields['notifiable'];
        $attendee->form_submission_id = $submissionId;
        $attendee->paid = ((!count($type->fees) || $fields['custom_fee'] === '0.00') && $fields['confirmed']) ? 1 : 0;
        $attendee->subdelegates = (count($fields['subdelegates']) > 0) ? $fields['subdelegates'] : null;
        $attendee->custom_fee = $fields['custom_fee'];
        $attendee->votes = $fields['votes'] ?? 0;

        $attendee->save();

        return $attendee;
    }

    /**
     * Update attendee info
     */
    private function updateAttendeeRow(Attendee $attendee, $fields): Attendee
    {
        $type = Type::find($fields['type']);

        $attendee->type_id = $fields['type'];
        $attendee->confirmed = $fields['confirmed'];
        $attendee->notifiable = $fields['notifiable'];
        $attendee->paid = ((!count($type->fees) || $fields['custom_fee'] === '0.00') && $fields['confirmed']) ? 1 : $attendee->paid;
        $attendee->subdelegates = (count($fields['subdelegates']) > 0) ? $fields['subdelegates'] : null;
        $attendee->custom_fee = $fields['custom_fee'];
        if ($this->mappings->sync_votes) {
            $attendee->votes = $fields['votes'] ?? 0;
        }

        $attendee->save();

        return $attendee;
    }

    /**
     * Create attendee details
     */
    private function createAttendeeDetails(Attendee $attendee, $fields): void
    {
        foreach($fields['other_fields'] as $field) {
            if (isset($field->answer) && $field->answer) {
                $attendeeDetails = new AttendeeDetail;
                $attendeeDetails->attendee_id = $attendee->id;
                $attendeeDetails->field_key = $field->name;
                $attendeeDetails->field_label = $field->text;
                $attendeeDetails->field_value = (gettype($field->answer) === 'string') ? $field->answer : json_encode($field->answer);
                $attendeeDetails->save();
            }
        }
    }

    /**
     * Update attendee details
     */
    private function updateAttendeeDetails(Attendee $attendee, $fields): void
    {  
        foreach($fields['other_fields'] as $field) {
            if (!isset($field->answer) || $field->answer === '[]') {
                continue;
            }

            $row = AttendeeDetail::where('attendee_id', $attendee->id)
                ->where('field_key', $field->name)
                ->first();
           
            if ($row) {
                $row->field_label = $field->text;
                $row->field_value = (gettype($field->answer) === 'string') ? $field->answer : json_encode($field->answer);
                $row->save();
            } else {
                $newRow = new AttendeeDetail;
                $newRow->attendee_id = $attendee->id;
                $newRow->field_key = $field->name;
                $newRow->field_label = $field->text;
                $newRow->field_value = (gettype($field->answer) === 'string') ? $field->answer : json_encode($field->answer);
                $newRow->save();
            }
        }

        $keysOnForm = $fields['other_fields']->map(function (object $item, int $key) {
            return $item->name;
        })->values()->toArray();

        foreach($attendee->details as $attendeeDetail) {
            if (!in_array($attendeeDetail->field_key, $keysOnForm)) {
                $attendeeDetail->delete();
            }
        }
    }

    /**
     * Delete un
     */
    private function deleteMissingAttendees($submissions, Edition $edition)
    {
        $ids = collect($submissions)->map(function (object $item, int $key) {
            return $item->id;
        })->values()->toArray();

        foreach($edition->attendees as $attendee) {
            if (!in_array($attendee->form_submission_id, $ids)) {
                $this->deleteAttendee($attendee);
            }
        }

        return true;
    }

    /**
     * Delete unassigned users
     */
    private function deleteUnassignedUsers(): void
    {
        $users = User::where('role', 'user')->get();

        foreach($users as $user) {
            if (count($user->attendees)) continue;
            
            foreach($user->tokens as $token) {
                $token->delete();
            }

            $user->delete();
            $this->warn('Deleting unassigned user');
        }
    }

    /**
     * Link subdelegates to their parent delegate
     */
    private function assignRelationships(): void
    {
        $attendeesWithSubdelegates = Attendee::whereNotNull('subdelegates')->get();

        foreach($attendeesWithSubdelegates as $parent) {
            $subdelegates = $parent->subdelegates;
            
            foreach($subdelegates as $subdelegate) {
                $subuser = User::where('email', $subdelegate->email)->first();
                if (!$subuser) continue;
                $subattendee = Attendee::where('user_id', $subuser->id)->first();
                if (!$subattendee) continue;
                $subattendee->registered_by_user_id = $parent->user_id;
                $subattendee->save();
            }
        }
    }

    /**
     * Notify unotified users
     */
    public function notifyUsers(): void
    {
        $attendees = Attendee::whereNull('registered_by_user_id')->where('notified', 0)->where('notifiable', 1)->where('confirmed', 1)->get();

        foreach($attendees as $attendee) {
            if ($attendee->hasFees() && !$attendee->paid) {
                $attendee->user->notify(new PaymentNotification);
            } else {
                $attendee->ticket_notified = 1;
                $attendee->user->notify(new TicketIssued);
            }

            $attendee->notified = 1;
            $attendee->save();
        }
    }

    /**
     * Filters through fields on a JotForm response
     */
    private function field($key) {
        $field = $this->fields->filter(function ($value, $i) use ($key) {
            return $value->name === $key;
        })->first();

        return $field->answer ?? null;
    }

    /**
     * Filters through subfields on a JotForm field
     */
    private function subfield($fields, $key) {
        return $fields[$key] ?? '';
    }

    /**
     * Generates a random temp address for duplicates
     */
    private function getTempEmail($email): String {
        $random = rand(11111, 99999);
        $username = str_replace('@', '__at__', $email);
        return $username . $random . '@congress.europeangreens.eu';
    }
}
