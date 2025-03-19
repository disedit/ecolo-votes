<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LoginToken;
use Illuminate\Console\Command;
use function Laravel\Prompts\search;

class CreateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = search(
            label: 'Search for the user to generate a token for',
            options: fn (string $value) => strlen($value) > 0
                ? User::whereLike('first_name', "%{$value}%")
                    ->orWhereLike('email', "%{$value}%")
                    ->orWhereLike('last_name', "%{$value}%")
                    ->pluck('first_name', 'id')
                    ->all()
                : []
        );

        $user = User::find($userId);
        $loginToken = LoginToken::create($user);

        $this->table(
            ['Temporary Token'],
            [[$loginToken->token]]
        );
    }
}
