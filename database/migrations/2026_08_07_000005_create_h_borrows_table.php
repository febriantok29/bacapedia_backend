<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('h_borrows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('borrow_id');
            $table->enum('status', ['Aktif', 'Dikembalikan', 'Terlambat']);
            $table->text('remarks')->nullable();

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('created_by', 36)->nullable();

            $table->foreign('borrow_id')->references('id')->on('t_borrows')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('h_borrows');
    }
};
