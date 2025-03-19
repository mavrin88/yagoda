<?php

    namespace App\Helpers;

    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    class AccessHelper
    {
        /**
         * Проверяет, имеет ли текущий пользователь доступ к выбранной организации.
         *
         * @return bool
         */
        public static function hasAccessToOrganization(): bool
        {
            $user = Auth::guard('web')->user();
            if ($user && $user->roles()->where('role_id', 1)->exists()) {
                return true;
            }

            $selectedOrganizationId = Session::get('selected_organization_id');

            if (!$user || !$selectedOrganizationId) {
                return false;
            }
            return DB::table('organization_user')
                ->where('user_id', $user->id)
                ->where('organization_id', $selectedOrganizationId)
                ->exists();
        }
    }
