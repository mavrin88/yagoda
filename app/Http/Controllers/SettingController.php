<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Setting;
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

    public function checkInn(Request $request)
    {
        $inn = $request->query('inn');
        $organizationId = $request->query('organizationId');

        $exists = Organization::where('inn', $inn)
            ->when($organizationId, function ($query, $organizationId) {
                return $query->where('id', '!=', $organizationId);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }

}
