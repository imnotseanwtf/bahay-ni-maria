<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class, 'caregiver_id')->constrained()->cascadeOnDelete();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('maiden_name')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('zip');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('phone');
            $table->integer('marital_status');
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->string('spouse_maiden_name')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_relationship');
            $table->string('emergency_contact_phone');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('device_identifier')->unique();
            $table->string('room')->nullable();
            $table->string('bed_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
