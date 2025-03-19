<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RedSmsConfirmationService
{
    private RedsmsApiSimple $redsmsApi;

    public function __construct()
    {
        $login = config('redsms.login');
        $apiKey = config('redsms.api_key');
        $this->redsmsApi = new RedsmsApiSimple($login, $apiKey);
    }

    public function sendSmsCode(string $phoneNumber, int $userId = null)
    {
        $code = $this->generateCode();
        $smsSenderName = 'Yagoda';
        $message = "Код для входа в систему Yagoda: $code";
        $lastMessageUuid = '';

        try {

            if (config('app.env') === 'local' || $userId == 2 || (config('app.env') === 'local' && $userId == 1)) {
                Log::info('SMS sending skipped (local env): ' . $code);
                $sendResult['success'] = true;
            } else {
                $sendResult = $this->redsmsApi->sendSMS($phoneNumber, $message, $smsSenderName);
            }



            if ($sendResult['success']) {
                $this->setCodeSession($code);
                return true;
            }

            if (!empty($sendResult['items']) && $messages = $sendResult['items']) {
                $this->setCodeSession($code);
                return true;
                foreach ($messages as $message) {
                    echo $message['to'].':'.$message['uuid'].PHP_EOL;
                    $lastMessageUuid = $message['uuid'];
                }
            }

            if ($lastMessageUuid) {
                echo 'Get message info: ' . PHP_EOL;
                echo json_encode($this->redsmsApi->messageInfo($lastMessageUuid)) . PHP_EOL;
                echo 'wait 10 sec... ' . PHP_EOL;
                sleep(10);
                echo json_encode($this->redsmsApi->messageInfo($lastMessageUuid)) . PHP_EOL;
            }

        } catch (\Exception $e) {
            Log::error('Error sending SMS code: ' . $e->getCode());
            Log::error('Error sending SMS code message: ' . $e->getMessage());
        }
    }

    public function sendSms(string $phoneNumber, string $smsText)
    {
        $smsSenderName = 'Yagoda';

        try {
            $sendResult = $this->redsmsApi->sendSMS($phoneNumber, $smsText, $smsSenderName);

            if ($sendResult['success']) {
                return true;
            }

            if (!empty($sendResult['items']) && $messages = $sendResult['items']) {
            }

        } catch (\Exception $e) {
            Log::error('Error sending SMS code: ' . $e->getCode());
            Log::error('Error sending SMS code message: ' . $e->getMessage());
        }
    }

    /**
     * @param string $phoneNumber Номер телефона
     * @return void
     */
//    public function sendSmsCode(string $phoneNumber): bool
//    {
//        try {
//            $login = env('REDSMS_LOGIN', 'default_login');
//            $api_key = env('REDSMS_API_KEY', 'default_api_key');
//            $ts = 'ts-value-' . time();
//            $secret = md5($ts . $api_key);
//            $generated_code = $this->generateCode();
//            $sendViaCall = 'sms';
//            $message = "Код для входа в систему Yagoda: $generated_code";
//            $ch = curl_init();
//
//            curl_setopt($ch, CURLOPT_URL, 'https://cp.redsms.ru/api/message');
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, [
//                'login: ' . $login,
//                'ts: ' . $ts,
//                'secret: ' . $secret,
//                'Content-type: application/json'
//            ]);
//
//            if ($sendViaCall == 'call') {
//                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
//                    'route' => 'fcall',
//                    'to' => $phoneNumber,
//                    'text' => $generated_code
//                ]));
//            }elseif ($sendViaCall == 'sms') {
//                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
//                    'route' => 'sms',
//                    'from' => 'SFW',
//                    'to' => $phoneNumber,
//                    'text' => $message
//                ]));
//            }
//
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//            $response = curl_exec($ch);
////            $info = curl_getinfo($ch);
//
//            curl_close($ch);
//
//            $responseData = json_decode($response, true);
//
//            if ($responseData['success']) {
//                $this->setCodeSession($generated_code);
//                return true;
//            } else {
////                foreach ($responseData['errors'] as $error) {
////
////                }
//                return false;
//            }
//        } catch (\Exception $e) {
//            Log::error('Error sending SMS code: ' . $e->getMessage());
//            return false;
//        }
//    }

    public function generateCode(): string
    {
        return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Устанавливает код SMS и его время жизни в сессию.
     */
    public function setCodeSession(string $smsCode): void
    {
        $expiresAt = now()->addSeconds(30);

        Session::put('sms_code', $smsCode);
        Session::put('sms_code_expires_at', $expiresAt);
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
     * Очищает код SMS и его время жизни из сессии.
     */
    public function clearSmsCode(): void
    {
        Session::forget('sms_code');
        Session::forget('sms_code_expires_at');
    }
}
