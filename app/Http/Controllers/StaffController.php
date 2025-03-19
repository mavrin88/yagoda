<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\RedSmsConfirmationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function staff()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $staff = User::staff($selectedOrganizationId);

        $url = '/super_admin/workforce';

        $staff->each(function ($staf) {
            $staf->photo_path = $staf->photo_path ? URL::route('image', ['path' => $staf->photo_path]) : null;
            $staf->first_name = $staf->encrypted_first_name ? Crypt::decryptString($staf->encrypted_first_name) : null;
            $staf->last_name = $staf->last_name ?: null;
        });

        return Inertia::render('General/Workforce/Staff/Index', compact('staff', 'url'));
    }

    public function admin_staff()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $staff = User::staff($selectedOrganizationId);

        $url = '/admin/workforce';

        $staff->each(function ($staf) {
            $staf->photo_path = $staf->photo_path ? URL::route('image', ['path' => $staf->photo_path]) : null;
            $staf->first_name = $staf->encrypted_first_name ? Crypt::decryptString($staf->encrypted_first_name) : null;
            $staf->last_name = $staf->last_name ?: null;
        });

        return Inertia::render('General/Workforce/Staff/Index', compact('staff', 'url'));
    }

    public function deleteStaff($id)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $user = User::findOrFail($id);

        $role = Role::where('slug', 'employee')->first();

        $user->organizations()->newPivotQuery()
            ->where('organization_id', $selectedOrganizationId)
            ->where('role_id', $role->id)
            ->update(['status' => 'archived']);

        return response()->json(['message' => 'Пользователь успешно архивирован']);
    }

    public function masters()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $url = '/super_admin/workforce';

        $masters = User::masters($selectedOrganizationId);

        $masters->each(function ($master) {
            $master->photo_path = $master->photo_path ? URL::route('image', ['path' => $master->photo_path]) : null;
            $master->first_name = $master->encrypted_first_name ? Crypt::decryptString($master->encrypted_first_name) : null;
            $master->last_name = $master->last_name ?: null;
        });

        return Inertia::render('General/Workforce/Masters/Index', compact('masters', 'url'));
    }

    public function admin_masters()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $url = '/admin/workforce';

        $masters = User::masters($selectedOrganizationId);

        $masters->each(function ($master) {
            $master->photo_path = $master->photo_path ? URL::route('image', ['path' => $master->photo_path]) : null;
            $master->first_name = $master->encrypted_first_name ? Crypt::decryptString($master->encrypted_first_name) : null;
            $master->last_name = $master->last_name ?: null;
        });

        return Inertia::render('General/Workforce/Masters/Index', compact('masters', 'url'));
    }

    public function deleteMaster($id)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $user = User::findOrFail($id);

        $role = Role::where('slug', 'master')->first();

        $user->organizations()->newPivotQuery()
            ->where('organization_id', $selectedOrganizationId)
            ->where('role_id', $role->id)
            ->update(['status' => 'archived']);

        return response()->json(['message' => 'Пользователь успешно архивирован']);
    }

    public function administrators()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $admins = User::administrators($selectedOrganizationId);

        $url = '/super_admin/workforce';

        $admins->each(function ($admin) {
            $admin->photo_path = $admin->photo_path ? URL::route('image', ['path' => $admin->photo_path]) : null;
            $admin->first_name = $admin->encrypted_first_name ? Crypt::decryptString($admin->encrypted_first_name) : null;
            $admin->last_name = $admin->last_name ?: null;
        });

        return Inertia::render('General/Workforce/Admins/Index', compact('admins', 'url'));
    }

    public function deleteAdministrator($id)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $user = User::findOrFail($id);

        $role = Role::where('slug', 'admin')->first();

        $user->organizations()->newPivotQuery()
            ->where('organization_id', $selectedOrganizationId)
            ->where('role_id', $role->id)
            ->update(['status' => 'archived']);

        return response()->json(['message' => 'Пользователь успешно архивирован']);
    }

    public function workforce()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $workforce = [
            'administrators' => User::administrators($selectedOrganizationId)->count(),
            'masters' => User::masters($selectedOrganizationId)->count(),
            'staff' => User::staff($selectedOrganizationId)->count(),
        ];

        return Inertia::render('SuperAdmin/Workforce/Index', compact('workforce'));
    }

    public function adminWorkforce()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $workforce = [
            'masters' => User::masters($selectedOrganizationId)->count(),
            'staff' => User::staff($selectedOrganizationId)->count(),
        ];

        return Inertia::render('Admin/Workforce/Index', compact('workforce'));
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'role_slug' => 'required|string',
        ]);
    }

    public function storeUser(Request $request, RedSmsConfirmationService $redSmsConfirmationService)
    {
        $request->validate([
//            'first_name' => 'string',
//            'last_name' => 'string',
            'phone' => 'required|string',
            'role_slug' => 'required|string',
        ]);

        $selectedOrganizationId = Session::get('selected_organization_id');

        $user = User::where('phone', $request->phone)->first();
        $role = Role::where('slug', $request->role_slug)->first();

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Роль не найдена',
            ]);
        }

