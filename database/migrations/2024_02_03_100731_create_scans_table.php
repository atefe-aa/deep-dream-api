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
            $table->integer('slide_number')->nullable()->default(1);
            $table->json('slide_coordinates'); /* ['sw' => ['x'=>0,'y'=>0], 'ne' => ['x'=>25,'y'=>75]] */
            $table->enum('status', ['ready', 'failed', 'stopped', 'scanning', 'scanned', '2x-scanned'])->default('ready');
            $table->integer('slide_number')->nullable()->default(null);
            $table->text('slide_image')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
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
