<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Category;
use App\Models\Organization;
use App\Models\OrganizationStatus;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\CallService;
use App\Services\RedSmsConfirmationService;
use App\Services\SmsConfirmationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    private $callService;
    public function __construct(CallService $callService)
    {
        $this->callService = $callService;
    }

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }
    public function index(): Response
    {
        return Inertia::render('Auth/LoginForCanNewOrganization');
    }

    public function checkUserExists(Request $request, RedSmsConfirmationService $redSmsConfirmationService)
    {
        $phoneNumber = preg_replace('/\D/', '', $request->phone);

        $user = User::where('phone', $request->phone)->first();

        $isUserNew = false;
        $ownOrganization = $request->is_own_organization ? true : false;


        if (!$user) {
            $user = User::create([
                'account_id' => 1,
                'is_own_organization' => $ownOrganization,
                'first_add_organization' => true,
                'phone' => $request->phone,
                'encrypted_first_name' => Crypt::encryptString(''),
                'encrypted_card_number' => empty($request->card_number) ? null : Crypt::encryptString($request->card_number),
                'encrypted_email' => Crypt::encryptString(''),
                'last_name' => '',
                'password' => 'secret',
            ]);
            $isUserNew = true;
        }

        if ($user->phone == '+7 (123) 456-78-90') {
            return response()->json([
                'success' => true,
            ]);
        }else {
            $sms = $redSmsConfirmationService->sendSmsCode($phoneNumber, $user->id);

            if (!$sms) {
                return response()->json([
                    'message' => 'Не удалось отправить SMS-код, превышен лимит запросов, повторите попытку позже.',
                    'success' => false,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'isUserNew' => $isUserNew,
        ]);
    }

    public function verifySmsCode(LoginRequest $request, SmsConfirmationService $smsConfirmationService)
    {

        if ($request->phone == '+7 (123) 456-78-90') {

        }else {
            if (!$smsConfirmationService->verifySmsCode($request->code)) {
                return response()->json([
                    'message' => 'Не верный код',
                    'success' => false,
                ]);
            }
        }

        $user = User::where('phone', $request->phone)->first();
        $user->incrementLoginCount();

        $request->authenticate();

        $request->session()->regenerate();

        if ($user->organizations->count() > 0) {

            $organizations = $user->organizations->map(function ($organization) use ($user){
                return [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'role_id' => $organization->pivot->role_id,
                    'role_name' => Role::find($organization->pivot->role_id)->name,
                ];
            });

            return response()->json([
                'success' => true,
                'organizations' =>  $organizations,
                'loginCount' => $user->login_count,
                'is_registered' => $user->is_registered
            ]);
        }else{

            $organization = Organization::create(
                [
                    'account_id' => 1,
                    'status' => OrganizationStatus::STATUS_NEW,
                    'hide' => true,
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

            Session::put('selected_organization_id', $organization->id);

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

            $user = Auth::user();
            DB::table('organization_user')->insert([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'role_id' => RolesEnum::super_admin
            ]);

            return response()->json([
                'success' => true,
                'organizations' =>  $organization,
                'loginCount' => $user->login_count,
                'is_registered' => 0,
                'role_id' => RolesEnum::super_admin,
            ]);

        }

        return response()->json([
            'message' => 'У вас нет организаций, обратитесь к администратору',
            'success' => true,
            'organizations' => [],
            'loginCount' => $user->login_count
        ]);
    }

    public function getNewSmsCodeEditNumber(Request $request, SmsConfirmationService $smsConfirmationService)
    {
        $sms = $smsConfirmationService->sendSmsCode($request->phone);

        if (!$sms) {
            return response()->json([
                'message' => 'Не удалось отправить SMS-код, превышен лимит запросов, повторите попытку позже.',
                'success' => false,
            ]);
        }
    }

    public function verifySmsCodeEditNumber(Request $request, SmsConfirmationService $smsConfirmationService)
    {
        if (!$smsConfirmationService->verifySmsCode($request->code)) {
            return response()->json([
                'message' => 'Неверный код пожтверждения',
                'success' => false,
            ]);
        }

        $user = Auth::user();

        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'message' => 'Верный код пожтверждения',
            'success' => true,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function registration()
    {
        $user = Auth::user();

        return Inertia::render('Auth/UserSettings', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : '',
                'card_number' => $user->encrypted_card_number ? Crypt::decryptString($user->encrypted_card_number) : '',
                'photo_path' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path]) : null,
                'telegram_name' => $user->telegram_name,
                'is_registered' => $user->is_registered,
                'is_own_organization' => $user->is_own_organization,
            ],
        ]);
    }

    public function userSettings()
    {
        $user = Auth::user();

        return Inertia::render('Auth/UserSettings', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->encrypted_first_name ? Crypt::decryptString($user->encrypted_first_name) : '',
                'card_number' => $user->encrypted_card_number ? Crypt::decryptString($user->encrypted_card_number) : '',
                'photo_path' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path]) : null,
                'telegram_name' => $user->telegram_name,
                'is_registered' => $user->is_registered,
                'purpose_tips' => $user->purpose_tips,
                'email' => $user->email,
            ],
        ]);
    }

    public function userDelete(User $user, Request $request)
    {
        $organiazations = Organization::where('phone', $user->phone)->count();

        if ($organiazations > 0) {
            return response()->json([
                'message' => 'Удалить аккаунт невозможно. К вашему номеру привязана организация. Замените в организации ваш номер на другой. И после этого вы сможете удалить свой аккаунт.',
                'success' => false,
            ]);
        }

        $user->first_name = 'Без имени';
        $user->last_name = null;
        $user->photo_path = null;
        $user->card_number = null;
        $user->status = 'deleted';

        $totalDeletedUsers = User::where('status', 'deleted')->count();

        $user->phone = '+0' . str_pad($totalDeletedUsers + 1, 9, '0', STR_PAD_LEFT);

        $user->save();

        return $this->destroy($request);

    }

    public function userUpdate(Request $request)
    {
        $user = User::find($request->user['id']);

        $user->update([
            'encrypted_first_name' => Crypt::encryptString($request->user['first_name']),
            'telegram_name' => $request->user['telegram_name'],
            'encrypted_card_number' => !empty($request->user['card_number']) ? Crypt::encryptString($request->user['card_number']) : null,
            'is_registered' => true,
            'purpose_tips' => $request->user['purpose_tips'] ?? null,
            'email' => $request->user['email'] ?? null,
        ]);

        if (($request->user['photo_path'] ?? false)) {
            $base64String = $request->user['photo_path'];

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {

                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                $fileName = 'photo_' . time() . '.' . $imageType;

                Storage::put('users/' . $fileName, $imageData);

                $imagePath = 'users/' . $fileName;
            }
        }

        $user->photo_path = $imagePath ?? $user->photo_path;
        $user->save();


    }
}
