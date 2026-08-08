<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_borrows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('book_id');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['Aktif', 'Dikembalikan', 'Terlambat'])->default('Aktif');
            $table->decimal('penalty', 10, 2)->default(0);

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('created_by', 36)->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updated_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('user_id')->references('id')->on('s_users')->onDelete('restrict');
            $table->foreign('book_id')->references('id')->on('m_books')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_borrows');
    }
};
