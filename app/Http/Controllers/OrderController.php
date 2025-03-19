<?php

namespace App\Http\Controllers;

use App\Events\CommentUpdated;
use App\Helpers\AccessHelper;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Bill;
use App\Models\Category;
use App\Models\Client;
use App\Models\MasterShift;
use App\Models\Order;
use App\Models\OrderStatistics;
use App\Models\OrderStatus;
use App\Models\Organization;
use App\Models\QrCode;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\OrderService;
use App\Services\VbrrService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\URL;
use Pusher\Pusher;

class OrderController extends Controller
{
    public VbrrService $vbrService;
    public function __construct(VbrrService $vbrService)
    {
        $this->vbrService = $vbrService;
    }


    /**
     * При правке исправлять и public function filter!
     *
     * @param $filter
     *
     * @return \Inertia\Response
     */
    public function index($filter = null): Response
    {
        if (AccessHelper::hasAccessToOrganization()) {
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
            // Средние чаевые, процент
            $mediumTips = 0;
            // Сумма заказов с чаем
            $todayOrdersWithTipsTotal = 0;

            $orders = Order::with('organization', 'status', 'paymentMethod', 'orderParticipants', 'orderItems')
                ->where('organization_id', Session::get('selected_organization_id'))
                ->where('status', '!=', 'draft')
                ->whereDate('created_at', $dateFilter)
                ->orderBy('created_at', 'desc')
                ->get();

            //        dd($orders->count());

            foreach ($orders as $order) {
                $hasNullUserId = $order->orderParticipants->contains(function ($participant) {
                    return is_null($participant->user_id);
                });
                if ($hasNullUserId) {
                    $masterName = '';
                }
                else {
                    if (isset($order->orderParticipants->first()->user_id)) {
                        $user = User::find($order->orderParticipants->first()->user_id);

                        try {
                            if (empty($user->encrypted_first_name)) {
                                $masterName = '';
                            } else {
                                $masterName = Crypt::decryptString($user->encrypted_first_name);
                            }
                        } catch (DecryptException $e) {
                            $masterName = '';
                        }


                        $masterName = $masterName;
                    }
                }

                $totalAmount = $order->total_amount;


                $data['orders'][] = [
                    'id' => $order->id,
                    'price' => $totalAmount,
                    'master' => $masterName,
                    'tips' => $order->tips ? number_format($order->tips, 0, ',', ' ') : 0,
                    'status' => $this->getStatus($order),
                    'items' => $order->orderItems,
                    'is_pay_order' => $order->is_pay_order,
                    'comment' => $order->comment,
                ];


                if ($order->status == OrderStatus::STATUS_COMPLETED && $order->created_at->isToday()) {
                    $todayOrdersCount++;
                    $todayOrdersTotal += $totalAmount;

                    if ($order->tips > 0) {
                        $todayOrdersWithTipsCount++;
                        $todayOrdersWithTipsTotal += $totalAmount;
                        $totalTips += $order->tips;
                    }
                }
            }

            if ($todayOrdersCount > 0) {
                $todayOrdersWithTipsPercentage = round(($todayOrdersWithTipsCount / $todayOrdersCount) * 100, 2);
            }

            // todo: кэш?
            $role = Role::where('slug', 'master')->first();
            $masterCount = MasterShift::where('organization_id', Session::get('selected_organization_id'))
                ->where('role_id', $role->id)
                ->count();
            $data['masterCount'] = $masterCount;

            $data['todayOrdersWithTipsPercentage'] = $todayOrdersWithTipsPercentage;
            $data['totalTips'] = $totalTips;
            $data['todayOrdersCount'] = $todayOrdersCount;
            $data['todayOrdersWithTipsCount'] = $todayOrdersWithTipsCount;
            $data['todayOrdersTotalSum'] = number_format($todayOrdersTotal, 0, ',', ' ');
            $data['date'] = Carbon::now()->format('d.m.Y');

            // Средний % чаевых от суммы заказа
            if ($todayOrdersTotal > 0 && $todayOrdersWithTipsTotal > 0) {
                $mediumTips = round($totalTips * 100 / $todayOrdersWithTipsTotal, 2);
            }
            $data['mediumTips'] = $mediumTips;

            $products = [];

            $selectedOrganizationId = Session::get('selected_organization_id');

            $qrCodes = QrCode::where('organization_id', $selectedOrganizationId)->get();

            $categories = Category::where('organization_id', $selectedOrganizationId)->get();

            $organizationName = null;
            $organization = Organization::where('id', $selectedOrganizationId)->first();
            if ($organization) {
                $organizationName = $organization->full_name;
                //            Log::debug($organizationName);
            }

            //region Список работников в смене
            //todo: не вижу, чтобы masterShift использовался дальше в коде
            $masterShift = MasterShift::where('organization_id', $selectedOrganizationId)
                ->with('user')
                ->get()
                ->map(function ($masterShift) {
                    return $masterShift->user;
                });
            $masters = $this->getUsersWithShifts(User::masters($selectedOrganizationId), $selectedOrganizationId, 4);
            $administrators = $this->getUsersWithShifts(User::administrators($selectedOrganizationId), $selectedOrganizationId, 3);
            $employees = $this->getUsersWithShifts(User::staff($selectedOrganizationId), $selectedOrganizationId, 5);
            //endregion

            foreach ($categories as $category) {
                $category->image = $category->image ? URL::route('image', ['path' => $category->image]) : null;

                $products[$category->id] = $category->products->whereNull('deleted_at')->map(function ($product) {
                    $product->image = $product->image ? URL::route('image', ['path' => $product->image]) : null;
                    return $product;
                });
            }

            $bills = Bill::where('organization_id', $selectedOrganizationId)->get();

            $formattedArray = $bills->map(function ($bills) {
                return (object) [
                    'id' => $bills->id,
                    'label' => $bills->name,
                    'value' => $bills->id,
                ];
            });

            $user = auth()->user();
            $selectBill = $user->bills->where('organization_id', $selectedOrganizationId)->first();

            if (!$selectBill) {
                $selectBill = $bills->first();
            }

            if ($selectBill) {
                $selectBill = (object) [
                    'id' => $selectBill->id,
                    'label' => $selectBill->name,
                    'value' => $selectBill->id,
                ];
            }

            return Inertia::render('Admin/Orders/Index', compact('data', 'categories', 'products', 'qrCodes', 'masterShift', 'masters', 'administrators', 'employees','organizationName', 'selectedOrganizationId', 'formattedArray', 'selectBill'));
        }
        return Inertia::render('Admin/Orders/Index');
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
            $user['first_name'] = $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : null;
            if (isset($user['photo_path'])) {
                $user['photo_path'] = URL::route('image', ['path' => $user['photo_path'], 'w' => 60, 'h' => 60, 'fit' => 'crop']);
            }
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


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // TODO: refactor!!!!!!!!!!!!!
    public function filter(Request $request): \Illuminate\Http\JsonResponse {
        $validated = $request->validate([
            'filter.date' => 'required|date',
        ]);
        if (AccessHelper::hasAccessToOrganization()) {

            $selectedOrganizationId = (int) Session::get('selected_organization_id');

            // Преобразуем входную дату в UTC
            $dateFilter = Carbon::parse($validated['filter']['date'], $request->timezone ?? 'UTC')->utc();

            $data = [];

            $todayOrdersTotal = 0;
            $todayOrdersCount = 0;
            $todayOrdersWithTipsCount = 0;
            $todayOrdersWithTipsPercentage = 0;
            $totalTips = 0;
            // Средние чаевые, процент
            $mediumTips = 0;
            // Сумма заказов с чаем
            $todayOrdersWithTipsTotal = 0;

            $orders = Order::with('organization', 'status', 'paymentMethod', 'orderParticipants', 'orderItems')
                ->where('organization_id', $selectedOrganizationId)
                ->where('status', '!=', 'draft')
                ->whereDate('created_at', $dateFilter)
                ->orderBy('created_at', 'desc')
                ->get();

            //        dd($orders->count());

            foreach ($orders as $order) {
                $hasNullUserId = $order->orderParticipants->contains(function ($participant) {
                    return is_null($participant->user_id);
                });
                if ($hasNullUserId) {
                    $masterName = '';
                }
                else {
                    if (isset($order->orderParticipants->first()->user_id)) {
                        $user = User::find($order->orderParticipants->first()->user_id);
                        try {
                            if (empty($user->encrypted_first_name)) {
                                $masterName = '';
                            } else {
                                $masterName = Crypt::decryptString($user->encrypted_first_name);
                            }
                        } catch (DecryptException $e) {
                            $masterName = '';
                        }
                        $masterName = $masterName;
                    }
                }

                $totalAmount = $order->total_amount;

                $data['orders'][] = [
                    'id' => $order->id,
                    'price' => $totalAmount,
                    'master' => $masterName,
                    'tips' => $order->tips ? number_format($order->tips, 0, ',', ' ') : 0,
                    'status' => $order->status,
                    'items' => $order->orderItems,
                    'comment' => $order->comment,
                ];

                if ($order->status == OrderStatus::STATUS_COMPLETED) {
                    // Количество заказов
                    $todayOrdersCount++;
                    // Общая сумма заказов
                    $todayOrdersTotal += $totalAmount;

                    if ($order->tips > 0) {
                        $todayOrdersWithTipsCount++;
                        $todayOrdersWithTipsTotal += $totalAmount;
                        $totalTips += $order->tips;
                    }
                }
            }



            if ($todayOrdersCount > 0) {
                $todayOrdersWithTipsPercentage = round(($todayOrdersWithTipsCount / $todayOrdersCount) * 100, 2);
            }

            // todo: кэш?
            $role = Role::where('slug', 'master')->first();
            $masterCount = MasterShift::where('organization_id', Session::get('selected_organization_id'))
                ->where('role_id', $role->id)
                ->count();
            $data['masterCount'] = $masterCount;

            $data['todayOrdersWithTipsPercentage'] = $todayOrdersWithTipsPercentage;
            $data['totalTips'] = $totalTips;
            $data['todayOrdersCount'] = $todayOrdersCount;
            $data['todayOrdersWithTipsCount'] = $todayOrdersWithTipsCount;
            $data['todayOrdersTotalSum'] = number_format($todayOrdersTotal, 0, ',', ' ');
            $data['date'] = Carbon::now()->format('d.m.Y');

            // Средний % чаевых от суммы заказа
            if ($todayOrdersTotal > 0 && $todayOrdersWithTipsTotal > 0) {
                $mediumTips = round($totalTips * 100 / $todayOrdersWithTipsTotal, 2);
            }
            $data['mediumTips'] = $mediumTips;

            return response()->json(
                [
                    'data' => $data
                ]
            );

        }
        return response()->json(
            [
                'data' => []
            ]
        );
    }


    public function store(Request $request)
    {
        $order_link_life_time = Setting::where('key', 'order_link_life_time')->first()->value;

        $qrCode = QrCode::find($request->qrCode['id']);

        $orderLast = $qrCode->orders->last();

        if ($orderLast) {
            if ($qrCode->type === 'static') {

                $qrCodeAttachedAt = Carbon::parse($orderLast->qr_code_attached_at);

                $threeMinutesAgo = now()->subMinutes($order_link_life_time);

                if ($qrCodeAttachedAt > $threeMinutesAgo) {
                    $secondsLeft = $threeMinutesAgo->diffInSeconds($qrCodeAttachedAt);
                    $minutesLeft = floor($secondsLeft / 60);
                    $secondsLeft = $secondsLeft % 60;

                    return response()->json(['error' => "QR-код может быть использован через $minutesLeft минут $secondsLeft секунд."]);
                }
            }

            $orderLast->update(
                [
                    'qr_code_id' => null,
                    'qr_code_attached_at' => null,
                ]
            );
        }


        $order = Order::create([
            'organization_id' => Session::get('selected_organization_id'),
            'total_amount' => $request->totalPrice,
            'status' => 'new',
            'qr_code_id' => $request->qrCode['id'],
            'discount' => (float) $request->discount,
            'qr_code_attached_at' => Carbon::now(),
        ]);

        foreach ($request->products as $product) {
            $order->orderItems()->create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'quantity' => $product['quantity'],
                'product_price' => $product['price'],
            ]);
        }

