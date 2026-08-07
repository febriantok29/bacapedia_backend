<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('s_error_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('error_code', 20);
            $table->text('message');
            $table->longText('stack_trace')->nullable();
            $table->string('user_id', 36)->nullable();
            $table->string('endpoint', 255);
            $table->string('http_method', 10);
            $table->text('request_body')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('error_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('s_error_logs');
    }
};
