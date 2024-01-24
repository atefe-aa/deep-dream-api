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
        Schema::create('slides', static function (Blueprint $table) {
            $table->id();
            $table->integer('nth')->unique();
            $table->integer('bottom_left_x');
            $table->integer('bottom_left_y');
            $table->integer('top_right_x');
            $table->integer('top_right_y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
