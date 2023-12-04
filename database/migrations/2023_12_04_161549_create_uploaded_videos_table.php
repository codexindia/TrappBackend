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
        Schema::create('uploaded_videos', function (Blueprint $table) {
            $table->id();
            $table->string('creator_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('privacy',['public','private']);
            $table->string('thumbnail');
            $table->string('video_loc');
            $table->enum('video_type',['normal','live'])->default('normal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_videos');
    }
};
