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
       //path to sql file
        $sql = base_path('countries.sql');
        $phase1 = base_path('phase1.sql');

        //collect contents and pass to DB::unprepared
        DB::unprepared(file_get_contents($phase1));
        DB::unprepared(file_get_contents($sql));
    }
}
