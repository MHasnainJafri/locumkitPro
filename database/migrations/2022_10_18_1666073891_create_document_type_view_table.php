<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTypeViewTable extends Migration
{
    public function up()
    {
        Schema::create('document_type_view', function (Blueprint $table) {
            $table->id();
            $table->integer('view_id');
            $table->integer('document_type_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_type_view');
    }
}