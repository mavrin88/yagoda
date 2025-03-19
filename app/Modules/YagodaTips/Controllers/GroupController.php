<?php

namespace App\Modules\YagodaTips\Controllers;

use App\Helpers\AccessHelper;
use App\Http\Controllers\Controller;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\ActivityType;
use App\Models\OrganizationForm;
use App\Models\User;
use App\Modules\YagodaTips\Helpers\AccessHelperGroups;
use App\Modules\YagodaTips\Models\Group;
use App\Modules\YagodaTips\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Intervention\Image\Facades\Image;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find($request->user()?->id);

        $activityTypes = ActivityType::all();

        $formattedArray = $activityTypes->map(function ($activityType) {
            return ['id' => $activityType->id, 'label' => $activityType->name, 'value' => $activityType->id];
        });

        $selectedOrganizationId = Session::get('selected_organization_id');

        $group = Group::find($selectedOrganizationId);

        $selectActivityType = ActivityType::find($group->activity_type_id);

        $selectActivityType = (object)[
            'id' => $selectActivityType->id,
            'label' => $selectActivityType->name,
            'value' => $selectActivityType->id,
        ];

        $group->backup_card = !empty($group->backup_card) ? Crypt::decryptString($group->backup_card) : null;
        $group->logo_path = $group->logo_path ? URL::route('image', ['path' => $group->logo_path,]) : null;
        $group->photo_path = $group->photo_path ? URL::route('image', ['path' => $group->photo_path,]) : null;

        return Inertia::render('YagodaTips/Groups/Index',
            compact('group', 'selectActivityType', 'user', 'formattedArray')
        );
    }

    public function getGroup(Request $request)
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedOrganizationId = Session::get('selected_organization_id');

            $group = Group::find($selectedOrganizationId);

            return response()->json([
                'success' => true,
                'group' => $group,
            ]);
        }
    }

    /**
     * Получить данные организации для шапки
     *
     * @return \App\Http\Responses\ErrorResponse|\App\Http\Responses\SuccessResponse
     */
    public function getData(): ErrorResponse|SuccessResponse {
        if (AccessHelper::hasAccessToOrganization()) {
            $selectedOrganizationId = Session::get('selected_organization_id');
            if ($selectedOrganizationId) {
                $group = Group::find($selectedOrganizationId);
                if ($group) {
                    $group->logo_path = $group->logo_path ? URL::route('image', ['path' => $group->logo_path,]) : null;
                    $group->photo_path = $group->photo_path ? URL::route('image', ['path' => $group->photo_path,]) : null;
                    return new SuccessResponse(json_encode($group));
                }
            }
        }
        return new ErrorResponse('Нет доступа');
    }

    public function storeSettings(Request $request)
    {
        $user = User::find($request->user()?->id);

        $group = Group::find($request->group['id']);

        $group->name = $request->group['name'];
        $group->full_name = $request->group['full_name'];
        $group->contact_name = $request->group['contact_name'];
        $group->contact_phone = $request->group['contact_phone'];
        $group->activity_type_id = $request->group['activity_type_id'];
        $group->email = $request->group['email'];
        $group->backup_card = !empty($request->group['backup_card']) ? Crypt::encryptString($request->group['backup_card']) : null;
        $group->save();

        $user->update([
            'phone' => $request->user['phone']
        ]);

        if (($request->group['saveImageLogo'] ?? false)) {
            $base64String = $request->group['saveImageLogo'];

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {

                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                $fileName = 'logo_' . time() . '.' . $imageType;

                Storage::put('organizations/' . $fileName, $imageData);

                $publicPath = 'organizations/' . $fileName;

                $group->update([
                    'logo_path' => $publicPath
                ]);
            }
        }

        if (($request->group['saveImage'] ?? false)) {
            $base64String = $request->group['saveImage'];

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {
                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                // Сжимаем изображение с помощью Intervention/Image
                $image = Image::make($imageData);
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode($imageType, 80);

                $fileName = 'img_' . time() . '.' . $imageType;

                Storage::put('organizations/' . $fileName, $image->encoded);

                $publicPath = 'organizations/' . $fileName;

                $group->update([
                    'photo_path' => $publicPath
                ]);
            }
        }
    }

    public function setTips(Request $request)
    {
        $organization = Group::find($request->group['id']);
        $maxTipPercentage = Setting::where('key', 'maximum_tip_amount')->first()->value;

        $hasError = false;
        $errorMessage = '';

        foreach ($request->tips as $item) {
            switch ($item['name']) {
                case 'tips_1':
                    $organization->tips_1 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
                case 'tips_2':
                    $organization->tips_2 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
                case 'tips_3':
                    $organization->tips_3 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
                case 'tips_4':
                    $organization->tips_4 = $this->validateTip($item['value'], $hasError, $errorMessage);
                    break;
            }
        }

        if ($hasError) {
            return response()->json(['error' => true, 'message' => $errorMessage, 'value' => $maxTipPercentage], 200);
        }

        $organization->save();

        return response()->json(['success' => true, 'message' => 'Чаевые успешно сохранены.']);
    }

    private function validateTip($value, &$hasError, &$errorMessage)
    {
        $maxTipPercentage = Setting::where('key', 'maximum_tip_amount')->first()->value;

        if ($value > $maxTipPercentage) {
            $hasError = true;
            $errorMessage = "Максимальное значение чаевых не может превышать {$maxTipPercentage}Р.";
            return $maxTipPercentage;
        }
        return $value;
    }

    public function settings()
    {
        return Inertia::render('YagodaTips/Groups/Settings/Index');
    }

    public function mapLinks()
    {
        if (AccessHelperGroups::hasAccessToGroup()) {
            $selectedGroupId = Session::get('selected_organization_id');

            $group = Group::find($selectedGroupId);

            return Inertia::render('YagodaTips/Groups/Settings/MapLinks/Index', compact('group'));
        }
    }

    public function saveLinksSettings(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:organizations,id',
            'map_link_yandex' => 'nullable',
            'map_link_2gis' => 'nullable',
        ]);

        try {
            $group = Group::find($validatedData['id']);

            if (!$group) {
                return response()->json([
                    'success' => false,
                    'message' => 'Организация не найдена',
                ], 404);
            }

            $group->map_link_yandex = $validatedData['map_link_yandex'] ?? null;
            $group->map_link_2gis = $validatedData['map_link_2gis'] ?? null;
            $group->save();

            return response()->json([
                'success' => true,
                'message' => 'Ссылки успешно обновлены'
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при обновлении ссылок: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении ссылок',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
