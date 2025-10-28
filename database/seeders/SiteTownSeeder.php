<?php

namespace Database\Seeders;

use App\Models\SiteTown;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteTownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SiteTown::truncate();
        Schema::enableForeignKeyConstraints();
        $site_town_sql_file_path = "database/factories/SiteTownFactory.sql";
        if (file_exists(base_path($site_town_sql_file_path))) {
            $sql = file_get_contents(base_path($site_town_sql_file_path));
            DB::unprepared($sql);
        }
    }
}