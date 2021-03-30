<?php

namespace App\Http\Controllers;

use App\Domain;
use App\FoodDay;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function getSettingsView()
    {
        $response = Gate::inspect('viewAny', Setting::class);
        if ($response->allowed()) {
            $settings = Setting::all();
            if ($settings)
                return view("admin.settings")->with("settings", $settings);
            else
                return redirect()->route("index")->withErrors("Fehler beim Laden von Einstellungen");
        } else
            return redirect()->route("index")->withErrors($response->message());
    }

    public function update(Request $request)
    {
        $response = Gate::inspect('update', new Setting());
        if ($response->allowed()) {
            try {
                $this->validate($request,
                    [
                        'maxSpan' => 'required|integer',
                        'maxDeviceCount' => 'required|integer',
                        'removeFromShoppingCartAfter' => 'required|integer',
                        'sendEmailBefore' => 'required|integer',
                    ]
                );

                foreach ($request->all() as $key => $value) {
                    switch ($key) {
                        //ignore these
                        case "_token":
                            continue 2;

                        //default for others
                        default:
                            Setting::where('key', $key)->update(['value' => $value]);
                    }
                }
            } catch (\Exception $ex) {
                return redirect()->route('index')
                    ->withErrors('Einstellungen konnten nicht aktualisiert werden!');
            }
            return redirect()->route("index");
        } else
            return redirect()->route("index")->withErrors($response->message());
    }
}
