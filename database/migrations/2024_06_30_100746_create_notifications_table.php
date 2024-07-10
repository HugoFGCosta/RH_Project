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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('absence_id')->nullable();
            $table->foreignId('vacation_id')->nullable();
            $table->foreignId('events_id')->nullable();
            $table->boolean('state')->default(false); // false = não lido, true = lido
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('absence_id')->references('id')->on('absences')->onDelete('cascade');
            $table->foreign('vacation_id')->references('id')->on('vacations')->onDelete('cascade');
            $table->foreign('events_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};