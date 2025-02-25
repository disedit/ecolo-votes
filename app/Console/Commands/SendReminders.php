<?php

namespace App\Console\Commands;

use App\Models\Attendee;
use Illuminate\Console\Command;
use App\Notifications\PaymentReminder;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminders {--test=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to unpaid tickets';

    /**
     * Form answers
     */
    protected $fields;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $test = $this->option('test');

        $attendees = Attendee::with('user')
            ->leftJoin('payments', 'payments.attendee_id', '=', 'attendees.id')
            ->where(function ($query) use ($test) {
                if ($test) {
                    $query->where('user_id', $test);
                } else {
                    $query
                        ->whereNull('payments.id')
                        ->where('confirmed', 1)
                        ->where('notified', 1)
                        ->where('notifiable', 1)
                        ->where('paid', 0)
                        ->whereNull('registered_by_user_id')
                        ->where('ticket_notified', 0);
                }
            })->get();

        $count = $attendees->count();

        if (!$this->confirm($count . ' will be notified. Do you wish to continue?')) {
            $this->line('Aborted');
            return;
        }

        foreach($attendees as $attendee) {
            $attendee->user->notify(new PaymentReminder);
        }

        $this->line('Reminders queued for notfying.');
    }

}
