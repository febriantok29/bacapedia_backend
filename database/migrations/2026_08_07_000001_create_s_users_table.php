<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('s_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_code', 20)->unique();
            $table->string('name', 255);
            $table->string('email', 128)->unique();
            $table->string('password', 255);
            $table->enum('role', ['Admin', 'Petugas', 'Anggota'])->default('Anggota');

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('created_by', 36)->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updated_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_users');
    }
};
