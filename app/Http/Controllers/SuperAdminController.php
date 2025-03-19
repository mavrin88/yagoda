<?php

namespace App\Http\Controllers;

use App\Helpers\AccessHelper;
use App\Models\ActivityType;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Organization;
use App\Models\OrganizationForm;
use App\Models\OrganizationStatus;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class SuperAdminController extends Controller
{
    public function organization(Request $request)
    {
        $user = User::find($request->user()?->id);

        $activityTypes = ActivityType::all();
        $organizationForms = OrganizationForm::all();

        $formattedArray = $activityTypes->map(function ($activityType) {
            return ['id' => $activityType->id, 'label' => $activityType->name, 'value' => $activityType->id];
        });

        $formattedArrayOrganizationForms = $organizationForms->map(function ($organizationForm) {
            return ['id' => $organizationForm->id, 'label' => $organizationForm->name, 'value' => $organizationForm->id];
        });

        $selectedOrganizationId = Session::get('selected_organization_id');

        $organization = Organization::find($selectedOrganizationId);

        $selectActivityType = ActivityType::find($organization->activity_type_id);

        $selectActivityType = (object)[
            'id' => $selectActivityType->id,
            'label' => $selectActivityType->name,
            'value' => $selectActivityType->id,
        ];

        $selectOrganizationForm = OrganizationForm::find($organization->form_id);

        $selectOrganizationForm = (object)[
            'id' => $selectOrganizationForm->id,
            'label' => $selectOrganizationForm->name,
            'value' => $selectOrganizationForm->id,
        ];

        $organization->acquiring_fee = Setting::where('key', 'acquiring_fee')->first()->value;
        $organization->backup_card = !empty($organization->backup_card) ? Crypt::decryptString($organization->backup_card) : null;

        $organization->logo_path = $organization->logo_path ? URL::route('image', ['path' => $organization->logo_path,]) : null;
        $organization->photo_path = $organization->photo_path ? URL::route('image', ['path' => $organization->photo_path,]) : null;

        return Inertia::render('SuperAdmin/Organization/Index',
            compact('organization', 'selectActivityType', 'user', 'formattedArray', 'formattedArrayOrganizationForms', 'selectOrganizationForm')
        );
    }

    public function storeSettings(Request $request)
    {
        //2202 2062 5820 5678
//        dd(!empty($request->organization['backup_card']) ? Crypt::encryptString($request->organization['backup_card']) : null);
        $user = User::find($request->user()?->id);

        $organization = Organization::find($request->organization['id']);
//        $organization->hide = false;
//        $organization->status = OrganizationStatus::STATUS_NEW;
        $organization->name = $request->organization['name'];
        $organization->full_name = $request->organization['full_name'];
        $organization->phone = $request->organization['phone'];
        $organization->contact_name = $request->organization['contact_name'];
        $organization->contact_phone = $request->organization['contact_phone'];
        $organization->inn = $request->organization['inn'];
        $organization->legal_address = $request->organization['legal_address'];
        $organization->registration_number = $request->organization['registration_number'];
        $organization->kpp = $request->organization['kpp'];
        $organization->acquiring_fee = $request->organization['acquiring_fee'];
        $organization->activity_type_id = $request->organization['activity_type_id'];
        $organization->form_id = $request->organization['form_id'];
        $organization->email = $request->organization['email'];
        $organization->backup_card = !empty($request->organization['backup_card']) ? Crypt::encryptString($request->organization['backup_card']) : null;
        $organization->save();

//        $organization->update($request->organization);

        $user->update([
            'phone' => $request->user['phone']
        ]);

        if (($request->organization['saveImageLogo'] ?? false)) {
            $base64String = $request->organization['saveImageLogo'];

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {

                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                $fileName = 'logo_' . time() . '.' . $imageType;

                Storage::put('organizations/' . $fileName, $imageData);

                $publicPath = 'organizations/' . $fileName;

                $organization->update([
                    'logo_path' => $publicPath
                ]);
            }
        }

        if (($request->organization['saveImage'] ?? false)) {
            $base64String = $request->organization['saveImage'];

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {
                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                // Сжимаем изображение с помощью Intervention/Image
                $image = Image::make($imageData);
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode($imageType, 80);

                $fileName = 'img_' . time() . '.' . $imageType;

                Storage::put('organizations/' . $fileName, $image->encoded);

                $publicPath = 'organizations/' . $fileName;

                $organization->update([
                    'photo_path' => $publicPath
                ]);
            }
        }


        if (isset($request->user['password'])) {
            $user->update(['password' => $request->user['password']]);
        }
        if ($user->is_own_organization && $user->first_add_organization) {
            return redirect()->route('super_admin.bills');
        } else {
            return redirect()->route('main', ['is_save_new_organization' => true]);
        }
    }

    public function setTips(Request $request)
    {
        $organization = Organization::find($request->organization['id']);
        $maxTipPercentage = Setting::where('key', 'maximum_tip_amount')->first()->value;

        $hasError = false;
        $errorMessage = '';

        foreach ($request->tips as $item) {
            switch ($item['name']) {
                case 'tips_1':
                    $organization->tips_1 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
                case 'tips_2':
                    $organization->tips_2 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
                case 'tips_3':
                    $organization->tips_3 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
                case 'tips_4':
                    $organization->tips_4 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
            }
        }

        if ($hasError) {
            return response()->json(['error' => true, 'message' => $errorMessage, 'value' => $maxTipPercentage], 200);
        }

        $organization->save();

        return response()->json(['success' => true, 'message' => 'Чаевые успешно сохранены.']);
    }

    private function validateTip($value, &$hasError, &$errorMessage)
    {
        $maxTipPercentage = Setting::where('key', 'maximum_tip_amount')->first()->value;

        if ($value > $maxTipPercentage) {
            $hasError = true;
            $errorMessage = "Максимальное значение чаевых не может превышать {$maxTipPercentage}%.";
            return $maxTipPercentage;
        }
        return $value;
    }

    public function tips(Request $request)
    {
        $toDayDate = Carbon::now()->format('Y-m-d');

        $selectedOrganizationId = Session::get('selected_organization_id');

        $masters = User::masters($selectedOrganizationId);
        $orders = Order::filteredByOrganizationAndStatusAndDate($selectedOrganizationId, $toDayDate)->get();

        $totalTips = $this->totalTips($orders);

        $buyersWithTips = $this->buyersWithTips($orders);

        $totalOrders = $orders->count();

        if ($totalOrders > 0) {
            $percentageWithTips = ($buyersWithTips / $totalOrders) * 100;
        } else {
            $percentageWithTips = 0;
        }

        if ($buyersWithTips > 0) {
            $averageTip = $totalTips / $buyersWithTips;
        } else {
            $averageTip = 0;
        }

        $revenue = $orders->sum('total_amount');

        $mastersCount = $masters->count();

        $mastersData = $masters->map(function ($master) {
            $orderParticipants = $master->orders;

            if ($orderParticipants) {
                $ordersWithTips = $orderParticipants->filter(function ($participant) {
                    return $participant->tips > 0;
                })->count();

                $totalOrderParticipantsForMaster = $orderParticipants->count();

                if ($totalOrderParticipantsForMaster > 0) {
                    $percentageWithTipsForMaster = ($ordersWithTips / $totalOrderParticipantsForMaster) * 100;
                } else {
                    $percentageWithTipsForMaster = 0;
                }

                $totalTipsForMaster = $orderParticipants->sum('tips');
            } else {
                $ordersWithTips = 0;
                $totalOrderParticipantsForMaster = 0;
                $percentageWithTipsForMaster = 0;
                $totalTipsForMaster = 0;
            }

            $masterPhotoUrl = isset($master->photo_path) ? URL::route('image', [
                'path' => $master->photo_path,
                'w' => 60, 'h' => 60, 'fit' => 'crop'
            ]) : null;

            return [
                'first_name' => $master->first_name,
                'last_name' => $master->last_name,
                'ordersWithTips' => $ordersWithTips,
                'percentageWithTips' => round($percentageWithTipsForMaster, 2),
                'averageTip' => $ordersWithTips > 0 ? round($totalTipsForMaster / $ordersWithTips, 2) : 0,
                'totalTips' => $totalTipsForMaster,
                'photo_path' => $masterPhotoUrl,
            ];
        });


        $data = [
            'sumTips' => $totalTips,
            'buyersCount' => $buyersWithTips,
            'percentageWithTips' => round($percentageWithTips, 2),
            'averageTip' => $averageTip,
            'revenue' => number_format($revenue, 0, '', ' '),
            'mastersCount' => $mastersCount,
            'masters' => $mastersData
        ];

        return Inertia::render('SuperAdmin/Tips/Index', compact('data'));
    }

    public function totalTips($orders)
    {
        return $orders->sum(function ($order) {
            return $order->orderParticipants->sum('tips');
        });
    }
    public function buyersWithTips($orders)
    {
        return $orders->filter(function ($order) {
            return $order->orderParticipants->sum('tips') > 0;
        })->count();
    }
    public function filterTipsStatistics(Request $request)
    {
        $startDate = Carbon::parse($request->input('startDate'));
        $endDate = Carbon::parse($request->input('endDate'));
        $selectedOrganizationId = Session::get('selected_organization_id');

        $masters = User::masters($selectedOrganizationId);

        if ($request->input('type') === 'day') {
            $orders = Order::filteredByOrganizationAndStatusAndDate($selectedOrganizationId, $endDate)->get();
        }else {
            $orders = Order::filteredByOrganizationStatusAndDateRange($selectedOrganizationId, $startDate, $endDate)->get();
        }
        $totalTips = $this->totalTips($orders);
        $buyersWithTips = $this->buyersWithTips($orders);
        $totalOrders = $orders->count();

        if ($totalOrders > 0) {
            $percentageWithTips = ($buyersWithTips / $totalOrders) * 100;
        } else {
            $percentageWithTips = 0;
        }

        if ($buyersWithTips > 0) {
            $averageTip = $totalTips / $buyersWithTips;
        } else {
            $averageTip = 0;
        }

        $revenue = $orders->sum('total_amount');

        $mastersCount = $masters->count();

        $mastersData = $masters->map(function ($master) {
            $orderParticipants = $master->orders;

            if ($orderParticipants) {
                $ordersWithTips = $orderParticipants->filter(function ($participant) {
                    return $participant->tips > 0;
                })->count();

                $totalOrderParticipantsForMaster = $orderParticipants->count();

                if ($totalOrderParticipantsForMaster > 0) {
                    $percentageWithTipsForMaster = ($ordersWithTips / $totalOrderParticipantsForMaster) * 100;
                } else {
                    $percentageWithTipsForMaster = 0;
                }

                $totalTipsForMaster = $orderParticipants->sum('tips');
            } else {
                $ordersWithTips = 0;
                $totalOrderParticipantsForMaster = 0;
                $percentageWithTipsForMaster = 0;
                $totalTipsForMaster = 0;
            }

            $masterPhotoUrl = isset($master->photo_path) ? URL::route('image', [
                'path' => $master->photo_path,
                'w' => 60, 'h' => 60, 'fit' => 'crop'
            ]) : null;

            return [
                'first_name' => $master->first_name,
                'last_name' => $master->last_name,
                'ordersWithTips' => $ordersWithTips,
                'percentageWithTips' => round($percentageWithTipsForMaster, 2),
                'averageTip' => $ordersWithTips > 0 ? round($totalTipsForMaster / $ordersWithTips, 2) : 0,
                'totalTips' => $totalTipsForMaster,
                'photo_path' => $masterPhotoUrl,
            ];
        });

        $data = [
            'sumTips' => $totalTips,
            'buyersCount' => $buyersWithTips,
            'percentageWithTips' => round($percentageWithTips),
            'averageTip' => $averageTip,
            'revenue' => number_format($revenue, 0, '', ' '),
            'mastersCount' => $mastersCount,
            'masters' => $mastersData
        ];
        return response()->json($data);
    }

    public function tipDistribution()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $organization = Organization::find($selectedOrganizationId);

        return Inertia::render('SuperAdmin/TipsDistribution/Index', compact('organization'));
    }

    public function saveTipDistribution(Request $request)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $validator = Validator::make($request->all(), [
            'masterPercentage' => 'required|numeric|min:0|max:100',
            'adminPercentage' => 'required|numeric|min:0|max:100',
            'staffPercentage' => 'required|numeric|min:0|max:100',
            'organizationPercentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors()
                ], 422);
        }

        $totalPercentage = $request->masterPercentage +
            $request->adminPercentage +
            $request->staffPercentage +
            $request->organizationPercentage;

        if ($totalPercentage !== 100) {
            return response()->json(
                [
                    'errors' => [
                        'Total percentage must be 100%'
                    ]
                ], 422);
        }

        try {
            DB::transaction(function () use ($selectedOrganizationId, $request) {
                $organization = Organization::findOrFail($selectedOrganizationId);
                $organization->update([
                    'master_percentage' => $request->masterPercentage,
                    'admin_percentage' => $request->adminPercentage,
                    'staff_percentage' => $request->staffPercentage,
                    'organization_percentage' => $request->organizationPercentage
                ]);
            });

            return response()->json(
                [
                    'message' => 'Tip distribution saved successfully'
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => [
                        'An error occurred while saving the tip distribution'
                    ]
                ], 500);
        }
    }

    // todo: объединить с filterOrdersStatistics, тут не нужен отдельный запрос
    public function ordersStatistics(Request $request)
    {
        $equalizationFees = [];
        $orderItemsData = [];
        $toDayDate = Carbon::now()->format('Y-m-d');
        $acquiringFeePercent = Setting::where('key', 'commission_for_using_the_service')->first()->value;
        $myFeePercent = Setting::where('key', 'acquiring_fee')->first()->value;
        $totalEconomy = 0;
        $todayOrdersCount = 0;
        $todayOrdersWithTipsCount = 0;
        $todayOrdersWithTipsPercentage = 0;
        $totalTips = 0;
        $totalProductPrice = 0;

        $selectedOrganizationId = Session::get('selected_organization_id');
        $orders = Order::getCompletedOrdersWithStatisticsAndItemsAndDate($toDayDate, $selectedOrganizationId);

        if ($orders->isEmpty()) {
            $ordersCount = 0;
        } else {
            $ordersCount = $orders->count();
        }

        /** @var number $orderTotalSumWithTips */
        // todo: сделать phpdoc для всех
        $orders->each(function ($order) use (&$orderTotalSumWithTips, &$equalizationFees, &$orderItemsData, &$acquiringFeePercent, &$myFeePercent, &$totalEconomy, &$todayOrdersWithTipsCount, &$todayOrdersWithTipsPercentage, &$totalTips, &$todayOrdersCount, &$totalProductPrice) {
            $todayOrdersCount++;
            if ($order->orderStatistics) {
                $order->orderStatistics->each(function ($orderStat) use (&$equalizationFees) {
                    $equalizationFees[] = $orderStat->service_acquiring_commission;
                });
            }
            if ($order->tips > 0) {
                $totalTips += $order->tips;
                // сумма всех заказов с чаем
                $orderTotalSumWithTips += $order->total_amount;
                $todayOrdersWithTipsCount++;
            }

            if ($order->orderItems) {
                $order->orderItems->each(function ($item) use (&$orderItemsData, &$acquiringFeePercent, &$myFeePercent, &$totalEconomy, &$totalProductPrice) {

                    $acquiringFee = ($item->product_price * $acquiringFeePercent) / 100;
                    $myFee = ($item->product_price * $myFeePercent) / 100;
                    $economy = $acquiringFee - $myFee;
                    $totalEconomy += $economy;
                    $productPriceReal = $item->discounted_total ?: $item->product_price;
                    $totalProductPrice += $productPriceReal;

                    $orderItemsData[] = [
                        'product_id' => $item->product_id,
                        'name' => $item->product_name,
                        'count_items' => $item->quantity,
                        'product_price' => number_format($productPriceReal, 2, '.', ' '),
                    ];
                });
            }

            if (!empty($orderItemsData)) {
                $orderItemsData = collect($orderItemsData)
                    ->groupBy(fn($item) => $item['product_id'] . '-' . $item['name'])
                    ->map(function ($items) {

                        $groupCount = $items->sum('count_items');
                        $groupProductPrice = number_format($items->sum(fn($item) => (float) str_replace(' ', '', $item['product_price'])), 2, '.', ' ');

                        return [
                            'product_id' => $items->first()['product_id'],
                            'name' => $items->first()['name'],
                            'count_items' => $groupCount, // Суммируем количество товаров
                            'product_price' => $groupProductPrice,
                            'total' => number_format($groupCount * $groupProductPrice, 2, '.', ' '),
                        ];
                    })->values()
                    ->toArray();
            }

        });

        $ordersTotalSum = $orders->sum('total_amount');

        if ($todayOrdersCount > 0) {
            $todayOrdersWithTipsPercentage = round(($todayOrdersWithTipsCount / $todayOrdersCount) * 100);
        }
        if ($orderTotalSumWithTips > 0 && $totalTips > 0) {
            $mediumTips = round($totalTips / ($orderTotalSumWithTips / 100), 2);
        } else {
            $mediumTips = 0;
        }
        $totalEqualizationFee = array_sum($equalizationFees);
        $totalEconomy = number_format(round($totalEconomy), 0, '.', ' ');

        $data = [
            // Выручка
            'revenue' => $ordersTotalSum,
            // Комиссия за экваринг Yagoda
            'equalizationFee' => $totalEqualizationFee,
            // Сэкономлено на экваринге
            'savedEqualization' => $totalEconomy,
            // Всего заказов за период
            'totalOrders' => $ordersCount,
            // Данные заказов
            'orders' => $orderItemsData,
            // заказов с чаем
            'todayOrdersWithTipsCount' => $todayOrdersWithTipsCount,
            // % заказов с чаем от всех
            'todayOrdersWithTipsPercentage' => $todayOrdersWithTipsPercentage,
            // средний чай
            'medium_tips' => $mediumTips,
            // итого чаевых
            'totalTips' => $totalTips,
        ];
        return Inertia::render('SuperAdmin/OrdersStatistics/Index', compact('data'));
    }

    public function getReports(): \Inertia\Response
    {
        return Inertia::render('SuperAdmin/Reports/Index');
    }

    public function showMessageTipDistribution(Request $request)
    {
        Organization::where('id', $request->organizationId)->update([
            'show_message' => true
        ]);
    }

    public function validateCardNumber(Request $request)
    {
        $cardNumber = preg_replace('/\D/', '', $request->card_number);

        if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
            return response()->json(
                [
                    'status' => false
                ]
            );
        }

        $sum = 0;
        $nDigits = strlen($cardNumber);
        $parity = $nDigits % 2;

        foreach (str_split($cardNumber) as $i => $digit) {
            $digit = (int)$digit;

            if ($i % 2 === $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        return response()->json(
            [
                'status' => $sum % 10 === 0
            ]
        );
    }

    public function UsefulVideos(): \Inertia\Response
    {
        return Inertia::render('SuperAdmin/Links/UsefulVideos');
    }

    public function AfterRegistration(): \Inertia\Response
    {
        return Inertia::render('SuperAdmin/Links/AfterRegistration');
    }

    public function settings()
    {
        return Inertia::render('SuperAdmin/Settings/Index');
    }

    public function mapLinks()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $organization = Organization::find($selectedOrganizationId);

        return Inertia::render('SuperAdmin/Settings/MapLinks', compact('organization'));
    }

    public function otherSettings()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $organization = Organization::find($selectedOrganizationId);

        return Inertia::render('SuperAdmin/Settings/OtherSettings/Index', compact('organization'));
    }


    public function otherSettingsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selectedOption' => 'required|string',
        ]);

        $errors = NULL;

        $selectedOrganizationId = Session::get('selected_organization_id');

        if ($validator->fails()) {
            $errors = response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors(),
            ], 422);
        }

        $organization = Organization::find($selectedOrganizationId);
        $organization->comp_source = $request->input('selectedOption');
        $organization->save();

        return Inertia::render('SuperAdmin/Settings/OtherSettings/Index', compact('organization', 'errors'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterOrdersStatistics(Request $request): \Illuminate\Http\JsonResponse {

        $selectedOrganizationId = Session::get('selected_organization_id');
        $organization = Organization::find($selectedOrganizationId);

        $validated = $request->validate([
            'startDate' => ['required', 'date', 'before_or_equal:endDate'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
        ]);

        try {
            $startDate = Carbon::parse($validated['startDate']);
            $endDate = Carbon::parse($validated['endDate']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 422);
        }

        $equalizationFees = [];
        $orderItemsData = [];
        $acquiringFeePercent = Setting::where('key', 'commission_for_using_the_service')->first()->value;
        $myFeePercent = Setting::where('key', 'acquiring_fee')->first()->value;
        $totalEconomy = 0;
        $todayOrdersCount = 0;
        $todayOrdersWithTipsCount = 0;
        $todayOrdersWithTipsPercentage = 0;
        $totalTips = 0;
        $totalProductPrice = 0;
        $orderTotalSumWithTips = 0;

        if ($request->input('type') === 'day') {
            $orders = Order::getCompletedOrdersWithStatisticsAndItemsAndDate($endDate, $selectedOrganizationId);
        } else {
            $orders = Order::getCompletedOrdersWithStatisticsAndItemsAndDateRange($startDate, $endDate, $selectedOrganizationId);
        }

        if ($orders->isEmpty()) {
            $ordersCount = 0;
        } else {
            $ordersCount = $orders->count();
        }

        /** @var number $orderTotalSumWithTips */
        // todo: сделать phpdoc для всех
        $orders->each(function ($order) use (&$orderTotalSumWithTips, &$equalizationFees, &$orderItemsData, &$acquiringFeePercent, &$myFeePercent, &$totalEconomy, &$todayOrdersWithTipsCount, &$todayOrdersWithTipsPercentage, &$totalTips, &$todayOrdersCount, &$totalProductPrice) {
            $todayOrdersCount++;
            if ($order->orderStatistics) {
                $order->orderStatistics->each(function ($orderStat) use (&$equalizationFees) {
                    $equalizationFees[] = $orderStat->service_acquiring_commission;
                });
            }
            if ($order->tips > 0) {
                $totalTips += $order->tips;
                // сумма всех заказов с чаем
                $orderTotalSumWithTips += $order->total_amount;
                $todayOrdersWithTipsCount++;
            }

            if ($order->orderItems) {
                $order->orderItems->each(function ($item) use (&$orderItemsData, &$acquiringFeePercent, &$myFeePercent, &$totalEconomy, &$totalProductPrice) {

                    $acquiringFee = ($item->product_price * $acquiringFeePercent) / 100;
                    $myFee = ($item->product_price * $myFeePercent) / 100;
                    $economy = $acquiringFee - $myFee;
                    $totalEconomy += $economy;
                    $productPriceReal = $item->discounted_total ?: $item->product_price;
                    $totalProductPrice += $productPriceReal;

                    $orderItemsData[] = [
                        'product_id' => $item->product_id,
                        'name' => $item->product_name,
                        'count_items' => $item->quantity,
                        'product_price' => number_format($productPriceReal, 2, '.', ' '),
                    ];
                });
            }

            if (!empty($orderItemsData)) {
                $orderItemsData = collect($orderItemsData)
                    ->groupBy(fn($item) => $item['product_id'] . '-' . $item['name'])
                    ->map(function ($items) {
                        $groupCount = $items->sum('count_items');
                        $groupProductPrice = number_format($items->sum(fn($item) => (float) str_replace(' ', '', $item['product_price'])), 2, '.', ' ');

                        return [
                            'product_id' => $items->first()['product_id'],
                            'name' => $items->first()['name'],
                            'count_items' => $groupCount, // Суммируем количество товаров
                            'product_price' => $groupProductPrice,
                            'total' => number_format($groupCount * $groupProductPrice, 2, '.', ' '),
                        ];
                    })->values()
                    ->toArray();
            }

        });

        $ordersTotalSum = $orders->sum('total_amount');

        if ($todayOrdersCount > 0) {
            $todayOrdersWithTipsPercentage = round(($todayOrdersWithTipsCount / $todayOrdersCount) * 100);
        }
        if ($orderTotalSumWithTips > 0 && $totalTips > 0) {
            $mediumTips = round($totalTips / ($orderTotalSumWithTips / 100), 2);
        } else {
            $mediumTips = 0;
        }
        $totalEqualizationFee = array_sum($equalizationFees);
        $totalEconomy = number_format(round($totalEconomy), 0, '.', ' ');

        usort($orderItemsData, function($a, $b) {
            $priceA = (float)str_replace(' ', '', $a['product_price']);
            $priceB = (float)str_replace(' ', '', $b['product_price']);
            return $priceB <=> $priceA;
        });

        $data = [
            // Выручка
            'revenue' => $ordersTotalSum,
            // Комиссия за экваринг Yagoda
            'equalizationFee' => $totalEqualizationFee,
            // Сэкономлено на экваринге
            'savedEqualization' => $totalEconomy,
            // Всего заказов за период
            'totalOrders' => $ordersCount,
            // Данные заказов
            'orders' => $orderItemsData,
            // заказов с чаем
            'todayOrdersWithTipsCount' => $todayOrdersWithTipsCount,
            // % заказов с чаем от всех
            'todayOrdersWithTipsPercentage' => $todayOrdersWithTipsPercentage,
            // средний чай
            'medium_tips' => $mediumTips,
            // итого чаевых
            'totalTips' => $totalTips,
            // итого чаевых
            'brandName' => $organization->full_name,
        ];
        return response()->json($data);
    }

    /**
     * Экспорт отчёта
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportToExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse {
        if (AccessHelper::hasAccessToOrganization()) {
            $data = $request->input('products');
            $name = $request->input('name');

            return Excel::download(new ProductsExport($data), $name . '.xlsx');
        }
        return response()->json(
            [
                'errors' => [
                    'Error'
                ]
            ], 422);
    }
}
