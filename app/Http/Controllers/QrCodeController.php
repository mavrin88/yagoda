<?php

namespace App\Http\Controllers;

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
        $qr_codes = QrCode::where('organization_id', Session::get('selected_organization_id'))->get();

        return Inertia::render('SuperAdmin/QrCodes/Index', compact('qr_codes'));
    }

    public function store(Request $request)
    {
        QrCode::create([
            'name' => $request['name'],
            'link' => $request['link'][0],
            'organization_id' => Session::get('selected_organization_id'),
            'type' => 'static',
        ]);

        return redirect()->route('super_admin.qr_codes');
    }

    public function destroy(QrCode $qrCode)
    {
        Order::where('qr_code_id', $qrCode->id)->update(['qr_code_id' => null]);

        $qrCode->delete();

        return redirect()->route('super_admin.qr_codes');
    }

    public function checkUniqueness(Request $request)
    {
        $link = $request->input('link');
        $isUnique = QrCode::where('link', $link)->doesntExist();

        return response()->json(['unique' => $isUnique]);
    }

    public function generateHideQrCode()
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
