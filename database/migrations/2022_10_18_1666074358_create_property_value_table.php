<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePropertyValueTable extends Migration
{
    public function up()
    {
        Schema::create('property_value', function (Blueprint $table) {
            $table->id();
            $table->integer('document_id')->nullable();
            $table->integer('property_id')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE property_value ADD value LONGBLOB NULL AFTER property_id;");
    }

    public function down()
    {
        Schema::dropIfExists('property_value');
    }
}
