<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        for($i = 0; $i < 15; $i++)
        {
            $user = new User();
            $user->firstName = $faker->firstName;
            $user->lastName = $faker->lastName;
            $user->email = $faker->unique()->email;
            $user->class = "5AHITM";
            $user->password = Hash::make('1234567890');
            $user->role_id = 3;
            $user->save();
        }

        for($i = 0; $i < 3; $i++)
        {
            $user = new User();
            $user->firstName = $faker->firstName;
            $user->lastName = $faker->lastName;
            $user->email = $faker->unique()->email;
            $user->class = "Lehrer";
            $user->password = Hash::make('1234567890');
            $user->role_id = 2;
            $user->save();
        }

        $user = new User();
        $user->firstName = $faker->firstName;
        $user->lastName = $faker->lastName;
        $user->email = $faker->unique()->email;
        $user->class = "Admin";
        $user->password = Hash::make('1234567890');
        $user->role_id = 1;
        $user->save();
    }
}
