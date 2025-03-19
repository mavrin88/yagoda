<?php

namespace app\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Services\CoordinatorService;
use App\Modules\YagodaTips\Services\ShiftService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CoordinatorController extends Controller
{
    protected $shiftService;
    protected $coordinatorService;

    public function __construct(ShiftService $shiftService, CoordinatorService $coordinatorService)
    {
        $this->shiftService = $shiftService;
        $this->coordinatorService = $coordinatorService;
    }

    public function index()
    {
        $data = $this->stats(
            Carbon::today()->toDateString(),
            Carbon::today()->toDateString()
        );

        return Inertia::render('YagodaTips/Coordinator/Index', compact('data'));
    }

    public function stats($startDate, $endDate)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');
        $group = Group::find($selectedOrganizationId);

        $totalTips = $this->coordinatorService->getTotalTipsForPaidOrders(
            $startDate,
            $endDate,
            $selectedOrganizationId
        );

        $paidOrdersCount = $this->coordinatorService->getPaidOrdersCount(
            $startDate,
            $endDate,
            $selectedOrganizationId
        );

        $participants = $this->coordinatorService->getOrderParticipantsByPeriodAndGroup(
            $startDate,
            $endDate,
            $selectedOrganizationId
        );

        $participantsGrouped = $participants->groupBy('user_id')->map(function ($group) {
            $firstParticipant = $group->first();
            $user = $firstParticipant->user;

            $roleSlug = Role::where('id', $firstParticipant->role_id)->value('slug');

            return [
                'id' => $firstParticipant->user_id,
                'tips' => $group->sum('tips'),
                'name' => $user ? $user->name : null,
                'orders_count' => $group->count(),
                'role_slug' => $roleSlug,
            ];
        })->values();


        $adminsTipsSum = $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'admin')->sum('tips');
        $mastersTipsSum = $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'master')->sum('tips');
        $employeeTipsSum = $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'employee')->sum('tips');

        $totalTranslationsSum = $adminsTipsSum + $mastersTipsSum + $employeeTipsSum;


        return [
            'role_name' => 'Координатор',
            'group_name' => $group->full_name,
            'people_count' => $this->shiftService->getAllShiftsCount(),
            'translations' => $paidOrdersCount,
            'translations_sum' => $totalTips,
            'total_translations_sum' => $totalTranslationsSum,
            'employees' => [
                'admins' => [
                    'name' => 'АДМИНИСТРАТОРЫ',
                    'total_translations_sum' => $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'admin')->values()->sum('tips'),
                    'employees' => $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'admin')->values(),
                ],
                'masters' => [
                    'name' => 'МАСТЕРА',
                    'total_translations_sum' => $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'master')->values()->sum('tips'),
                    'employees' => $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'master')->values(),
                ],
                'staffs' => [
                    'name' => 'ПЕРСОНАЛ',
                    'total_translations_sum' => $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'employee')->values()->sum('tips'),
                    'employees' => $participantsGrouped->filter(fn($p) => $p['role_slug'] === 'employee')->values(),
                ],
            ]
        ];
    }

    public function getTips(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        $startDate = Carbon::parse($validated['startDate']);
        $endDate = Carbon::parse($validated['endDate']);

        $data = $this->stats($startDate, $endDate);

        return response()->json($data);
    }


    public function tips()
    {
        return Inertia::render('YagodaTips/Coordinator/Tips/Index');
    }

    public function tipsOne(Request $request)
    {
//        dd($request->all());
        return Inertia::render('YagodaTips/Coordinator/Tips/Index');
    }

    public function stands()
    {
        return Inertia::render('YagodaTips/Coordinator/Qr/Stands/Index');
    }
}
