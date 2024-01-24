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
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('test_type_id');
            $table->string('sender_register_code')->nullable();
            $table->string('doctor_name')->nullable();
            $table->integer('price')->default(0);
            $table->enum(
                'status',
                ['registered','scanning','scanned','failed','answered','approved','suspended'])
                ->default('registered'
                );
            $table->integer('num_slide')->default(1);
            $table->integer('duration')->nullable();
            $table->text('description');
            $table->timestamps();
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
