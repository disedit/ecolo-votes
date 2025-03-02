<?php

namespace App\Rules;

use Closure;
use App\Models\VoteOption;
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
        foreach($value as $option_id) {
            $option = VoteOption::findOrFail($option_id);

            if ($option->vote_id !== intval(request()->input('vote_id'))) {
                $fail('You can\'t vote for this option.');
            }
        }
    }
}
