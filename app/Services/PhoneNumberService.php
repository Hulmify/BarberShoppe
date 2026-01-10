<?php

namespace App\Services;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class PhoneNumberService
{
    protected $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Parse and format phone number to E.164.
     * 
     * @param string $phoneNumber
     * @param string|null $timezone
     * @return string|null
     */
    public function formatE164(string $phoneNumber, ?string $timezone = null): ?string
    {
        $countryCode = $this->getCountryCodeFromTimezone($timezone);
        
        try {
            $numberProto = $this->phoneUtil->parse($phoneNumber, $countryCode);
            
            if ($this->phoneUtil->isValidNumber($numberProto)) {
                return $this->phoneUtil->format($numberProto, PhoneNumberFormat::E164);
            }
        } catch (NumberParseException $e) {
            // Log or handle error if needed
        }

        return null;
    }

    /**
     * Guess country code from timezone.
     * 
     * @param string|null $timezone
     * @return string
     */
    public function getCountryCodeFromTimezone(?string $timezone): string
    {
        if (!$timezone) {
            return 'US'; // Default
        }

        try {
            $tz = new \DateTimeZone($timezone);
            $location = $tz->getLocation();
            return $location['country_code'] ?? 'US';
        } catch (\Exception $e) {
            return 'US';
        }
    }
}
