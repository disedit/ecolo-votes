<?php

namespace App\Rules;

use Closure;
use App\Models\Vote;
use Illuminate\Contracts\Validation\ValidationRule;

class VoteIsOpen implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check vote is open
        $vote = Vote::findOrFail($value);
        if (!$vote->open) {
            $fail('Vote is closed');
        }
        
        // Check user hasn't voted yet
        $ballot = $vote->ballots()->where('code_id', request()->user()->code()->id)->first();
        if ($ballot->voted_at || $ballot->vote_option_id) {
            $fail('Your vote has already been cast in this vote.');
        }
    }
}
