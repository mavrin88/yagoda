<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

class QrCodeController extends Controller
{
    public function index()
    {

    }

    public function generateQrCode()
    {
        $random = 'https://yagoda-pay.ru/order/' . Str::random(6) . Str::upper(Str::random(6)) . Str::random(8);

        $qrCode = QrCode::create([
            'name' => $random,
            'link' => $random,
            'organization_id' => null,
            'type' => 'dinamic',
        ]);

        return $qrCode;
    }
}
