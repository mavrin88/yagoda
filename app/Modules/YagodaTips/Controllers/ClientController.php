<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Modules\YagodaTips\Models\QrCode;
use App\Http\Controllers\Controller;
use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Models\Order;
use App\Modules\YagodaTips\Models\Setting;
use App\Modules\YagodaTips\Repository\TochkaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Modules\YagodaTips\Services\ShiftService;
use Inertia\Inertia;

class ClientController extends Controller
{

    // Работники в смене
    protected $shiftService;
    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function handleLink($link)
    {
        $qrCode = $this->getQrCodeByLink($link);

        if ($qrCode === null) {
            return redirect('/');
        }

        if ($qrCode->orders->isEmpty()) {
            return redirect('/');
        }

        $order = $qrCode->orders->first();

        $group = $order->group;

        $group->logo_path = $group->logo_path ? URL::route('image', ['path' => $group->logo_path]) : null;
        $group->photo_path = $group->photo_path ? URL::route('image', ['path' => $group->photo_path]) : null;

        $settings = [];

        $settingsAll = Setting::get();

        if ($settingsAll) {
            foreach($settingsAll as $setting) {
                $settings[$setting->key] = $setting->value;
            }
        }

        $activeMasters = $this->shiftService->getAllShifts();

        $data = [];

        if ($activeMasters && $activeMasters['masters']) {
            $data['masters'] = $activeMasters['masters'];
        } else {
            $data['masters'] = $this->shiftService->getAllMasters();
        }

        return Inertia::render('YagodaTips/Client/Masters/Index', compact('data','group', 'settings'));
    }

    private function getQrCodeByLink($link)
    {
        return QrCode::where(function($query) use ($link) {
            $query->where('link', 'https://yagoda-pay.ru/order/' . $link)
                ->orWhere('link', 'https://yagoda.team/order/' . $link);
        })->first();
    }

    public function createOrder(Request $request)
    {
        $order = Order::create([
            'group_id' => $request->groupId,
            'status' => 'draft',
            'full_amount' => 0,
            'tips' => 0,
            'qr_code_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'orderId' => $order->id,
        ]);
    }

    public function makePayment(Request $request, TochkaRepository $repository)
    {
        return $repository->createOrder($request);
    }

    public function tipsLanding() {
        return Inertia::render('YagodaTips/Anonymous/Index');
    }
}
