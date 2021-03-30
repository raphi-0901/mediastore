<?php

use Illuminate\Database\Seeder;
use App\Setting;
class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //in days
        $setting = new Setting();
        $setting->key = "maxSpan";
        $setting->value = "28";
        $setting->save();

        $setting = new Setting();
        $setting->key = "maxDeviceCount";
        $setting->value = "10";
        $setting->save();

        //in minutes
        $setting = new Setting();
        $setting->key = "removeFromShoppingCartAfter";
        $setting->value = "60";
        $setting->save();

        //in days
        $setting = new Setting();
        $setting->key = "sendEmailBefore";
        $setting->value = "2";
        $setting->save();
    }
}
