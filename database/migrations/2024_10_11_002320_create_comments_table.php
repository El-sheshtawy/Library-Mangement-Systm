<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Use mediumText for larger comment content
            $table->mediumText('content')->nullable();

            // Rating between 1 and 5, default 3
            $table->unsignedTinyInteger('rating')->default(3);

            // Foreign keys with indexes
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('book_id')
                ->constrained('books')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Status as unsignedTinyInteger for more flexibility
            $table->unsignedTinyInteger('status')->default(1); // 1 = active, 0 = deleted

            // Soft delete support instead of enum for better flexibility
            $table->softDeletes();

            // Timestamps
            $table->timestamps();

            // Indexes for optimization
            $table->index(['user_id', 'book_id']);  // Composite index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
