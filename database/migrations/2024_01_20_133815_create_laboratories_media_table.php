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
        Schema::create('laboratories_media', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->text('avatar')->nullable();
            $table->text('signature')->nullable();
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->timestamps();

            $table->foreign('lab_id')->references('id')->on('laboratories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories_media');
    }
};
