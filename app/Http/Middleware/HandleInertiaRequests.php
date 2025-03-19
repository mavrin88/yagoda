<?php

namespace App\Http\Middleware;

use App\Models\Bill;
use App\Models\Organization;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Middleware;
use App\Models\User;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        $user = User::find($request->user()?->id);

        $roles = Role::where('in_adminka', 1)->get();

        $roles = $roles->map(function ($roles) {
            return (object)[
                'id' => $roles->id,
                'label' => $roles->name,
                'value' => $roles->id,
            ];
        });

        $selectedOrganizationId = Session::get('selected_organization_id');
        $selectedOrganizationName = Session::get('selected_organization_name');
        $selectedRoleId = Session::get('selected_organization_role_id');
        $selectedRoleName = Session::get('selected_organization_role_name');

        $organization = Organization::find($selectedOrganizationId) ?: null;

        if ($organization) {
            $organization->photo_path = $organization->photo_path ? URL::route('image', ['path' => $organization->photo_path]) : null;
            $organization->logo_path = $organization->logo_path ? URL::route('image', ['path' => $organization->logo_path]) : null;
        } else {

        }

        return array_merge(parent::share($request), [
            'auth' => function () use ($request, $user, $selectedOrganizationId, $selectedRoleId, $selectedRoleName, $selectedOrganizationName, $organization) {
                return [
                    'organization' => $organization,
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'is_own_organization' => $request->user()->is_own_organization,
                        'first_name' => $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : '',
                        'last_name' => $request->user()->last_name,
                        'email' => $user->encrypted_email ? Crypt::decryptString($user->encrypted_email) : '',
                        'owner' => $request->user()->owner,
                        'photo_path' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path]) : null,
                        'account' => [
                            'id' => $request->user()->account->id,
                            'name' => $request->user()->account->name,
                        ],
                        'selected_organization_id' => $selectedOrganizationId,
                        'selected_organization_name' => $selectedOrganizationName,
                        'selected_organization_role_id' => $selectedRoleId,
                        'selected_organization_role_name' => $selectedRoleName,
                        'super_admin_block' => [
                            'settings' => $organization = Organization::find($selectedOrganizationId),
                            $organization ? $organization->areSettingsFilled() : false,
                            'bills' => Bill::where('organization_id', $selectedOrganizationId)->count() > 0,
                        ],
                        'data' => $user->organizations->map(function ($organization) use ($user){
                            return [
                                'organization_id' => $organization->id,
                                'organization_name' => $organization->name,
                                'role_id' => $organization->pivot->role_id,
                                'role_status' => $organization->pivot->status,
                            ];
                        }),
                    ] : null,
                ];
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'dashboard_roles' => function () use ($roles) {
                return [
                    'roles' => $roles,
                ];
            }
        ]);
    }
}
