<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Modules\YagodaTips\Services\CoordinatorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TipsStatisticController extends Controller
{
    protected $coordinatorService;

    public function __construct(CoordinatorService $coordinatorService)
    {
        $this->coordinatorService = $coordinatorService;
    }

    public function index(Request $request)
    {
        $request->validate([
            'date' => 'date',
            'employeeId' => 'required|number',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                [
                    'date' => '10 марта 2025г',
                    'time' => '18:51:35',
                    'name' => 'Василий',
                    'totalTips' => 100,
                    'totalTraffic' => 200,
                    'payType' => 'spb',
                ]
            ],
        ]);
    }

    public function tips(Request $request)
    {
        $data = [
            'data' => [
                'translations' => 1,
                'translations_sum' => 1000,
            ],
            'employee' => [
                [
                    'date' => '10 марта 2025г',
                    'time' => '18:51:35',
                    'name' => 'Василий',
                    'totalTips' => 100,
                    'totalTraffic' => 200,
                    'payType' => 'spb',
                ],
                [
                    'date' => '10 марта 2025г',
                    'time' => '18:51:35',
                    'name' => 'Василий',
                    'totalTips' => 100,
                    'totalTraffic' => 200,
                    'payType' => 'spb',
                ],
                [
                    'date' => '10 марта 2025г',
                    'time' => '18:51:35',
                    'name' => 'Василий',
                    'totalTips' => 100,
                    'totalTraffic' => 200,
                    'payType' => 'spb',
                ]
            ]
        ];

        return Inertia::render('YagodaTips/Coordinator/Tips/Index', compact('data'));
    }

    public function tipsOne(Request $request)
    {
        $participants = $this->coordinatorService->getOrderParticipantsByPeriodAndGroup(
            '2025-03-10',
            '2025-03-14',
            1
        );


        $participantsGrouped = $participants
            ->filter(function ($participant) {
                return $participant->user_id === 1 && $participant->order && $participant->order->status === 'ok';
            })
            ->groupBy('user_id')
            ->map(function ($group) {
                $firstParticipant = $group->first();
                $user = $firstParticipant->user;

                $roleSlug = Role::where('id', $firstParticipant->role_id)->value('slug');

                // Собираем все orderStatistics для заказов в группе
                $orderStatistics = $group->pluck('order.orderStatistics')->filter()->flatten();

                // Количество записей в orderStatistics
                $statisticsCount = $orderStatistics->count();

                // Сумма full_amount из всех orderStatistics
                $fullAmountSum = $orderStatistics->sum('full_amount');

                return [
                    'id' => $firstParticipant->user_id,
//                    'tips' => $group->sum('tips'),
//                    'name' => $user ? $user->name : null,
//                    'orders_count' => $group->count(),
//                    'role_slug' => $roleSlug,
//                    'order_statistics' => $firstParticipant->order && $firstParticipant->order->orderStatistics
//                        ? $firstParticipant->order->orderStatistics->toArray()
//                        : null,
                    'statistics_count' => $statisticsCount, // Количество записей orderStatistics
                    'full_amount_sum' => $fullAmountSum,   // Сумма full_amount
                    'payType' => $orderStatistics,
                ];
            })->values();

        dd($participantsGrouped);

        $data = [
            'data' => [
                'translations' => 1,
                'translations_sum' => 1000,
            ],
            'employee' => [
                [
                    'date' => '10 марта 2025г',
                    'time' => '18:51:35',
                    'name' => 'Василий',
                    'totalTips' => 100,
                    'totalTraffic' => 200,
                    'payType' => 'spb',
                ]
            ]
        ];

        return Inertia::render('YagodaTips/Coordinator/Tips/Index', compact('data'));
    }
}

