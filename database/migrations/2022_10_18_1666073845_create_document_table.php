<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTable extends Migration
{
    public function up()
    {
        Schema::create('document', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url_key');
            $table->integer('status')->default('0')->nullable();
            $table->integer('sort_order')->default('0')->nullable();
            $table->tinyInteger('show_in_nav')->default('0')->nullable();
            $table->tinyInteger('can_be_cached')->default('1')->nullable();
            $table->string('locale')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('document_type_id')->nullable();
            $table->integer('view_id')->nullable();
            $table->integer('layout_id')->nullable();
            $table->integer('parent_id')->default('0')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document');
    }
}
