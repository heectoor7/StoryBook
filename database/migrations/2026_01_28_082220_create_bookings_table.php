<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED'])->default('PENDING');
            $table->timestamps();
            
            $table->unique(['service_id', 'date', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};