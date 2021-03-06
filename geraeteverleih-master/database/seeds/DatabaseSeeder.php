<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(TypeTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        //$this->call(OrdersTableSeeder::class);
        //$this->call(TypeUserTableSeeder::class);
        //$this->call(DeviceOrderTableSeeder::class);
        //$this->call(ShoppingCartSeeder::class);
    }
}
