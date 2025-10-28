<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locumlogbook_followup_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->string("practice_name");
            $table->date("date");
            $table->string("patient_id")->nullable();
            $table->string("referred_to")->nullable();
            $table->string("issue_hand")->nullable();
            $table->string("action_required")->nullable();
            $table->dateTime("reminder_datetime")->nullable();
            $table->string("notes")->nullable();
            $table->boolean("is_compeleted")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locumlogbook_followup_procedures');
    }
};