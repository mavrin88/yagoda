<?php

namespace App\Http\Controllers;

use App\Events\OrganizationCreated;
use App\Http\Responses\SuccessResponse;
use App\Models\ActivityType;
use App\Models\Organization;
use App\Models\OrganizationStatus;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Modules\YagodaTips\Services\GroupService;
use App\Services\EntityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request as RequestInertia;
use Illuminate\Support\Facades\Log;

class OrganizationsController extends Controller
{
    public function create(): Response
    {
        $activityTypes = ActivityType::all();

        return Inertia::render('Organizations/Create', [
            'activityTypes' => $activityTypes
        ]);
    }

    public function store(): RedirectResponse
    {
//        dd(\request()->all());
//        Request::validate([
//            'name' => ['required', 'max:100'],
//            'full_name' => ['nullable', 'max:100'],
//            'phone' => ['nullable', 'max:50'],
//            'contact_name' => ['nullable', 'max:50'],
//            'contact_phone' => ['nullable', 'max:50'],
//            'inn' => ['nullable', 'max:12'],
//            'legal_address' => ['nullable', 'max:100'],
//            'registration_number' => ['nullable', 'max:100'],
//            'acquiring_fee' => ['nullable', 'max:100'],
//            'photo_path' => ['nullable', 'image'],
//            'tips_1' => ['nullable', 'max:10'],
//            'tips_2' => ['nullable', 'max:10'],
//            'tips_3' => ['nullable', 'max:10'],
//            'tips_4' => ['nullable', 'max:10'],
//        ]);

        Auth::user()->account->organizations()->create([
            'name' => Request::get('name'),
            'full_name' => Request::get('full_name'),
            'phone' => Request::get('phone'),
            'contact_name' => Request::get('contact_name'),
            'contact_phone' => Request::get('contact_phone'),
            'inn' => Request::get('inn'),
            'legal_address' => Request::get('legal_address'),
            'registration_number' => Request::get('registration_number'),
            'acquiring_fee' => Request::get('acquiring_fee'),
            'tips_1' => Request::get('tips_1'),
            'tips_2' => Request::get('tips_2'),
            'tips_3' => Request::get('tips_3'),
            'tips_4' => Request::get('tips_4'),
            'photo_path' => Request::file('photo_path') ? Request::file('photo_path')->store('organizations') : null,
            'activity_type_id' => ['required', 'max:2'],
            'email' => ['required', 'max:50', 'email'],
        ]);


        return Redirect::route('organizations')->with('success', 'Организация успешно создана.');
    }

