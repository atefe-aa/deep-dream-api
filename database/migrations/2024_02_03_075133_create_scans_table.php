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
        Schema::create('scans', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id');
            $table->integer('slide_number');
            $table->json('slide_coordinates'); /* ['nw' => ['x'=>0,'y'=>0], 'se' => ['x'=>25,'y'=>75]] */
            $table->text('slide_image')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->unique(['test_id', 'slide_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
