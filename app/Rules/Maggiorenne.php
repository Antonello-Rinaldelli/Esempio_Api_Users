<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Maggiorenne implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $minDate = Carbon::now()->subYears(18)->format('Y-m-d');
        
        if ($value > $minDate) {
            $fail('Devi avere almeno 18 anni per registrarti.');
    }
}
}
