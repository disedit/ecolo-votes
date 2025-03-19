<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Group;
use App\Models\LoginToken;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Input
        $email = text(label: 'E-mail', required: true);
        $firstName = text(label: 'Prénom');
        $lastName = text(label: 'Nom');
        $groups = Group::pluck('name', 'id');

        if (!count($groups)) {
            $groupName = text(
                label: 'Créez un nouveau groupe pour les administrateurs',
                placeholder: 'Par exemple: Admins',
                default: 'Admins'
            );
            $group = new Group;
            $group->name = $groupName;
            $group->save();
            $group = $group->id;
        } else {
            $group = select(
                label: 'Sélectionnez un groupe',
                options: $groups,
                scroll: 10
            );
        }

        // Create user
        $user = new User;
        $user->email = $email;
        $user->first_name = $firstName ?? 'Admin';
        $user->last_name = $lastName ?? 'User';
        $user->password = Hash::make(Str::random(24));
        $user->group_id = $group;
        $user->role = 'admin';
        $user->save();

        // Create login token
        $loginToken = LoginToken::create($user);

        $this->table(
            ['Email', 'Temporary Token'],
            [[$email, $loginToken->token]]
        );
    }
}
