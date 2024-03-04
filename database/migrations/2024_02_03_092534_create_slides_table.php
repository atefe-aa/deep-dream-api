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
        Schema::create('slides', static function (Blueprint $table) {
            $table->id();
            $table->integer('nth')->unique();
            $table->float('sw_x');
            $table->float('sw_y');
            $table->float('ne_x');
            $table->float('ne_y');
            $table->timestamps();
            
            $table->unique(['sw_x', 'sw_y', 'ne_x', 'ne_y']);
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
