<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('s_password_resets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('guard', 10);
            $table->string('user_id', 36);
            $table->string('token', 64)->unique();
            $table->boolean('used')->default(false);
            $table->timestamp('expires_at');

            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_password_resets');
    }
};