    public function edit(Organization $organization): Response
    {
        $activityTypes = ActivityType::all();

        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'full_name' => $organization->full_name,
                'phone' => $organization->phone,
                'contact_name' => $organization->contact_name,
                'contact_phone' => $organization->contact_phone,
                'inn' => $organization->inn,
                'legal_address' => $organization->legal_address,
                'registration_number' => $organization->registration_number,
                'acquiring_fee' => $organization->acquiring_fee,
                'photo_path' => $organization->photo_path,
                'tips_1' => $organization->tips_1,
                'tips_2' => $organization->tips_2,
                'tips_3' => $organization->tips_3,
                'tips_4' => $organization->tips_4,
                'deleted_at' => $organization->deleted_at,
                'contacts' => $organization->contacts()->orderByName()->get()->map->only('id', 'name', 'city', 'phone'),
                'activity_type_id' => $organization->activity_type_id,
                'activityTypes' => $activityTypes,
                'email' => $organization->email,
            ],
        ]);
    }

    public function update(Organization $organization): RedirectResponse
    {
        $organization->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'full_name' => ['nullable', 'max:100'],
                'phone' => ['nullable', 'max:50'],
                'contact_name' => ['nullable', 'max:50'],
                'contact_phone' => ['nullable', 'max:50'],
                'inn' => ['nullable', 'max:12'],
                'legal_address' => ['nullable', 'max:100'],
                'registration_number' => ['nullable', 'max:100'],
                'acquiring_fee' => ['nullable', 'max:100'],
                'photo_path' => ['nullable', 'image'],
                'tips_1' => ['nullable', 'max:10'],
                'tips_2' => ['nullable', 'max:10'],
                'tips_3' => ['nullable', 'max:10'],
                'tips_4' => ['nullable', 'max:10'],
                'activity_type_id' => ['required', 'max:2'],
                'email' => ['required', 'max:50', 'email'],
            ])
        );

        return Redirect::back()->with('success', 'Organization updated.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return Redirect::back()->with('success', 'Organization deleted.');
    }

    public function restore(Organization $organization): RedirectResponse
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Organization restored.');
    }

    public function choose(Request $request, GroupService $groupService)
    {
        $user = Auth::user();

        $groups = $groupService->getUserGroups($user);

        $organizations = $user->organizations->map(function ($organization) use ($user) {
            return [
                'organization_id' => $organization->id,
                'organization_name' => $organization->full_name,
                'organization_hide' => $organization->hide,
                'organization_status' => $organization->status,
                'role_id' => $organization->pivot->role_id,
                'role_status' => $organization->pivot->status,
                'role_name' => Role::find($organization->pivot->role_id)->name,
            ];
        });

        $combined = $organizations->merge($groups);

        return Inertia::render('Auth/Choose', [
            'organizations' => $combined
        ]);
    }

    public function selectOrganization(RequestInertia $request)
    {
        $organizationService = new EntityService(Auth::user(), Session::getFacadeRoot());
        return $organizationService->selectOrganization($request);
    }

    public function createNewOrganization()
    {
        $user = Auth::user();

        $organization = Organization::create(
            [
                'account_id' => 1,
                'status' => OrganizationStatus::STATUS_NEW_WITHOUT_SAVE,
//                'hide' => true,
                'name' => '',
                'full_name' => '',
                'phone' => '',
                'inn' => '',
                'contact_phone' => '',
                'contact_name' => '',
                'legal_address' => '',
                'email' => '',
                'registration_number' => '',
                'acquiring_fee' => '17',
                'tips_1' => Setting::where('key', 'tips_1')->first()->value,
                'tips_2' => Setting::where('key', 'tips_2')->first()->value,
                'tips_3' => Setting::where('key', 'tips_3')->first()->value,
                'tips_4' => Setting::where('key', 'tips_4')->first()->value,
                'activity_type_id' => 1,
                'form_id' => 1,
            ]
        );

        // websocket
        try {
            event(new OrganizationCreated($organization));
        } catch (\Throwable $e) {
            Log::error('Ошибка трансляции события OrganizationCreated: ' . $e->getMessage());
        }



        //        $categories = $organization->categories()->where('hide', '=', true)->get();
        $categories = $organization->categories()->get();

        if ($categories->isEmpty()) {
            $category = Category::create([
                'name' => 'Скрытая категория',
                'organization_id' => $organization->id,
                'bill_id' => 1,
                'hide' => true
            ]);

            Product::create([
                'name' => 'Товар/Услуга',
                'category_id' => $category->id,
                'price' => 0,
                'hide' => true
            ]);
        }

        $roleSuperAdminId = Role::where('slug', 'super_admin')->pluck('id')->first();

        $user->organizations()->attach($organization->id, ['role_id' => $roleSuperAdminId]);

        return response()->json(
            [
                'message' => 'Организация создана успешно',
                'organizationId' => $organization->id,
                'roleId' => $roleSuperAdminId,
            ]
        );
    }

    public function deleteOrganization(RequestInertia $request)
    {
        $role = Role::where('slug', 'super_admin')->first();

        $organization = Organization::find($request->organizationId);
        $organization->phone = '+0 (000) 000-00-00';
        $organization->deleted = true;
        $organization->name = '';
        $organization->full_name = '';
        $organization->inn = '';
        $organization->legal_address = '';
        $organization->contact_phone = '';
        $organization->registration_number = '';
        $organization->kpp = '';
        $organization->backup_card = '';
        $organization->status = OrganizationStatus::STATUS_DELETED;
        $organization->save();

        $organization->users()
            ->wherePivot('role_id', $role->id)
            ->update(['organization_user.status' => 'archived']);

        $organization->qrCodes()->delete();
        $organization->bills()->delete();

        if (Session::has('selected_organization_id')) {
            Session::forget('selected_organization_id');
            Session::forget('selected_organization_name');
            Session::forget('selected_organization_role_id');
            Session::forget('selected_organization_role_name');
            Session::forget('selected_organization_role_slug');
        }
    }

    public function deleteNewOrganization(RequestInertia $request)
    {
        $organization = Organization::find($request->organizationId);

        if ($organization) {
            $organization->delete();
            return new SuccessResponse('Не сохраненная oрганизация успешно удалена');
        }

    }


    /**
     * Переключение организации пользователя.
     *
     * Проверяет, авторизован ли пользователь и обладает ли он нужной ролью,
     * затем проверяет существование организации и устанавливает её ID в сессию.
     *
     * @param int $id ID организации
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403, если нет доступа)
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException (404, если организации нет)
     */
    public function switchOrganization($id): RedirectResponse {
        $user = Auth::guard('web')->user();

        // Проверяем авторизацию и роль
        if (!$user || !$user->roles()->where('role_id', 1)->exists()) {
            abort(403, 'Доступ запрещен'); // Пользователь не имеет прав
        }

        // Проверяем существование организации
        if (!Organization::where('id', $id)->exists()) {
            abort(404, 'Организация не найдена');
        }

        $organization = Organization::find($id);

        // Устанавливаем ID организации в сессию
        Session::put('selected_organization_id', $id);
        Session::put('selected_organization_name', $organization->full_name);
        // Устанавливаем роль Суперадмин в сессию
        $roleId = 2;
        Session::put('selected_organization_role_id', $roleId);
        Session::put('selected_organization_role_name', Role::find($roleId)->name);
        Session::put('selected_organization_role_slug', Role::find($roleId)->slug);


        return redirect('/');
    }
}
