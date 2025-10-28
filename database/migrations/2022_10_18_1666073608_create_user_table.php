<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('firstname');
			$table->string('lastname');
			$table->string('email')->unique();
			$table->string('login')->unique();
			$table->string('password');
			$table->timestamp('email_verified_at')->nullable();
			$table->string('retrieve_password_key')->nullable();
			$table->timestamp('retrieve_updated_at')->nullable();
			$table->integer("active")->default(3)->comment("0. Disable 1.Active 2.Block 3. Guest user 4. Expired 5. Deleted");
			$table->foreignId("user_acl_role_id")->constrained();
			$table->foreignId("user_acl_profession_id")->constrained();
			$table->foreignId("user_acl_package_id")->default(4)->constrained();
			$table->boolean("is_free")->default(false);
			$table->rememberToken();
			$table->timestamps();
		});


        
	}

	public function down()
	{
		Schema::dropIfExists('users');
	}
}
