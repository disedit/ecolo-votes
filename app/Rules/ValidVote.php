<?php

namespace App\Rules;

use Closure;
use App\Models\Vote;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidVote implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Current vote 
        $vote = Vote::with('options')->find(intval(request()->input('vote_id')));

        if (!$vote) {
            $fail(__('vote.not_exists'));
        }
    
        // Check option_id exists and belongs to the vote
        $optionsExist = collect($value)->every(fn ($optionId) => $vote->options->contains(fn ($option) => $option->id === $optionId));

        if (!$optionsExist) {
            $fail(__('vote.options_not_valid'));
        }

        // Check that user has not selected more options than allowed
        if (count($value) > $vote->max_votes) {
            $fail(__('vote.over_limit'));
        }
    }
}
