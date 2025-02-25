<?php

namespace App\Console\Commands;

use App\Models\Attendee;
use App\Models\LoginToken;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class CreateTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendees:create-tokens {--test=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates tokens for delegates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $test = $this->option('test');

        $attendees = Attendee::where(function ($query) use ($test) {
            if ($test) {
                $query->where('user_id', $test);
            } else {
                $query->where('votes', '>', 0);
            }
        })->get();

        $count = $attendees->count();

        if (!$this->confirm($count . ' will be given new tokens. Do you wish to continue?')) {
            $this->line('Aborted');
            return;
        }

        foreach($attendees as $attendee) {
            $loginToken = new LoginToken;
            $loginToken->user_id = $attendee->user_id;
            $loginToken->token = Str::random(60);
            $loginToken->expires_at = now()->addDays(2);
            $loginToken->save();
        }

        $this->line('Tokens created.');
    }
}
