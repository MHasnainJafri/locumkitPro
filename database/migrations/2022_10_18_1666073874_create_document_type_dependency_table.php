<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTypeDependencyTable extends Migration
{
    public function up()
    {
        Schema::create('document_type_dependency', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->integer('children_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_type_dependency');
    }
}