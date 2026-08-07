<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('s_config', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 255);
            $table->string('value', 64)->nullable();
            $table->date('active_start_date')->nullable();
            $table->date('active_end_date')->nullable();

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('created_by', 36)->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updated_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_config');
    }
};
