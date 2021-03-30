<?php

use Illuminate\Database\Seeder;
use App\User;
class OnlyAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(TypeTableSeeder::class);

        $user = new User();
        $user->firstName = "Raphael";
        $user->lastName = "Wirnsberger";
        $user->email = "raphi.tab@gmail.com";
        $user->class = "5AHITM";
        $user->password = Hash::make('1234567890');
        $user->role_id = 1;
        $user->save();

        $user = new User();
        $user->firstName = "Julian";
        $user->lastName = "Tschernitz";
        $user->email = "tschernobyl.tab@gmail.com";
        $user->class = "5AHITM";
        $user->password = Hash::make('1234567890');
        $user->role_id = 1;
        $user->save();

        $user = new User();
        $user->firstName = "Medien";
        $user->lastName = "Mangale";
        $user->email = "media.tab@gmail.com";
        $user->class = "Lehrer";
        $user->password = Hash::make('1234567890');
        $user->role_id = 2;
        $user->save();

        $user = new User();
        $user->firstName = "Netz";
        $user->lastName = "Mangale";
        $user->email = "network.tab@gmail.com";
        $user->class = "Lehrer";
        $user->password = Hash::make('1234567890');
        $user->role_id = 2;
        $user->save();
    }
}
