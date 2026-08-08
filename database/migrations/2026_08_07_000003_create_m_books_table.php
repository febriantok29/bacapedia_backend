<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('book_code', 20)->unique();
            $table->uuid('category_id');
            $table->string('title', 255);
            $table->string('author', 255);
            $table->string('publisher', 255);
            $table->integer('published_year');
            $table->integer('stock')->default(0);

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('created_by', 36)->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updated_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 36)->nullable();

            $table->foreign('category_id')->references('id')->on('m_categories')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_books');
    }
};
