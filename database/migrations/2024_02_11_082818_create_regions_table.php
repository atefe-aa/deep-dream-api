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
            $table->enum('status', ['ready', 'failed', 'stopped', 'scanning', 'scanned'])->default('ready');
            $table->timestamps();
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
