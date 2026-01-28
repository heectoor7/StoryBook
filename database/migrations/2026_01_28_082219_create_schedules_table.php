<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->enum('day', ['LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB', 'DOM']);
            $table->time('start');
            $table->time('end');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};