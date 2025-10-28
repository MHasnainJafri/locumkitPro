<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddSupplierTable extends Migration
{
	public function up()
	{
		Schema::dropIfExists('suppliers');
		Schema::create('suppliers', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('store_name');
			$table->string('address', 255);
			$table->string('second_address')->nullable(); //change from addresssec to second_address
			$table->string('town', 200)->nullable();
			$table->string('country', 200)->nullable();
			$table->string('postcode', 200)->nullable();
			$table->string('email')->unique();
			$table->string('contact_no')->nullable();
			$table->boolean('automatic_invoice')->default(0); //change from automaticinvoice string='No' to automatic_invoice boolean 1
			$table->enum('status', ['active', 'deleted'])->default('active');
			$table->foreignId('created_by_user_id')->constrained("users");
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('suppliers');
	}
}