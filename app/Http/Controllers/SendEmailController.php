<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\SendEmail;
use Illuminate\Http\Request;

class SendEmailController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function sendEmail(Request $request): void {
        $data = [
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'data' => $request->input('data', [])
        ];
        $template = new SendEmail($data, $data['subject']);

        $email = $data['email'] ?: env('MAIL_FROM_ADDRESS');

        SendEmailJob::dispatch($email, $template);
    }
}
