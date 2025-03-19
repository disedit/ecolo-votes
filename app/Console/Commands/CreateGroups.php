<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;

class CreateGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a group';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text(label: 'Group name', required: true);

        $isCodes = confirm(
            label: 'Is this group for Codes?',
            default: false,
            yes: 'Yes',
            no: 'No',
        );

        $group = new Group;
        $group->name = $name;
        $group->is_codes = $isCodes;
        $group->save();

        $this->info('✅ Group created');
    }
}
