<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class MasterShiftController extends Controller
{
    public function index()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $masters = $this->getUsersWithShifts(User::masters($selectedOrganizationId), $selectedOrganizationId, 4);
        $administrators = $this->getUsersWithShifts(User::administrators($selectedOrganizationId), $selectedOrganizationId, 3);
        $employees = $this->getUsersWithShifts(User::staff($selectedOrganizationId), $selectedOrganizationId, 5);

        return Inertia::render('Admin/MastersShift/Index', compact('masters', 'administrators', 'employees'));
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
            $user['first_name'] = $user->encrypted_first_name? Crypt::decryptString($user->encrypted_first_name): '';
            if (isset($user['photo_path'])) {
                $user['photo_path'] = URL::route('image', ['path' => $user['photo_path'], 'w' => 60, 'h' => 60, 'fit' => 'crop']);
            }
            return $user;
        });
    }

    //        return URL::route('image', ['path' => photo_path, 'w' => 60, 'h' => 60, 'fit' => 'crop']);


    public function saveMastersShift(Request $request)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        MasterShift::where('organization_id', $selectedOrganizationId)->delete();

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
                    'organization_id' => $organizationId,
                    'role_id' => $roles[$roleSlug]->id,
                ];
            }
        }
    }

    public function mastersShiftInOrder()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $masters = User::masters($selectedOrganizationId);

        $mastersShift = MasterShift::where('organization_id', $selectedOrganizationId)->get()->toArray();

        $combinedMasters = $masters->toArray();

        foreach ($combinedMasters as $shift) {
            $masterShiftIndex = array_search($shift['id'], array_column($mastersShift, 'user_id'));

            if ($masterShiftIndex !== false) {
                $combinedMasters[$masterShiftIndex]['shift'] = true;
            }

            $masters = $combinedMasters;
        }

        return response()->json([
            'success' => true,
            'data' => $masters,
        ]);
    }
}
