<?php

namespace app\Modules\YagodaTips\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Modules\YagodaTips\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();

        return response()->json($settings);
    }

    public function save(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $savedSettings = [];

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $setting->update(['value' => $value]);
            } else {
                $setting = Setting::create(['key' => $key, 'value' => $value]);
            }

            $savedSettings[] = $setting;
        }

        return response()->json($savedSettings, 200);
    }
}
