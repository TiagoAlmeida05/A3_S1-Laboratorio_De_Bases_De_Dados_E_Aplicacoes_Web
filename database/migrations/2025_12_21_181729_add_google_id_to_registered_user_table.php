<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registered_user', function (Blueprint $table) {
            // ID do Google
            $table->string('google_id')->nullable()->unique();
            // URL da foto de perfil do Google
            $table->string('avatar')->nullable();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registered_user', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
