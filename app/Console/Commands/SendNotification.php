<?php

namespace App\Console\Commands;

use App\Models\Attendee;
use Illuminate\Console\Command;
use App\Notifications\VoteNotification;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notification {--test=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send vote notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $test = $this->option('test');

        $attendees = Attendee::with('user')
            ->where(function ($query) use ($test) {
                if ($test) {
                    $query->where('user_id', $test);
                } else {
                    $query->where('votes', '>', 0);
                    $query->whereNotNull('checked_in');
                }
            })->get();

        $count = $attendees->count();

        if (!$this->confirm($count . ' will be notified. Do you wish to continue?')) {
            $this->line('Aborted');
            return;
        }

        foreach($attendees as $attendee) {
            $attendee->user->notify(new VoteNotification);
        }

        $this->line('Notifications queued.');
    }
}
