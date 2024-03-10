<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id');
            $table->unsignedBigInteger('user_id');
            $table->json('data');
            $table->timestamps();

            $table->foreign('test_id')->on('tests')->references('id')->onDelete('restrict');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
