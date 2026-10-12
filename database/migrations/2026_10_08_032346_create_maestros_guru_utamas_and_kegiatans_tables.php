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
        Schema::create('maestros', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('bidang_seni');
            $table->string('kabupaten')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('kontak')->nullable();
            $table->timestamps();
        });

        Schema::create('guru_utamas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('nuptk')->nullable();
            $table->string('nip')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('kabupaten')->index();
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });

        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->string('kabupaten')->index();
            $table->string('tempat')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status')->default('berlangsung');
            $table->timestamps();
        });

        Schema::create('kegiatan_maestro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            $table->foreignId('maestro_id')->constrained('maestros')->onDelete('cascade');
            $table->string('peran')->nullable();
            $table->timestamps();
        });

        Schema::create('kegiatan_guru_utama', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            $table->foreignId('guru_utama_id')->constrained('guru_utamas')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->foreignId('guru_utama_id')->nullable()->after('nama_guru_utama')->constrained('guru_utamas')->nullOnDelete();
            $table->foreignId('kegiatan_id')->nullable()->after('guru_utama_id')->constrained('kegiatans')->nullOnDelete();
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('guru_utama_id')->nullable()->after('alamat_sekolah')->constrained('guru_utamas')->nullOnDelete();
            $table->string('nama_guru_utama')->nullable()->after('guru_utama_id');
            $table->foreignId('kegiatan_id')->nullable()->after('nama_guru_utama')->constrained('kegiatans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['kegiatan_id']);
            $table->dropForeign(['guru_utama_id']);
            $table->dropColumn(['kegiatan_id', 'nama_guru_utama', 'guru_utama_id']);
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropForeign(['kegiatan_id']);
            $table->dropForeign(['guru_utama_id']);
            $table->dropColumn(['kegiatan_id', 'guru_utama_id']);
        });

        Schema::dropIfExists('kegiatan_guru_utama');
        Schema::dropIfExists('kegiatan_maestro');
        Schema::dropIfExists('kegiatans');
        Schema::dropIfExists('guru_utamas');
        Schema::dropIfExists('maestros');
    }
};
