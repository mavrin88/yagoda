<?php
    namespace App\Modules\YagodaTips\Services;

    use App\Modules\YagodaTips\Models\GroupUser;
    use App\Modules\YagodaTips\Models\MasterShift;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Session;
    use App\Models\User;
    use Illuminate\Support\Facades\URL;

    class ShiftService
    {

        /**
         * @return array
         */
        public function getAllShifts(): array {
            $selectedOrganizationId = Session::get('selected_organization_id');

            return [
                'administrators' => $this->getUsersWithShifts(User::tipsAdministrators($selectedOrganizationId), $selectedOrganizationId, 3),
                'masters' => $this->getUsersWithShifts(User::tipsMasters($selectedOrganizationId), $selectedOrganizationId, 4),
                'employees' => $this->getUsersWithShifts(User::tipsStaff($selectedOrganizationId), $selectedOrganizationId, 5),
            ];
        }

        public function getAllShiftsCount(): array {
            $selectedOrganizationId = Session::get('selected_organization_id');

            return [
                'administrators' => $this->getUsersWithShifts(User::tipsAdministrators($selectedOrganizationId), $selectedOrganizationId, 3, TRUE),
                'masters' => $this->getUsersWithShifts(User::tipsMasters($selectedOrganizationId), $selectedOrganizationId, 4, TRUE),
                'employees' => $this->getUsersWithShifts(User::tipsStaff($selectedOrganizationId), $selectedOrganizationId, 5, TRUE),
            ];
        }

        /**
         * @param $users
         * @param $organizationId
         * @param $roleId
         * @param $isCount
         *
         * @return mixed
         */
        private function getUsersWithShifts($users, $organizationId, $roleId, $isCount = false): mixed {
            if ($isCount) {
                return MasterShift::where('group_id', $organizationId)
                    ->where('role_id', $roleId)
                    ->get()
                    ->keyBy('user_id')
                    ->count();
            } else {
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
        }

        /**
         * Получить вобще всех мастеров группы
         *
         * @return mixed
         */
        public function getAllMasters(): mixed {
            return GroupUser::where('status', 'active')
                ->where('role_id', 4)
                ->with('user')
                ->get()
                ->map(function($groupUser) {
                    $groupUser->first_name = $groupUser->user->encrypted_first_name
                        ? \Crypt::decryptString($groupUser->user->encrypted_first_name)
                        : '';
                    $groupUser->purpose_tips = $groupUser->user->purpose_tips
                        ?: '';
                    $groupUser->photo_path = \URL::route('image', [
                        'path' => $groupUser->user->photo_path,
                        'w'    => 60,
                        'h'    => 60,
                        'fit'  => 'crop'
                    ]);

                    // Скрываю объект user, он не используется
                    $groupUser->unsetRelation('user');

                    return $groupUser;
                });
        }


    }
