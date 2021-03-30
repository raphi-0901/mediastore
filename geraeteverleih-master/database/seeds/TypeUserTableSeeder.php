<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Type;
class TypeUserTableSeeder extends Seeder
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
           if($u->isTeacher())
           {
               for($i = 0;$i<15;$i++)
               {
                   try{
                       $u->types()->attach(Type::all()->random()->id);
                   }
                   catch (Exception $ex){}
               }
           }
       }
    }
}
