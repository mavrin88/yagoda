<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsConfirmationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsConfirmationController extends Controller
{
    /**
     * Отправить SMS-код на указанный номер телефона.
     *
     * @param Request $request
     * @param SmsConfirmationService $smsConfirmationService
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSmsCode(Request $request, SmsConfirmationService $smsConfirmationService)
    {
        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:18',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Неверный номер телефона.',
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Отправляем SMS-код
        $smsConfirmationService->sendSmsCode($phone);

        return response()->json([
            'message' => 'SMS-код отправлен на ваш телефон.',
            'success' => true
        ]);
    }

    /**
     * Проверить SMS-код.
     *
     * @param Request $request
     * @param SmsConfirmationService $smsConfirmationService
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifySmsCode(Request $request, SmsConfirmationService $smsConfirmationService)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:4|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Неверный код подтверждения.',
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $verified = $smsConfirmationService->verifySmsCode($request->code);

        if (!$verified) {
            return response()->json([
                'message' => 'Неверный SMS-код.',
                'success' => false
            ]);
        }

        return response()->json([
            'message' => 'SMS-код подтвержден',
            'success' => true
        ]);
    }

    public function checkUserExists(Request $request, SmsConfirmationService $smsConfirmationService)
    {
        $phoneNumber = preg_replace('/\D/', '', $request->phone);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            $user = User::create([
                'account_id' => 1,
                'phone' => $request->phone,
                'first_name' => '',
                'last_name' => '',
                'password' => 'secret',
            ]);
        }

        $smsConfirmationService->sendSmsCode($phoneNumber);

        return response()->json([
            'success' => true,
        ]);
    }
}
