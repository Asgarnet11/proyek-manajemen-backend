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
        // Schema::create('tasks', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('project_id')->constrained()->onDelete('cascade');
        //     $table->string('title');
        //     $table->foreignId('user_id')->constrained();
        //     $table->string('status')->default('Belum Dimulai');
        //     $table->string('priority');
        //     $table->string('proof_file_path')->nullable();
        //     $table->timestamps();
        // });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // penanggung jawab
            $table->string('title');
            $table->enum('status', ['Belum Dimulai', 'Sedang Berjalan', 'Selesai'])->default('Belum Dimulai');
            $table->enum('priority', ['Rendah', 'Sedang', 'Tinggi'])->default('Sedang');
            $table->string('proof_file_path')->nullable(); // bukti penyelesaian
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
