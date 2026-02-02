<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar logo a companies
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo', 255)->nullable()->after('description');
        });

        // Agregar imagen a posts (para stories y publicaciones)
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->after('content');
        });

        // Agregar imagen a services
        Schema::table('services', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
