<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentInfoTable extends Migration
{
    public function up()
    {
        Schema::create('user_payment_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->foreignId("user_acl_package_id")->constrained();
            $table->string('payment_type');
            $table->decimal('price', 7, 2);
            $table->integer('payment_status');
            $table->string('payment_token')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_payment_infos');
    }
}