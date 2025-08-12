<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsService
{
    public function sendSms(string $mobile_number, string $message): void
    {
        try {
            $twilio = new Client(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );

            $phoneNumber = '+63' . $mobile_number;

            Log::info('Attempting to send SMS', [
                'mobile_number' => $phoneNumber,
                'message' => $message
            ]);

            $response = $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.from_number'),
                    'body' => $message,
                ]
            );

            Log::info('SMS sent successfully', [
                'sid' => $response->sid,
                'status' => $response->status
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS', [
                'mobile_number' => $mobile_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
