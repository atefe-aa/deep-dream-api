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
        Schema::table('test_types', function (Blueprint $table) {
            $table->unsignedBigInteger('report_template_id')
                ->nullable()
                ->default(null)
                ->after('id');

            $table->foreign('report_template_id')
                ->on('report_templates')
                ->references('id')
                ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('report_template_id');
        });
    }
};
