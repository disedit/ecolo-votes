<?php

namespace App\Console\Commands;

use App\Models\Edition;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;

class CreateEdition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:edition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an edition';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = text(label: 'Titre', required: true);
        $shortTitle = text(
            label: 'Titre court',
            required: true,
            hint: 'Max. 10 caractères',
            validate: fn (string $value) => match (true) {
                strlen($value) > 10 => 'Le titre ne doit pas dépasser 10 caractères.',
                default => null
            }
        );
        $startDate = text(label: 'Date de début', required: true, hint: 'Format: YYYY-MM-DD');
        $endDate = text(label: 'Date de fin', required: true, hint: 'Format: YYYY-MM-DD');

        $edition = new Edition;
        $edition->title = $title;
        $edition->short_title = $shortTitle;
        $edition->date_start = $startDate;
        $edition->date_end = $endDate;
        $edition->current = 1;
        $edition->save();

        $this->info('✅ L\'édition a été créée');
    }
}
