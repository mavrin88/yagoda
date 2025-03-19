<?php

namespace app\Modules\YagodaTips\Controllers;

use App\Helpers\AccessHelper;
use App\Http\Controllers\Controller;
use App\Modules\YagodaTips\Helpers\AccessHelperGroups;
use App\Modules\YagodaTips\Models\MasterShift;
use App\Models\Role;
use App\Models\User;
use App\Modules\YagodaTips\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class MasterShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function index()
    {
        $data = $this->shiftService->getAllShifts();
        $administrators = $data['administrators'];
        $masters = $data['masters'];
        $employees = $data['employees'];

        return Inertia::render('YagodaTips/MastersShift/Index', compact('masters', 'administrators', 'employees'));

    }

    private function getUsersWithShifts($users, $organizationId, $roleId)
    {
        $shifts = MasterShift::where('group_id', $organizationId)
            ->where('role_id', $roleId)
            ->get()
            ->keyBy('user_id')
            ->toArray();

        return $users->map(function ($user) use ($shifts) {
            $user['shift'] = isset($shifts[$user['id']]);
            $user['first_name'] = $user->encrypted_first_name? Crypt::decryptString($user->encrypted_first_name): '';
            if (isset($user['photo_path'])) {
                $user['photo_path'] = URL::route('image', ['path' => $user['photo_path'], 'w' => 60, 'h' => 60, 'fit' => 'crop']);
            }
            return $user;
        });
    }

    public function saveMastersShift(Request $request)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        MasterShift::where('group_id', $selectedOrganizationId)->delete();

        $roles = Role::whereIn('slug', ['master', 'admin', 'employee'])->get()->keyBy('slug');

        $shiftsData = [];

        $this->prepareShiftData($request->masters, 'master', $selectedOrganizationId, $roles, $shiftsData);
        $this->prepareShiftData($request->administrators, 'admin', $selectedOrganizationId, $roles, $shiftsData);
        $this->prepareShiftData($request->employees, 'employee', $selectedOrganizationId, $roles, $shiftsData);

        MasterShift::insert($shiftsData);
    }

    private function prepareShiftData($users, $roleSlug, $organizationId, $roles, &$shiftsData)
    {
        if (!is_null($users)) {
            foreach ($users as $user) {
                $shiftsData[] = [
                    'user_id' => $user['id'],
                    'group_id' => $organizationId,
                    'role_id' => $roles[$roleSlug]->id,
                ];
            }
        }
    }
}