        foreach ($request->masters as $master) {
            $order->orderParticipants()->create([
                'order_id' => $order->id,
                'user_id' => $master['id'],
            ]);
        }
    }


    /**
     * @param $id
     *
     * @return \Inertia\Response
     */
    public function single($id, QrCodeController $qrCodeController, OrderService $orderService)
    {
        $amountPerMaster = 0;

        $order = Order::find($id);

        $orderStatistic = OrderStatistics::where('order_id', $id)->where('status', 'ok')->first();

        if (!$order->qr_code_id) {
            $order->qr_code_id = $qrCodeController->generateHideQrCode()->id;
            $order->save();
        }

        if ($order) {
            if ($order->qrCode->type === 'static') {
                $order->qr_code_id = $qrCodeController->generateHideQrCode()->id;
                $order->save();
            }
        }

        $order = Order::with('organization', 'paymentMethod', 'orderParticipants', 'orderItems', 'qrCode', 'user', 'client')
            ->where('id', $id)
            ->where('organization_id', Session::get('selected_organization_id'))
            ->first();

        if ($order) {
            $roleMasterId = Role::where('slug', 'master')->pluck('id')->first();

            $masters = $order->orderParticipants->filter(function ($participant) use ($roleMasterId) {
                return $participant->role_id === $roleMasterId;
            });

            $order->participantsArray = $masters->map(function ($participant) {
                $user = User::find($participant->user_id);

                try {
                    if (empty($user->encrypted_first_name)) {
                        $masterName = '';
                    } else {
                        $masterName = Crypt::decryptString($user->encrypted_first_name);
                    }
                } catch (DecryptException $e) {
                    $masterName = '';
                }

                return [
                    'id' => $user->id,
                    'first_name' => $masterName,
                    'last_name' => $user->last_name,
                    'photo_path' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 40, 'h' => 40, 'fit' => 'crop']) : null,
                    'distributed_amount' => $participant->tips ?? 0
                ];
            });

            $order->total_product_price = $order->orderItems->sum('product_price');
        }

        return response()->json(
            [
                'order' => $order,
                'orderStatistic' => $orderService->calculateOrderStatistics($id),
            ]
        );


        return Inertia::render('Admin/Orders/ShowOrder/Index', compact('order'));
    }

    public function checkIsOpenOrder(Request $request)
    {
        $isOpen = Order::where('qr_code_id', $request->generatedQrCode['id'])
            ->where('is_open', true)
            ->exists();

        if ($isOpen) {
            $this->clearStaticQrCode($request->generatedQrCode['id']);
        }

        return response()->json(['status' => $isOpen]);
    }

    public function calculateDiscount(Request $request)
    {
        $products = [];

        foreach ($request->products as $product) {
            $product['discountedPrice'] = $this->discountedPrice($product['price'], $request->discount);
            $products[] = $product;
        }

        $totalSum = collect($products)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $totalSumDiscount = collect($products)->sum(function ($item) {
            return $item['discountedPrice'];
        });

        $sumPerDiscount = $totalSum - $totalSumDiscount;

        return response()->json(
            [
                'products' => $products,
                'totalSum' => $totalSum,
                'totalSumDiscount' => $totalSumDiscount,
                'sumPerDiscount' => $sumPerDiscount,
            ]
        );
    }

    public function findStaticQrCode($qrCode)
    {
        $qrCode = QrCode::find($qrCode);

        $orderLast = $qrCode->orders->last();

        if ($orderLast && $qrCode->type === 'static') {
            return $orderLast;
        }

        return false;
    }

    public function clearStaticQrCode($qrCode): void
    {
        $orderLast = $this->findStaticQrCode($qrCode);

        if ($orderLast) {

            $orderLast->update(
                [
                    'qr_code_id' => null,
                    'qr_code_attached_at' => null,
                ]
            );
        }
    }

    public function createDraftOrder(Request $request)
    {
        $order = Order::create([
            'organization_id' => $request->organizationId,
            'total_amount' => 0,
            'status' => 'draft',
            'qr_code_id' => null,
            'discount' => 0,
            'qr_code_attached_at' => null,
        ]);

        return response()->json(
            [
                'order' => $order
            ]
        );
    }

    public function checkPhoneForNewClient(Request $request)
    {
        if (!isset($request->dopOrderData['newClientData']['phone'])) {
            return null;
        }

        $phone = $request->dopOrderData['newClientData']['phone'];

        if (strlen($phone) !== 18) {
            return null;
        }

        $client = Client::updateOrCreate(
            ['phone' => $phone],
            [
                'name' => $request->dopOrderData['newClientData']['name'] ?? null,
                'organization_id' => $request->dopOrderData['newClientData']['organization_id'] ?? null,
                'discount' => $request->dopOrderData['newClientData']['discount'] ?? null,
            ]
        );

        return $client;
    }

    public function saveOrder(Request $request)
    {
//        dd($request->all());
        // Проверка телефона клиента
        $client = $this->checkPhoneForNewClient($request);

        // Обработка QR-кода
        $this->handleQrCode($request);

        // Находим или создаём заказ
        $order = Order::find($request->draftOrder['order']['id']);

        // Обновляем позиции заказа
        $totalOrderPrice = $this->updateOrderItems($order, $request->products, $request->discount);

        // Обновляем данные заказа
        $this->updateOrder($order, $request, $client, $totalOrderPrice);

        // Удаляем старых участников заказа
        $order->orderParticipants()->delete();

        // Добавляем участников заказа (мастера, администратор, сотрудники)
        $this->addOrderParticipants($order, $request);
    }

    /**
     * Обработка QR-кода.
     */
    protected function handleQrCode(Request $request)
    {
        $qrCode = QrCode::find($request->qrCode['id']);
        $orderLast = $qrCode->orders->last();

        if ($orderLast) {
            $orderLast->update([
                'qr_code_id' => null,
                'qr_code_attached_at' => null,
            ]);
        }
    }

    /**
     * Обновляет позиции заказа.
     */
    protected function updateOrderItems(Order $order, array $products, $discount): float
    {
        $totalOrderPrice = 0;

        $order->orderItems()->delete();

        foreach ($products as $product) {
            $billId = $this->getBillIdForProduct($product);

//            $totalPrice = $product['price'] * $product['quantity'];
//            $discountAmount = $totalPrice * ((float)$discount / 100);
//            $totalAmount = $totalPrice - $discountAmount;
//            $roundedTotalAmount = floor($totalAmount);
            $totalOrderPrice += $this->discountedPrice($product['price'], $discount, $product['quantity']);

            $order->orderItems()->create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'bill_id' => $billId,
                'product_name' => $product['name'],
                'quantity' => $product['quantity'],
                'product_price' => $product['price'],
                'discounted_total' => $this->discountedPrice($product['price'], $discount, $product['quantity']),
            ]);
        }

        return $totalOrderPrice;
    }

    /**
     * Получает bill_id для продукта на основе категории.
     */
    protected function getBillIdForProduct(array $product): int
    {
        $category = Category::find($product['category_id']);

        if ($category->hide) {
            $user = auth()->user();
            $existingBill = $user->bills()
                ->wherePivot('organization_id', Session::get('selected_organization_id'))
                ->first();

            if (!$existingBill) {
                $existingBill = Bill::where('organization_id', Session::get('selected_organization_id'))->first();
            }

            return $existingBill->id;
        }

        return $category->bill_id;
    }

    /**
     * Обновляет данные заказа.
     */
    protected function updateOrder(Order $order, Request $request, $client, float $totalOrderPrice)
    {
        $order->update([
//            'total_amount' => $request->dopOrderData['totalSummFromDiscount'],
            'total_amount' => $totalOrderPrice,
            'status' => 'new',
            'is_arbitrary_amount' => $request->arbitraryAmount,
            'qr_code_id' => $request->qrCode['id'],
            'discount' => (float) $request->discount,
            'qr_code_attached_at' => Carbon::now(),
            'user_id' => Auth::user()->id,
            'client_id' => $client->id ?? null,
        ]);
    }

    /**
     * Добавляет участников заказа.
     */
    protected function addOrderParticipants(Order $order, Request $request)
    {
        $roleMaster = Role::where('slug', 'master')->first();
        $roleAdmin = Role::where('slug', 'admin')->first();
        $roleEmployee = Role::where('slug', 'employee')->first();

        foreach ($request->masters as $master) {
            $order->orderParticipants()->create([
                'order_id' => $order->id,
                'user_id' => $master['id'],
                'role_id' => $roleMaster->id,
            ]);
        }

        $order->orderParticipants()->create([
            'order_id' => $order->id,
            'user_id' => Auth::user()->id,
            'role_id' => $roleAdmin->id,
        ]);

        $this->addEmployeeParticipants($order, $roleEmployee);
    }

    /**
     * Добавляет участников заказа с ролью сотрудника.
     */
    protected function addEmployeeParticipants(Order $order, Role $roleEmployee)
    {
        $orderShifts = MasterShift::where('organization_id', $order->organization_id)
            ->where('role_id', $roleEmployee->id)
            ->get();

        foreach ($orderShifts as $orderShift) {
            $order->orderParticipants()->create([
                'order_id' => $order->id,
                'user_id' => $orderShift->user_id,
                'role_id' => $roleEmployee->id,
            ]);
        }
    }

