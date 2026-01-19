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
        Schema::create('monasteries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['monastery', 'building'])->default('monastery');
            $table->string('monastery_name')->nullable(); // For buildings, link to parent monastery
            $table->integer('monks')->default(0);
            $table->integer('novices')->default(0);
            $table->integer('total')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monasteries');
    }
};

