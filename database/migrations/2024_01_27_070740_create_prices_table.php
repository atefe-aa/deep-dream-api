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
        Schema::create('prices', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('test_type_id');
            $table->integer('price')->default(0);
            $table->integer('price_per_slide')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('lab_id')->references('id')->on('laboratories')->onDelete('restrict');
            $table->foreign('test_type_id')->references('id')->on('test_types')->onDelete('restrict');

            $table->unique(['lab_id', 'test_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
