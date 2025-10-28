<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCommentTable extends Migration
{
    public function up()
    {
        Schema::create('blog_comment', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email');
            $table->boolean('show_email')->default(false);
            $table->boolean('is_active')->default(false);
            $table->text('message');
            $table->integer('document_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_comment');
    }
}
