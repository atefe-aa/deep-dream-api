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
        Schema::create('regions', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scan_id');
            $table->json('coordinates'); /* ['sw' => ['x'=>0,'y'=>0], 'ne' => ['x'=>25,'y'=>75]] */
            $table->text('image')->nullable();
            $table->unsignedBigInteger('cytomine_image_id')->nullable();
            $table->bigInteger('estimated_duration')->nullable()->default(null);
            $table->bigInteger('duration')->nullable()->default(null);
            $table->enum('status', ['ready', 'failed', 'scanning', 'scanned', 'image-ready'])->default('ready');
            $table->timestamps();

            $table->foreign('scan_id')->on('scans')->onDelete('cascade')->references('id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
