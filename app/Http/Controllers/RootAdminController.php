<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MasterShift;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Organization;
use App\Models\QrCode;
use App\Models\Role;
use App\Models\Setting;
use App\Models\UnidentifiedPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class RootAdminController extends Controller
{
    public function index()
    {
        return Inertia::render('RootAdmin/Index');
    }
    public function settings()
    {
        $settings = Setting::all();

        $formattedSettings = [];

        foreach ($settings as $setting) {
            $formattedSettings[$setting->key] = $setting->value;
        }

        return Inertia::render('RootAdmin/Settings/Index', compact('formattedSettings'));
    }
    public function orders($filter = null): Response
    {
        $dateFilter = Carbon::today();

        if (isset($filter['date']) && $filter['date'] !== null) {
            $dateFilter = Carbon::parse($filter['date']);
        }

        $data = [];

        $todayOrdersTotal = 0;
        $todayOrdersCount = 0;
        $todayOrdersWithTipsCount = 0;
        $todayOrdersWithTipsPercentage = 0;
        $totalTips = 0;

        $orders = Order::with('organization', 'status', 'paymentMethod', 'orderParticipants', 'orderItems')
            ->where('organization_id', Session::get('selected_organization_id'))
            ->where('status', '!=', 'draft')
            ->whereDate('created_at', $dateFilter)
            ->orderBy('created_at', 'desc')
            ->get();

//        dd($orders->count());

        foreach ($orders as $order) {
            $totalPrice = 0;

            foreach ($order->orderItems as $orderItem) {
                $totalPrice += $orderItem->product_price * $orderItem->quantity;
            }

            $discountMultiplier = (100 - $order->discount) / 100; // Преобразуем процент в дробное значение
            $discountedPrice = $totalPrice * $discountMultiplier;

            $master = '';
            if ($firstParticipant = $order->orderParticipants->first()) {
                if ($user = User::find($firstParticipant->user_id)) {
                    $master = $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : '';
                }
            }

            $data['orders'][] = [
                'id' => $order->id,
                'price' => number_format($discountedPrice, 0, ',', ' '),
                'master' => $master,
                'tips' => $order->orderParticipants->first() ? number_format($order->orderParticipants->first()->tips, 0, ',', ' ') : '',
                'status' => $this->getStatus($order),
                'items' => $order->orderItems,
            ];


            if ($order->status == OrderStatus::STATUS_COMPLETED && $order->created_at->isToday()) {
                $todayOrdersCount++;
                $todayOrdersTotal += $totalPrice;

                if ($order->orderParticipants->first() && $order->orderParticipants->first()->tips > 0) {
                    $todayOrdersWithTipsCount++;
                    $totalTips += $order->orderParticipants->first()->tips;
                }
            }
        }

        if ($todayOrdersCount > 0) {
            $todayOrdersWithTipsPercentage = round(($todayOrdersWithTipsCount / $todayOrdersCount) * 100, 2);
        }

        $role = Role::where('slug', 'master')->first();
        $masterCount = MasterShift::where('organization_id', Session::get('selected_organization_id'))
            ->where('role_id', $role->id)
            ->count();


        $data['todayOrdersWithTipsPercentage'] = $todayOrdersWithTipsPercentage;
        $data['totalTips'] = $totalTips;
        $data['todayOrdersCount'] = $todayOrdersCount;
        $data['todayOrdersWithTipsCount'] = $todayOrdersWithTipsCount;
        $data['todayOrdersTotalSum'] = number_format($todayOrdersTotal, 0, ',', ' ');
        $data['date'] = Carbon::now()->format('d.m.Y');
        $data['masterCount'] = $masterCount;

        $products = [];

        $selectedOrganizationId = Session::get('selected_organization_id');

        $qrCodes = QrCode::where('organization_id', $selectedOrganizationId)->get();

        $categories = Category::where('organization_id', $selectedOrganizationId)->get();

        $organizationName = null;
        $organization = Organization::where('id', $selectedOrganizationId)->first();
        if ($organization) {
            $organizationName = $organization->name;
//            Log::debug($organizationName);
        }

        $masterShift = MasterShift::where('organization_id', $selectedOrganizationId)
            ->with('user')
            ->get()
            ->map(function ($masterShift) {
                return $masterShift->user;
            });


        $selectedOrganizationId = Session::get('selected_organization_id');

        $masters = $this->getUsersWithShifts(User::masters($selectedOrganizationId), $selectedOrganizationId, 4);
        $administrators = $this->getUsersWithShifts(User::administrators($selectedOrganizationId), $selectedOrganizationId, 3);
        $employees = $this->getUsersWithShifts(User::staff($selectedOrganizationId), $selectedOrganizationId, 5);


        foreach ($categories as $category) {
            $category->image = $category->image ? URL::route('image', ['path' => $category->image]) : null;

            $products[$category->id] = $category->products->map(function ($product) {
                $product->image = $product->image ? URL::route('image', ['path' => $product->image]) : null;
                return $product;
            });
        }

        return Inertia::render('RootAdmin/Orders/Index', compact('data', 'categories', 'products', 'qrCodes', 'masterShift', 'masters', 'administrators', 'employees','organizationName', 'selectedOrganizationId'));
    }

    private function getUsersWithShifts($users, $organizationId, $roleId)
    {
        $shifts = MasterShift::where('organization_id', $organizationId)
            ->where('role_id', $roleId)
            ->get()
            ->keyBy('user_id')
            ->toArray();

        return $users->map(function ($user) use ($shifts) {
            $user['shift'] = isset($shifts[$user['id']]);
            return $user;
        });
    }

    private function getStatus($order)
    {
        if ($order->status === 'new') {
            return 'cancel';
        }
        return $order->status;
    }

    public function tochkaStatistics()
    {
        $unidentifiedPayments = UnidentifiedPayment::with('deals')
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('RootAdmin/TochkaStatistics/Index', compact('unidentifiedPayments'));
    }

    public function click()
    {
        return Inertia::render('RootAdmin/TochkaStatistics/Click');
    }
}
