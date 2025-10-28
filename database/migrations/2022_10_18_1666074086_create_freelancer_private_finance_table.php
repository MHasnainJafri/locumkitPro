<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancerPrivateFinanceTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('freelancer_private_finance');
        Schema::create('freelancer_private_finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained("users");
            $table->foreignId('freelancer_private_job_id')->constrained();
            $table->float('job_rate');
            $table->date('job_date');
            $table->string('job_expense')->nullable();
            $table->string('employer_name')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('freelancer_private_finances');
    }
}