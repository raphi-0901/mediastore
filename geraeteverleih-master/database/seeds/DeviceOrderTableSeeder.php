<?php

use Illuminate\Database\Seeder;
use App\Order;
use App\Device;
class DeviceOrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::all();
        foreach ($orders as $order) {
            for($i = 0; $i < 15; $i++)
            {
                try{
                    $order->devices()->attach(Device::all()->random());
                }
                    //duplicate
                catch (Exception $ex) {}
            }
        }

    }
}
