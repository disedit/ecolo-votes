<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\EditionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = ['open', 'debate'];

    /**
     * Edition
     */
    public function edition(): BelongsTo {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Options
     */
    public function options(): HasMany {
        return $this->hasMany(VoteOption::class);
    }

    /**
     * Ballots
     */
    public function ballots(): HasMany {
        return $this->hasMany(VoteBallot::class);
    }

    /**
     * Winner
     */
    public function winner(): HasOne {
        return $this->hasOne(VoteOption::class, 'id', 'winner_id');
    }

    /**
     * Get the current vote
     */
    static public function current(): Self | null {
        return Self::where('open', 1)->first();
    }

    /**
     * Get the current vote
     */
    static public function currentOrRecentlyClosed(): Self | null {
        return Self::where('open', 1)
            ->orWhere('debate', 1)
            ->orWhere('closed_at', '>', now()->subMinute())
            ->orderBy('open', 'desc')
            ->orderBy('debate', 'desc')
            ->orderBy('closed_at', 'desc')
            ->first();
    }

    /**
     * Open vote
     */
    public function openVote(): bool {
        $this->closeAllVotes();
        $this->open = 1;
        $this->opened_at = now();

        // Insert or reset ballots
        VoteBallot::where('vote_id', $this->id)->update(['votes' => 0]);
        $ballots = Code::all()->map(function ($code) {
            return ['vote_id' => $this->id, 'code_id' => $code->id, 'votes' => 1, 'checked_in' => ($code->used_at) ? 1 : 0];
        })->toArray();
        VoteBallot::upsert($ballots, uniqueBy: ['vote_id', 'code_id'], update: ['votes', 'checked_in']);

        return $this->save();
    }

    /**
     * Close vote
     */
    public function closeVote(): bool {
        $results = $this->results();
        $winner = $results['winner'];
        $this->closeAllVotes();
        $this->closed_at = now();
        $this->winner_id = ($winner && $winner !== 'tie') ? $winner->id : null;
        return $this->save();
    }

    /**
     * Open debate on a vote
     */
    public function openDebate(): bool {
        $this->closeAllVotes();
        $this->debate = 1;
        return $this->save();
    }

    /**
     * Close debate on a vote
     */
    public function closeDebate(): bool {
        return $this->closeAllVotes();
    }

    /**
     * Close all votes
     */
    public function closeAllVotes(): bool {
        return DB::table('votes')->update(['open' => 0, 'debate' => 0]);
    }

    /**
     * Delete vote, options and ballots
     */
    public function deleteVote(): bool {
        // Prevent foreign key constraint error
        $this->winner_id = null;
        $this->save();

        // Delete ballots and options
        $this->ballots->each->delete();
        $this->options->each->delete();

        // Delete vote
        return $this->delete();
    }

    /**
     * Results attribute
     */
    public function getResultsAttribute(): array {
        return $this->results();
    }

    /**
     * Was recently closed attribute
     */
    public function getRecentlyClosedAttribute(): bool {
        return $this->closed_at > now()->subMinute();
    }

    /**
     * Calculate results
     */
    public function results(): array {
        $ballots = $this->ballots()->get();
        $options = $this->options()->get();
        
        // Results tally
        $totals = ['turnout' => 0, 'votes_cast' => 0];
        $abstentions = 0;
        $noes = 0;
        $tally = $options->mapWithKeys(fn ($option) => [$option->id => 0]);
        
        // Add votes to tally
        foreach($ballots as $ballot) {
            $votedFor = $ballot->vote_option_ids;
            if ($votedFor === null) continue;
            foreach ($votedFor as $optionId) {
                $option = $options->first(fn ($option) => $option->id === $optionId);
                $tally[$optionId] += $ballot->votes;
                $totals['votes_cast'] += $ballot->votes;
                $abstentions += $option->is_abstain ? $ballot->votes : 0;
                $noes += $option->is_no ? $ballot->votes : 0;
            }
            $totals['turnout']++;
        }

        // Add percentages and winner
        $results = $this->addPercentagesAndSort($options, $tally, $abstentions, $totals);
        $winner = $this->getWinnerFrom($results);

        return [
            'codes' => $ballots->count(),
            'in_use' => $ballots->sum('checked_in'),
            'totals' => $totals,
            'noes' => $noes,
            'abstentions' => $abstentions,
            'options' => $results,
            'winner' => $winner,
        ];
    }

    /**
     * Format results with percentages
     */
    private function addPercentagesAndSort(Collection $options, Collection $tally, int $abstentions, array $totals): Collection {
        $results = $options->map(function ($option) use ($tally, $abstentions, $totals) {
            $option->votes = $tally[$option->id];
            $option->percentages = [
                'with_abstentions' => [
                    'turnout' => ($totals['turnout'] === 0) ? 0 : $option->votes / $totals['turnout'],
                    'votes_cast' => ($totals['votes_cast'] === 0) ? 0 : $option->votes / $totals['votes_cast']
                ],
                'without_abstentions' => [
                    'turnout' => ($totals['turnout'] - $abstentions === 0) ? 0 : $option->votes / ($totals['turnout'] - $abstentions),
                    'votes_cast' => ($totals['votes_cast'] - $abstentions === 0) ? 0 : $option->votes / ($totals['votes_cast'] - $abstentions)
                ]
            ];
            return $option;
        });

        if ($this->type === 'options') {
            $resultsWithoutNoAbs = $results->filter(fn ($option) => $option->is_abstain === 0 && $option->is_no === 0);
            $absKey = $this->with_abstentions ? 'with_abstentions' : 'without_abstentions';
            $sortByKey = 'percentages.' . $absKey . '.' . $this->relative_to;
            $sortedResults = collect($resultsWithoutNoAbs)->sortByDesc($sortByKey)->values();
            // Add back noes and abstentions
            $results->each(function ($option) use ($sortedResults) {
                if ($option->is_no || $option->is_abstain) {
                    $sortedResults->push($option);
                }
            });
            return $sortedResults;
        }

        return $results;
    }

    /**
     * Declare winner
     */
    private function getWinnerFrom(Collection $results): object | string | null {
        if (count($results) === 0) return null;

        $absKey = $this->with_abstentions ? 'with_abstentions' : 'without_abstentions';
        $sortByKey = 'percentages.' . $absKey . '.' . $this->relative_to;
        $sortedResults = $results->filter(fn ($option) => !$option->is_abstain)->sortByDesc($sortByKey)->values();

        if ($this->majority === 'simple') {
            if ($sortedResults[0]->votes === 0) return null;
            if ($sortedResults[0]->votes === $sortedResults[1]->votes) return 'tie';
            return $sortedResults[0];
        }

        return $this->aboveThreshold($sortedResults);
    }

    /**
     * Calculate threshold
     */
    private function aboveThreshold(Collection $sortedResults): object | string | null {
        $absKey = $this->with_abstentions ? 'with_abstentions' : 'without_abstentions';

        if ($sortedResults[0]->percentages[$absKey][$this->relative_to] === $sortedResults[1]->percentages[$absKey][$this->relative_to]) return 'tie';

        if ($this->majority === '50') {
            $winner = ($sortedResults[0]->percentages[$absKey][$this->relative_to] > .5) ? $sortedResults[0] : null;
        } else {
            $winner = ($sortedResults[0]->percentages[$absKey][$this->relative_to] >= 2/3) ? $sortedResults[0] : null;
        }

        // If no winner in Yes/No/Abstain vote, No wins by default
        if ($this->type === 'yesno' && !$winner) {
            return $sortedResults->first(fn ($option) => $option->is_no);
        }

        return $winner;
    }

    /**
     * Filter open votes
     */
    public function scopeOpen($query)
    {
        return $query->where('open', 1);
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new EditionScope);
    }
}
