<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserQuestionTable extends Migration
{
	public function up()
	{
		Schema::create('user_questions', function (Blueprint $table) {
			$table->id();
			$table->foreignId("user_acl_profession_id")->constrained()->cascadeOnDelete();
			$table->string('employer_question')->nullable();
			$table->string('freelancer_question')->nullable();
			$table->integer("type")->comment("1. Text field, 2. Select option, 3. Multi Select, 4. Comparative, 5.Range, 6. Yes/No");
			$table->text("values")->nullable();
			$table->integer('sort_order')->default(0);
			$table->boolean('is_required')->default(false);
			$table->string('range_type_unit')->nullable();
			$table->integer('range_type_condition')->nullable()->comment("1. Greater than 2. Greater than OR equel to 3. Less than 4. Less than OR equel to 5. Equel to");
			$table->boolean('is_active')->default(true);
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('user_questions');
	}
}