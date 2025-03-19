<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Modules\YagodaTips\Helpers\AccessHelperGroups;
use App\Http\Controllers\Controller;
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
    public function workforce()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');
            $workforce = [
                'administrators' => User::tipsAdministrators($selectedGroupId)->count(),
                'masters' => User::tipsMasters($selectedGroupId)->count(),
                'staff' => User::tipsStaff($selectedGroupId)->count(),
            ];

            return Inertia::render('YagodaTips/Workforce/Index', compact('workforce'));
        }
    }

    public function getWorkforce()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            return response()->json([
                'success' => true,
                'administrators' => User::tipsAdministrators($selectedGroupId)->count(),
                'masters' => User::tipsMasters($selectedGroupId)->count(),
                'staff' => User::tipsStaff($selectedGroupId)->count()
            ]);
        }
    }

    public function administrators()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $admins = User::tipsAdministrators($selectedGroupId);

            $url = '/tips/workforce';

            $admins->each(function ($admin) {
                $admin->photo_path = $admin->photo_path ? URL::route('image', ['path' => $admin->photo_path]) : null;
                $admin->first_name = $admin->encrypted_first_name ? Crypt::decryptString($admin->encrypted_first_name) : null;
                $admin->last_name = $admin->last_name ?: null;
            });

            return Inertia::render('YagodaTips/Workforce/Admins/Index', compact('admins', 'url'));
        }
    }

    public function deleteAdministrator($id)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $user = User::findOrFail($id);

            $role = Role::where('slug', 'admin')->first();

            $user->groups()->newPivotQuery()
                ->where('group_id', $selectedGroupId)
                ->where('role_id', $role->id)
                ->update(['status' => 'archived']);

            return response()->json(['message' => 'Пользователь успешно архивирован']);
        }
    }

    public function masters()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $url = '/tips/workforce';

            $masters = User::tipsMasters($selectedGroupId);

            $masters->each(function ($master) {
                $master->photo_path = $master->photo_path ? URL::route('image', ['path' => $master->photo_path]) : null;
                $master->first_name = $master->encrypted_first_name ? Crypt::decryptString($master->encrypted_first_name) : null;
                $master->last_name = $master->last_name ?: null;
            });

            return Inertia::render('YagodaTips/Workforce/Masters/Index', compact('masters', 'url'));
        }
    }

    public function deleteMaster($id)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $user = User::findOrFail($id);

            $role = Role::where('slug', 'master')->first();

            $user->groups()->newPivotQuery()
                ->where('group_id', $selectedGroupId)
                ->where('role_id', $role->id)
                ->update(['status' => 'archived']);

            return response()->json(['message' => 'Пользователь успешно архивирован']);
        }
    }

    public function staff()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $staff = User::tipsStaff($selectedGroupId);

            $url = '/tips/workforce';

            $staff->each(function ($staf) {
                $staf->photo_path = $staf->photo_path ? URL::route('image', ['path' => $staf->photo_path]) : null;
                $staf->first_name = $staf->encrypted_first_name ? Crypt::decryptString($staf->encrypted_first_name) : null;
                $staf->last_name = $staf->last_name ?: null;
            });

            return Inertia::render('YagodaTips/Workforce/Staff/Index', compact('staff', 'url'));
        }
    }

    public function deleteStaff($id)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $user = User::findOrFail($id);

            $role = Role::where('slug', 'employee')->first();

            $user->groups()->newPivotQuery()
                ->where('group_id', $selectedGroupId)
                ->where('role_id', $role->id)
                ->update(['status' => 'archived']);

            return response()->json(['message' => 'Пользователь успешно архивирован']);
        }
    }

    public function storeUser(Request $request, RedSmsConfirmationService $redSmsConfirmationService)
    {
        $request->validate([
            'phone' => 'required|string',
            'role_slug' => 'required|string',
        ]);

        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $user = User::where('phone', $request->phone)->first();
            $role = Role::where('slug', $request->role_slug)->first();

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Роль не найдена',
                ]);
            }

            if ($user) {
                // Проверяем, есть ли пользователь с выбранной ролью в организации
                $groupUser = $user->groups()
                    ->where('tips_groups.id', $selectedGroupId)
                    ->where('tips_group_user.role_id', $role->id)
                    ->first();

                // Если пользователь уже существует в организации
                if ($groupUser) {
                    // Проверяем статус пользователя
                    if ($groupUser->pivot->status !== 'active') {
                        // Обновляем статус на 'active'
                        $groupUser->pivot->status = 'active';
                        $groupUser->pivot->save();

                        // Подмена данных
                        $user->photo_path = $user->photo_path ? URL::route('image', ['path' => $user->photo_path]) : null;
                        $user->first_name = $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : null;
                        $user->last_name = $user->last_name ?: null;

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
                    $user->groups()->attach($selectedGroupId, ['role_id' => $role->id, 'status' => 'active']);
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

                $user->groups()->attach($selectedGroupId, ['role_id' => $role->id]);
            }

            // Подмена фото
            $user->photo_path = $user->photo_path ? URL::route('image', ['path' => $user->photo_path]) : null;
            $user->first_name = $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : null;
            $user->last_name = $user->last_name ?: null;

            return response()->json([
                'success' => true,
                'message' => $role->name . ' успешно добавлен',
                'user' => $user,
            ]);
        }
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
