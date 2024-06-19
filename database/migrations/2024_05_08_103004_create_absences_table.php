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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('justification_id')->nullable()->constrained();
            $table->foreignId('absence_states_id')->constrained();
            $table->foreignId('absence_types_id')->constrained();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('absence_start_date');
            $table->dateTime('absence_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
