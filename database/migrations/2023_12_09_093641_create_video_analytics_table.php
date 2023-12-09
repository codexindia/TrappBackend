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
        Schema::create('video_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('creator_id');
            $table->string('user_id');
            $table->enum('action',['follow','like','dislike']);
            $table->json('attribute')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_analytics');
    }
};
