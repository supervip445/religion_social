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
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->string('viewable_type'); // App\Models\Post, App\Models\Dhamma, etc.
            $table->unsignedBigInteger('viewable_id');
            $table->string('ip_address', 45); // IPv6 can be up to 45 characters
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Index for efficient queries
            $table->index(['viewable_type', 'viewable_id']);
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};

