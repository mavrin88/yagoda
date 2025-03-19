<?php

    namespace App\Modules\YagodaTips\Helpers;

    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    class AccessHelperGroups
    {
        /**
         * Проверяет, имеет ли текущий пользователь доступ к выбранной группе.
         *
         * @return bool
         */
        public static function hasAccessToGroup(): bool
        {
            $user = Auth::guard('web')->user();
            if ($user && $user->roles()->where('role_id', 1)->exists()) {
                return true;
            }

            $selectedGroupId = Session::get('selected_organization_id');

            if (!$user || !$selectedGroupId) {
                return false;
            }
            return DB::table('tips_group_user')
                ->where('user_id', $user->id)
                ->where('group_id', $selectedGroupId)
                ->exists();
        }
    }
