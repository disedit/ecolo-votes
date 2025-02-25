<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CodeCorrect implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();

        if ($user->code_exception) return;

        if (empty($value)) {
            $fail('You need to enter the code shown on screen to confim your vote.');
        }

        $secret = config('google2fa.secret');
        $google2fa = app('pragmarx.google2fa');
        $google2fa->setOneTimePasswordLength(config('google2fa.password_length'));
        $google2fa->setKeyRegeneration(config('google2fa.regenerate_every'));
        $verified = $google2fa->verifyKey($secret, $value, 1);

        if (!$verified) {
            $fail('The code entered is not correct. The code shown on the screen may have changed. Please, check again.');
        }
    }
}
