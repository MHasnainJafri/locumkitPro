<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        BlogCategory::truncate();
        Schema::enableForeignKeyConstraints();

        $blog_categories = [
            [
                "name" => "Recent"
            ],
            [
                "name" => "Secondary"
            ],
        ];

        BlogCategory::insert($blog_categories);
    }
}