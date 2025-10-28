<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        ExpenseType::truncate();
        Schema::enableForeignKeyConstraints();

        $expense_types = [
            ["expense" => "Travel", "expense_colour" => "#e42f61",],
            ["expense" => "Hotels", "expense_colour" => "#e42fac",],
            ["expense" => "Lunch", "expense_colour" => "#7d2fe4",],
            ["expense" => "Entertaining", "expense_colour" => "#0ee9ff",],
            ["expense" => "Insurance", "expense_colour" => "#2fe467",],
            ["expense" => "Professional fees", "expense_colour" => "#efff00",],
            ["expense" => "Accountancy fees", "expense_colour" => "#e47a2f",],
            ["expense" => "Stationary", "expense_colour" => "#e4482f",],
            ["expense" => "Charity", "expense_colour" => "#322fe4",],
            ["expense" => "Telephone", "expense_colour" => "#2f77e4",],
            ["expense" => "Computer costs", "expense_colour" => "#008000",],
            ["expense" => "Books / Magazine", "expense_colour" => "#ffcc80",],
            ["expense" => "Depreciation", "expense_colour" => "#00A9E0",],
            ["expense" => "Interest", "expense_colour" => "#077598",],
            ["expense" => "Other", "expense_colour" => "#ccc",],
        ];

        ExpenseType::insert($expense_types);
    }
}