<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageRateReportTable extends Migration
{
    public function up()
    {
        Schema::create('package_rate_report', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->nullable();
            $table->float('package_rate')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_rate_report');
    }
}
