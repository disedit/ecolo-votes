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
        $ballots = Attendee::voters()->get()->map(function ($voter) {
            return ['vote_id' => $this->id, 'attendee_id' => $voter->id, 'votes' => $voter->votes, 'checked_in' => ($voter->checked_in) ? 1 : 0];
        })->toArray();
        VoteBallot::upsert($ballots, uniqueBy: ['vote_id', 'attendee_id'], update: ['votes', 'checked_in']);

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
    public function getAbridgedResultsAttribute(): array {
        return $this->results(abridged: true);
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
    public function results(bool $abridged = false): array {
        $results = VoteOption::selectRaw('vote_options.id, SUM(votes) AS votes_cast, vote_options.name, vote_options.gender, vote_options.is_abstain')
            ->leftJoin('vote_ballots', 'vote_ballots.vote_option_id', '=', 'vote_options.id')
            ->where('vote_options.vote_id', $this->id)
            ->groupBy('vote_options.id', 'vote_options.name', 'vote_options.gender', 'vote_options.is_abstain')
            ->orderBy('vote_options.id', 'asc')
            ->get();

        $rollcall = VoteBallot::select('vote_ballots.vote_id', 'attendees.id as attendee_id', 'users.first_name', 'users.last_name', 'attendees.votes', 'vote_ballots.voted_at', 'vote_ballots.checked_in')
            ->join('attendees', 'attendees.id', '=', 'vote_ballots.attendee_id')
            ->join('users', 'users.id', '=', 'attendees.user_id')
            ->where('vote_ballots.vote_id', $this->id)
            ->where('vote_ballots.votes', '>', '0')
            ->orderBy('users.last_name', 'asc')
            ->get();

        $checkedIn = $rollcall->sum('checked_in');
        $turnout = $rollcall->sum(fn ($delegate) => ($delegate->voted_at) ? 1 : 0);
        $allocatedVotes = $this->getAllocatedVotes();
        $votesCast = $this->getVotesCast($results);
        $resultsWithPercentages = $this->addPercentages($results, $votesCast, $allocatedVotes);

        return [
            'checked_in' => $checkedIn,
            'turnout' => $turnout,
            'votes_cast' => $votesCast,
            'votes_cast_with_abstentions' => $results->sum('votes_cast'),
            'allocated_votes' => $allocatedVotes,
            'winner' => $this->getWinnerFrom($resultsWithPercentages),
            'results' => $resultsWithPercentages,
            'rollcall' => (!$abridged) ? $rollcall : null
        ];
    }

    /**
     * Declare winner
     */
    private function getWinnerFrom(Collection $resultsWithAbstentions): object | string | null {
        // Remove abstentions and sort by votes
        $results = $resultsWithAbstentions->filter(function (object $result) {
            return !$result->is_abstain;
        })->sortByDesc('percentage.votes_cast')->values()->all();

        if (count($results) === 0) return null;

        // Get winner depending on type of majority
        switch($this->majority){
            case 'absolute':
                return $this->aboveThreshold($results, 0.5, 'votes_cast');
                break;
            case '2/3_all':
                return $this->aboveOrEqualThreshold($results, 2/3, 'allocated_votes');
                break;
            case '3/4_all':
                return $this->aboveOrEqualThreshold($results, 3/4, 'allocated_votes');
                break;
            case '2/3_cast':
                return $this->aboveOrEqualThreshold($results, 2/3, 'votes_cast');
                break;
            case '3/4_cast':
                return $this->aboveOrEqualThreshold($results, 3/4, 'votes_cast');
                break;
            default: // Simple majority
                if ($results[0]->votes_cast === 0) return null;
                if ($results[0]->votes_cast === $results[1]->votes_cast) return 'tie';
                return $results[0];
                break;
        }
    }

    /**
     * Calculate threshold
     */
    private function aboveThreshold(array $results, float $threshold, string $type, bool $equal = false): object | null {
        if ($equal) {
            $winner = ($results[0]->percentage[$type] >= $threshold) ? $results[0] : null;
        } else {
            $winner = ($results[0]->percentage[$type] > $threshold) ? $results[0] : null;
        }
        
        
        if ($this->type === 'yesno' && !$winner) {
            return collect($results)->first(fn ($result) => $result->name === 'No');
        }

        return $winner;
    }

    /**
     * Calculate threshold, including equal
     */
    private function aboveOrEqualThreshold(array $results, float $threshold, string $type): object | null {
        return $this->aboveThreshold(results: $results, threshold: $threshold, type: $type, equal: true);
    }

    /**
     * Get votes cast, ignoring abstentions
     */
    private function getVotesCast(Collection $results): int {
        return $results->sum(function (object $result) {
            return ($result->is_abstain) ? 0 : $result->votes_cast;
        });
    }

    /**
     * Calculate allocated votes
     */
    private function getAllocatedVotes(): int {
        return VoteBallot::where('vote_id', $this->id)->get()->sum('votes');
    }

    /**
     * Format results with percentages, ignoring abstentions
     */
    private function addPercentages(Collection $results, int $votesCast, int $allocatedVotes): Collection {
        $resultsWithPercentages = $results->map(function (object $result) use ($votesCast, $allocatedVotes) {
            $result->votes_cast = intval($result->votes_cast) ?? 0;
            $result->percentage = [
                'votes_cast' => ($result->is_abstain || $votesCast === 0) ? 0 : $result->votes_cast / $votesCast,
                'allocated_votes' => ($result->is_abstain || $allocatedVotes === 0) ? 0 : $result->votes_cast / $allocatedVotes
            ];
            return $result;
        });

        if ($this->type === 'options') {
            return collect($resultsWithPercentages->sortByDesc('percentage.votes_cast')->values()->all());
        }

        return $resultsWithPercentages;
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
