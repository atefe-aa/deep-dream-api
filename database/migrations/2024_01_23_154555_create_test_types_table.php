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
        Schema::create('test_types', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->nullable();
            $table->enum('gender',['male','female','both'])->default('both');
            $table->enum('type',['optical','fluorescent','invert'])->default('optical');
            $table->integer('num_layer')->default(1);
            $table->integer('step')->nullable();
            $table->integer('micro_step')->nullable();
            $table->integer('z_axis')->nullable();
            $table->integer('condenser')->nullable();
            $table->integer('brightness')->nullable();
            $table->integer('magnification');
            $table->string('description')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_types');
    }
};
