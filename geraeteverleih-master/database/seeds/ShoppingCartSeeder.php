<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Device;
class ShoppingCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $u)
        {
            try{
                $u->shoppingCart()->attach(Device::all()->random());
            }
            catch (Exception $ex){}
        }
    }
}
