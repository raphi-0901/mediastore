<?php

use Illuminate\Database\Seeder;
use App\Order;
use App\User;
class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($i = 0; $i < 100; $i++)
        {
            $order = new Order();
            $from = $faker->numberBetween(-15, 45);
            $to = $faker->numberBetween($from, 45);
            $order->from = \Carbon\Carbon::today()->subDays($from);
            $order->to = \Carbon\Carbon::today()->addDays($to);
            $order->user_id = User::students()->random()->id;
            $order->save();
        }
    }
}

