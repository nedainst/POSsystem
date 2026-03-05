<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $groups = [
            'general' => SiteSetting::getGroup('general'),
            'appearance' => SiteSetting::getGroup('appearance'),
            'pos' => SiteSetting::getGroup('pos'),
            'receipt' => SiteSetting::getGroup('receipt'),
        ];

        return view('settings.index', compact('groups'));
    }

    public function update(Request $request)
    {
        $settings = $request->except('_token', '_method');

        foreach ($settings as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            if ($setting) {
                if ($setting->type === 'image' && $request->hasFile($key)) {
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $value = $request->file($key)->store('settings', 'public');
                }

                $setting->update(['value' => $value]);
                Cache::forget("setting_{$key}");
            }
        }

        // Handle checkboxes (boolean settings that are unchecked come as missing)
        $booleanSettings = SiteSetting::where('type', 'boolean')->get();
        foreach ($booleanSettings as $setting) {
            if (!isset($settings[$setting->key])) {
                $setting->update(['value' => '0']);
                Cache::forget("setting_{$setting->key}");
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan!');
    }
}
