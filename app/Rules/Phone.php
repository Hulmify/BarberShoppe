<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Phone implements ValidationRule
{
    protected $timezone;

    public function __construct($timezone = null)
    {
        $this->timezone = $timezone;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phoneService = resolve(\App\Services\PhoneNumberService::class);
        if (!$phoneService->formatE164($value, $this->timezone)) {
            $fail('The :attribute is not a valid phone number.');
        }
    }
}
