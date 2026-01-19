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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->morphs('likeable'); // likeable_id, likeable_type (posts, dhammas, etc.)
            $table->string('user_identifier')->nullable(); // IP address or session ID for anonymous users
            $table->enum('type', ['like', 'dislike'])->default('like');
            $table->timestamps();
            
            // Prevent duplicate likes from same user on same item
            $table->unique(['likeable_id', 'likeable_type', 'user_identifier'], 'unique_like');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};