//        $hasOrganizationWithRole = $user ? $user->organizations()
//            ->where('organizations.id', $selectedOrganizationId)
//            ->where('organization_user.role_id', $role->id)
//            ->exists() : false;

        if ($user) {
            // Проверяем, есть ли пользователь с выбранной ролью в организации
            $organizationUser = $user->organizations()
                ->where('organizations.id', $selectedOrganizationId)
                ->where('organization_user.role_id', $role->id)
                ->first();

            // Если пользователь уже существует в организации
            if ($organizationUser) {
                // Проверяем статус пользователя
                if ($organizationUser->pivot->status !== 'active') {
                    // Обновляем статус на 'active'
                    $organizationUser->pivot->status = 'active';
                    $organizationUser->pivot->save();

                    return response()->json([
                        'success' => true,
                        'message' => $role->name . ' успешно добавлен',
                        'user' => $user,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $role->name . ' с таким номером телефона уже присутствует в организации',
                    ]);
                }
            } else {
                // Если пользователя нет в организации, добавляем его
                $user->organizations()->attach($selectedOrganizationId, ['role_id' => $role->id, 'status' => 'active']);
            }
        } else {
            $user = User::create([
                'account_id' => 1,
                'password' => Hash::make('secret'),
                'phone' => $request->phone,
            ]);

            $smsText = "В Yagoda вам добавлена роль {$role->name}. Пройдите регистрацию https://yagoda.team/login";

            $phoneNumber = preg_replace('/\D/', '', $request->phone);

            $redSmsConfirmationService->sendSms($phoneNumber, $smsText);

            $user->organizations()->attach($selectedOrganizationId, ['role_id' => $role->id]);
        }



//        if ($user) {
//            if ($hasOrganizationWithRole) {
//                return response()->json([
//                    'success' => false,
//                    'message' => $role->name . ' с таким номером телефона уже присутствует в организации',
//                ]);
//            } else {
//                $user->organizations()->attach($selectedOrganizationId, ['role_id' => $role->id]);
//            }
//        } else {
////                return response()->json([
////                    'success' => false,
////                    'message' => 'Пользователь с таким номером не найден.',
////                ]);
//
//            $user = User::create([
//                'account_id' => 1,
//                'password' => Hash::make('secret'),
//                'phone' => $request->phone,
////                'encrypted_card_number' => empty($request->card_number) ? null : Crypt::encryptString($request->card_number),
////                'encrypted_email' => Crypt::encryptString(''),
////                'first_name' => $request->first_name,
////                'last_name' => $request->last_name,
////                'encrypted_first_name' => Crypt::encryptString($request->first_name),
//            ]);
//
//            $smsText = "В Yagoda вам добавлена роль {$role->name}. Пройдите регистрацию https://yagoda.team/login";
//
//            $phoneNumber = preg_replace('/\D/', '', $request->phone);
//
//            $redSmsConfirmationService->sendSms($phoneNumber, $smsText);
//
//            $user->organizations()->attach($selectedOrganizationId, ['role_id' => $role->id]);
//        }

        return response()->json([
            'success' => true,
            'message' => $role->name . ' успешно добавлен',
            'user' => $user,
        ]);
    }

    public function deleteArchivedRole(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|integer|exists:organizations,id',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $userInOrganization = DB::table('organization_user')
            ->where('organization_id', $request->organization_id)
            ->where('role_id', $request->role_id)
            ->where('status', 'archived');

        if ($userInOrganization->exists()) {
            $userInOrganization->update(['status' => 'deleted']);

            return response()->json([
                'success' => true,
                'message' => 'Роль успешно удалена.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Роль не найдена или уже удалена.',
        ], 404);
    }
}
