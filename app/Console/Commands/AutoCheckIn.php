<?php

namespace App\Console\Commands;

use App\Models\Attendee;
use Illuminate\Console\Command;

class AutoCheckIn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delegates:check-in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto check in delegates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attendees = Attendee::with('user')->where('votes', '>', 0)->get();
        $count = $attendees->count();

        if (!$this->confirm($count . ' delegates will be checked in. Do you wish to continue?')) {
            $this->line('Aborted');
            return;
        }

        foreach($attendees as $attendee) {
            $attendee->checkIn('COMMAND');
        }

        $this->line('All delegates checked in.');
    }
}
