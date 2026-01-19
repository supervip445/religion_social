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
            $table->morphs('commentable'); // commentable_id, commentable_type (posts, dhammas, etc.)
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('comment');
            $table->string('user_identifier')->nullable(); // IP address or session ID
            $table->boolean('is_approved')->default(true); // Admin can moderate comments
            $table->timestamps();
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

