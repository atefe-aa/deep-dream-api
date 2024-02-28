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
            $table->integer('nth_slide');
            $table->unsignedBigInteger('test_id')->nullable()->default(0);
            $table->integer('slide_number')->nullable()->default(1);
            $table->bigInteger('estimated_duration')->nullable()->default(null);
            $table->bigInteger('duration')->nullable()->default(null);
            $table->json('slide_coordinates'); /* ['sw' => ['x'=>0,'y'=>0], 'ne' => ['x'=>25,'y'=>75]] */
            $table->enum('status', ['ready', 'failed', 'scanning', 'scanned', '2x-scanned', '2x-failed', '2x-image-ready', 'image-ready'])->default('ready');
            $table->boolean('is_processing')->default(true);
            $table->text('slide_image')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
            $table->foreign('test_id')->on('tests')->onDelete('cascade')->references('id');
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
