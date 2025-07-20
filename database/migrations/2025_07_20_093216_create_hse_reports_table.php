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

        Schema::create('hse_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('report_type'); // enum-like: 'Inspeksi Rutin', 'Laporan Kecelakaan', 'Laporan Pelanggaran'
            $table->text('description');
            $table->text('findings')->nullable();
            $table->text('corrective_action')->nullable();
            $table->string('documentation_path')->nullable(); // File foto/dokumen
            $table->foreignId('reported_by')->constrained('users');
            $table->date('report_date');
            $table->timestamps();

            // Opsional: indeks untuk query cepat
            $table->index('report_type');
            $table->index('report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hse_reports');
    }
};
