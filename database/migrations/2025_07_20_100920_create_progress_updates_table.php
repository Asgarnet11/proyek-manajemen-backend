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
        Schema::create('progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('percentage'); // Persentase progress (0-100)
            $table->text('notes')->nullable(); // Catatan atau update per pekerjaan
            $table->string('photo_path')->nullable(); // Foto lapangan
            $table->foreignId('created_by')->constrained('users'); // Siapa yang mengupdate
            $table->date('update_date'); // Tanggal update progress
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_updates');
    }
};