//    public function saveOrder(Request $request)
//    {
//        $client = $this->checkPhoneForNewClient($request);
//
//        $qrCode = QrCode::find($request->qrCode['id']);
//
//        $orderLast = $qrCode->orders->last();
//
//        if ($orderLast) {
//            $orderLast->update(
//                [
//                    'qr_code_id' => null,
//                    'qr_code_attached_at' => null,
//                ]
//            );
//        }
//
//        $order = Order::find($request->draftOrder['order']['id']);
//
//        $totalOrderPrice = 0;
//
//        $order->orderItems()->delete();
//
//        foreach ($request->products as $product) {
//            $category = Category::find($product['category_id']);
//            if ($category->hide) {
//                $user = auth()->user();
//                $existingBill = $user->bills()->wherePivot('user_id', $user->id)
//                    ->wherePivot('organization_id', Session::get('selected_organization_id'))
//                    ->first();
//
//                if (!$existingBill) {
//                    $existingBill = Bill::where('organization_id', Session::get('selected_organization_id'))->first();
//                }
//                $billId = $existingBill->id;
//            }else {
//                $billId = $category->bill_id;
//            }
//
//            $totalPrice = $product['price'] * $product['quantity'];
//            $discountAmount = $totalPrice * ((float) $request->discount / 100);
//            $totalAmount = $totalPrice - $discountAmount;
//            $roundedTotalAmount = floor($totalAmount);
//            $totalOrderPrice += $roundedTotalAmount;
//
//            $order->orderItems()->create([
//                'order_id' => $order->id,
//                'product_id' => $product['id'],
//                'bill_id' => $billId,
//                'product_name' => $product['name'],
//                'quantity' => $product['quantity'],
//                'product_price' => $product['price'],
//                'discounted_total' => $this->discountedPrice($product['price'], $request->discount, $product['quantity']),
//            ]);
//        }
//
//        $order->update([
//            'total_amount' => $request->dopOrderData['totalSummFromDiscount'],
//            'status' => 'new',
//            'is_arbitrary_amount' => $request->arbitraryAmount,
//            'qr_code_id' => $request->qrCode['id'],
//            'discount' => (float) $request->discount,
////            'discount_summ' => $request->dopOrderData['calculateDiscountSum'],
//            'qr_code_attached_at' => Carbon::now(),
//            'user_id' => Auth::user()->id,
//            'client_id' => $client->id ?? null,
//        ]);
//
//        $order->orderParticipants()->delete();
//
//        $roleMaster = Role::where('slug', 'master')->first();
//        $roleAdmin = Role::where('slug', 'admin')->first();
//
//        foreach ($request->masters as $master) {
//            if(empty($request->masters )){
//                $masterId = null;
//            }else {
//                $masterId = $master['id'];
//            }
//            $order->orderParticipants()->create([
//                'order_id' => $order->id,
//                'user_id' => $masterId,
//                'role_id' => $roleMaster->id,
//            ]);
//        }
//
//        $order->orderParticipants()->create([
//            'order_id' => $order->id,
//            'user_id' => Auth::user()->id,
//            'role_id' => $roleAdmin->id,
//        ]);
//
//        $roleEmployee = Role::where('slug', 'employee')->first();
//
//        $orderShifts = MasterShift::where('organization_id', $order->organization_id)
//            ->where('role_id', $roleEmployee->id)
//                ->get();
//
//        if ($orderShifts->count() > 0) {
//
//            foreach ($orderShifts as $orderShift) {
//                if (empty($request->masters)) {
//                    $masterId = null;
//                }else {
//                    $masterId = $orderShift->user_id;
//                }
//                $order->orderParticipants()->create([
//                    'order_id' => $order->id,
//                    'user_id' => $masterId,
//                    'role_id' => $roleEmployee->id,
//                ]);
//            }
//        }
//    }

    public function editOrder(Request $request)
    {
        $client = $this->checkPhoneForNewClient($request);

        $qrCode = QrCode::find($request->qrCode['id']);

        $orderLast = $qrCode->orders->last();

        if ($orderLast) {
            $orderLast->update(
                [
                    'qr_code_id' => null,
                    'qr_code_attached_at' => null,
                ]
            );
        }

        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $totalOrderPrice = 0;

        foreach ($request->products as $product) {
            $category = Category::find($product['category_id']);
            if ($category->hide) {
                $user = auth()->user();
                $existingBill = $user->bills()->wherePivot('user_id', $user->id)
                    ->wherePivot('organization_id', Session::get('selected_organization_id'))
                    ->first();

                if (!$existingBill) {
                    $existingBill = Bill::where('organization_id', Session::get('selected_organization_id'))->first();
                }
                $billId = $existingBill->id;
            }else {
                $billId = $category->bill_id;
            }

            $totalPrice = $product['price'] * $product['quantity'];
            $discountAmount = $totalPrice * ((float) $request->discount / 100);
            $totalAmount = $totalPrice - $discountAmount;
            $roundedTotalAmount = floor($totalAmount);
            $totalOrderPrice += $roundedTotalAmount;

            $order->orderItems()->updateOrCreate(
                ['order_id' => $request->order_id, 'product_id' => $product['id']],
                [
                    'bill_id' => $billId,
                    'product_name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'product_price' => $product['price'],
                    'discounted_total' => $this->discountedPrice($product['price'], $request->discount, $product['quantity']),
                ]
            );
        }

        $order->update([
            'total_amount' => $request->dopOrderData['totalSummFromDiscount'],
            'status' => 'new',
            'is_arbitrary_amount' => $request->arbitraryAmount,
            'qr_code_id' => $request->qrCode['id'],
            'discount' => (float) $request->discount,
//            'discount_summ' => $request->dopOrderData['calculateDiscountSum'],
            'qr_code_attached_at' => Carbon::now(),
            'user_id' => Auth::user()->id,
            'client_id' => $client->id ?? null,
        ]);

        $order->orderParticipants()->delete();

        $roleMaster = Role::where('slug', 'master')->first();
        $roleAdmin = Role::where('slug', 'admin')->first();

        foreach ($request->masters as $master) {
            if(empty($request->masters )){
                $masterId = null;
            }else {
                $masterId = $master['id'];
            }
            $order->orderParticipants()->create([
                'order_id' => $order->id,
                'user_id' => $masterId,
                'role_id' => $roleMaster->id,
            ]);
        }

        $order->orderParticipants()->create([
            'order_id' => $order->id,
            'user_id' => Auth::user()->id,
            'role_id' => $roleAdmin->id,
        ]);

        $roleEmployee = Role::where('slug', 'employee')->first();

        $orderShifts = MasterShift::where('organization_id', $order->organization_id)
            ->where('role_id', $roleEmployee->id)
            ->get();

        if ($orderShifts->count() > 0) {

            foreach ($orderShifts as $orderShift) {
                if (empty($request->masters)) {
                    $masterId = null;
                }else {
                    $masterId = $orderShift->user_id;
                }
                $order->orderParticipants()->create([
                    'order_id' => $order->id,
                    'user_id' => $masterId,
                    'role_id' => $roleEmployee->id,
                ]);
            }
        }
    }

    public function discountedPrice(float $price, $discount, int $quantity = 1): float
    {
        $discount = $discount ?? 0;

        $discount = max(0, min(100, $discount));

        $priceAfterDiscount = $price * ((100 - $discount) / 100);

        return floor($priceAfterDiscount * $quantity * 100) / 100;
    }


    public function saveQrCodeOrder(Request $request)
    {
        $orderId = $request->order['id'];
        $qrCodeId = $request->order['qr_code_id'];

        Order::where('qr_code_id', $qrCodeId)->update(['qr_code_id' => null]);

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->qr_code_id = $qrCodeId;
        $order->save();

        return response()->json(['message' => 'QR code ID saved successfully.']);
    }


    public function isQrCodeAttachedRecently($qrCode, $orderLinkLifeTime)
    {
        $orderLast = $qrCode->orders->last();

        if ($orderLast) {
            if ($qrCode->type === 'static') {
                $qrCodeAttachedAt = Carbon::parse($orderLast->qr_code_attached_at);
                $threeMinutesAgo = now()->subSecond($orderLinkLifeTime);

                if ($qrCodeAttachedAt > $threeMinutesAgo) {
                    return [
                        'error' => true,
                        'timeLeft' => $threeMinutesAgo->diffInSeconds($qrCodeAttachedAt),
                    ];
                }
            }
        }

        return ['error' => false];
    }

    public function generateQrCodeErrorResponse($secondsLeft)
    {
        $minutesLeft = floor($secondsLeft / 60);
        $secondsLeft = $secondsLeft % 60;

        return response()->json(
            [
                'status' => false,
                'error' => "QR-код может быть использован через $minutesLeft минут $secondsLeft секунд."
            ]
        );
    }


    public function deleteOrder(Request $request)
    {
        try {
            $order = Order::with(['orderItems', 'orderParticipants'])->find($request->orderId);
            if ($order) {
                $order->orderStatistics()->delete();
                $order->orderItems()->delete();
                $order->orderParticipants()->delete();
                $order->delete();
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Не удалось удалить заказ: ' . $e->getMessage()], 400);
        }
    }

    public function checkQrCodeStatus(Request $request)
    {
        $order_link_life_time = Setting::where('key', 'order_link_life_time')->first()->value;

        $qrCode = QrCode::find($request->qrCode['id']);

        $qrCodeCheck = $this->isQrCodeAttachedRecently($qrCode, $order_link_life_time);

        if ($qrCodeCheck['error']) {
            return $this->generateQrCodeErrorResponse($qrCodeCheck['timeLeft']);
        }

        return response()->json(
            [
                'status' => true,
                'error' => "QR-код может быть использован"
            ]
        );
    }

    public function sendPayTips()
    {
        $user = Auth::user();

        return $this->vbrService->addCardTips($user->id);
    }


    /**
     * Обновление комментария к заказу
     *
     * @param $orderId
     * @param $comment
     *
     * @return void
     */
    private function pusherSendOrderComment($orderId, $comment): void
    {
//        $options = [
//            'cluster' => 'eu',
//            'useTLS' => true
//        ];
//
//        $pusher = new Pusher(
//            config('pusher.app_key'),
//            config('pusher.app_secret'),
//            config('pusher.app_id'),
//            $options
//        );
//
//        $data = [
//            'orderId' => $orderId,
//            'comment' => $comment
//        ];
//
//        $pusher->trigger('my-channel', 'comment-update', $data);

        $data = [
            'orderId' => $orderId,
            'comment' => $comment
        ];

        broadcast(new CommentUpdated($data));

    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $orderId
     *
     * @return \App\Http\Responses\ErrorResponse|\App\Http\Responses\SuccessResponse
     */
    public function orderCommentPost(Request $request, $orderId): ErrorResponse|SuccessResponse {

        if (empty($orderId)) {
            return new ErrorResponse('Идентификатор заказа не указан');
        }

        if (!AccessHelper::hasAccessToOrganization()) {
            return new ErrorResponse('Доступ запрещен');
        }

        $orderId = (int) $orderId;
        $order = Order::find($orderId);

        if (!$order) {
            return new ErrorResponse('Заказ не найден');
        }

        $order->comment = $request->comment;
        $order->save();

        $this->pusherSendOrderComment($orderId, $request->comment);

        return new SuccessResponse('Комментарий успешно добавлен к заказу');
    }


    /**
     * @param $orderId
     *
     * @return \App\Http\Responses\ErrorResponse|\App\Http\Responses\SuccessResponse
     */
    public function orderCommentGet($orderId): ErrorResponse|SuccessResponse {
        if (!empty($orderId)) {
            if (AccessHelper::hasAccessToOrganization()) {
                $orderId = (int) $orderId;
                $order = Order::find($orderId);
                if ($order) {
                    $comment = $order->comment ?? '';
                    return new SuccessResponse($comment);
                }
                return new ErrorResponse('Нет заказа с этим id');
            }
            return new ErrorResponse('Нет доступа');
        }
        return new ErrorResponse('Нет id заказа.');
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Http\Responses\ErrorResponse|\App\Http\Responses\SuccessResponse
     */
    public function orderGeoSave(Request $request): ErrorResponse|SuccessResponse {
        $request->validate([
            'order_id'  => 'required|integer',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $orderId = (int) $request->order_id;
        if (!empty($orderId)) {
            $order = Order::find($orderId);
            if ($order) {
                Order::where('id', $orderId)->update([
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                ]);

                return new SuccessResponse($order);
            }
        }
        return new ErrorResponse('Что-то пошло не так.');
    }
}
