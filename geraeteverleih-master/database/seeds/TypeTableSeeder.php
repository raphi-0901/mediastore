<?php

use Illuminate\Database\Seeder;
use App\Type;
class TypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new Type();
        $type->name = 'Medientechnik';
        $type->save();

        $type = new Type();
        $type->name = 'Netzwerktechnik';
        $type->save();

        $subMedia = ['Kamera', 'Stativ', 'Mikrofon', 'Objektiv', 'Zubehör', 'Blitz', 'Licht', 'Aufnahme', 'Kopfhörer', 'Lautsprecher', 'Kabel', 'Monitor', 'Computer'];
        $subNetwork = ['Switch', 'Router', 'Kabel'];

        foreach ($subMedia as $sub)
        {
            $type = new Type();
            $type->name = $sub;
            $type->parent_id = 1;
            $type->save();
        }

        foreach ($subNetwork as $sub)
        {
            $type = new Type();
            $type->name = $sub;
            $type->parent_id = 2;
            $type->save();
        }
    }
}
