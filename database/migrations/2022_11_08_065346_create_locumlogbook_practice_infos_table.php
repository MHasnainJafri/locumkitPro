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

        Schema::create('locumlogbook_practice_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->string("practice_name")->nullable();
            $table->string("appointment_time_slots")->nullable();
            $table->string("record_keeping")->nullable();
            $table->string("trial_set")->nullable();
            $table->string("phoropter")->nullable();
            $table->string("test_chat_type")->nullable();
            $table->string("visualfield_machinetype")->nullable();
            $table->string("funds_camera")->nullable();
            $table->string("oct")->nullable();
            $table->string("slit_lamp_type")->nullable();
            $table->string("reading_chart")->nullable();
            $table->string("stereo_test_type")->nullable();
            $table->string("colour_vision_type")->nullable();
            $table->string("pre_screening_procdure")->nullable();
            $table->string("is_there_do")->nullable();
            $table->string("contact_lenses")->nullable();
            $table->string("handover_procdure")->nullable();
            $table->string("any_patient_leaflets")->nullable();
            $table->string("primary_care_services")->nullable();
            $table->string("shop_floor_staff_members")->nullable();
            $table->string("no_of_clinics_running")->nullable();
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
        Schema::dropIfExists('locumlogbook_practice_infos');
    }
};