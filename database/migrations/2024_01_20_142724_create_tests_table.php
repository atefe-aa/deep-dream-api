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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_id');
            $table->string('sender_reg_num')->nullable();
            $table->integer('num_slide')->default(1);
            $table->integer('price')->default(0);
            $table->enum('status',['deleted','registered','scanning','scanned','failed','answered','approved'])
            ->default('registered');
            $table->text('description');
            $table->timestamps();

            $table->foreign('lab_id')->references('id')->on('laboratories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
