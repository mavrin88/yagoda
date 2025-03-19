<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class SmsConfirmationService
{

    private $plusofonService;

    public function __construct()
    {
        $this->plusofonService = new PlusofonService();
    }

    /**
     * @param string $phoneNumber Номер телефона
     * @return void
     */
    public function sendSmsCode(string $phoneNumber): bool
    {
        try {
            $response = $this->plusofonService->sendFlashCall($phoneNumber);

            if (isset($response['data']['pin'])) {
                $smsCode = $response['data']['pin'];
                $expiresAt = now()->addSeconds(30);

                Session::put('sms_code', $smsCode);
                Session::put('sms_code_expires_at', $expiresAt);

                return true;
            } else {
//                Log::error('Error retrieving SMS code: ', $response);
                return false;
            }
        } catch (\Exception $e) {
//            Log::error('Error sending SMS code: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * @param string $smsCode SMS-код
     * @return bool
     */
    public function verifySmsCode(string $smsCode): bool
    {
        $storedSmsCode = Session::get('sms_code');

        if ($smsCode === $storedSmsCode) {
            $this->clearSmsCode();
        }

        return $smsCode === $storedSmsCode;
    }

    /**
     * @return void
     */
    public function clearSmsCode()
    {
        Session::forget('sms_code');
        Session::forget('sms_code_expires_at');
    }
}
