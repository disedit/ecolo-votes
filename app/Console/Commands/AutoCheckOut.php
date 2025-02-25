<?php

namespace App\Console\Commands;

use App\Models\Attendee;
use Illuminate\Console\Command;

class AutoCheckOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delegates:check-out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attendees = Attendee::with('user')->where('votes', '>', 0)->get();
        $count = $attendees->count();

        if (!$this->confirm($count . ' delegates will be checked OUT. Do you wish to continue?')) {
            $this->line('Aborted');
            return;
        }

        foreach($attendees as $attendee) {
            $attendee->checkOut('COMMAND');
        }

        $this->line('All delegates checked out.');
    }
}
