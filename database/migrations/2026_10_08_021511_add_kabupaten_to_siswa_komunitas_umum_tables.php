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
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('kabupaten')->nullable()->after('sekolah')->index();
        });

        Schema::table('komunitas', function (Blueprint $table) {
            $table->string('kabupaten')->nullable()->after('nama_komunitas')->index();
        });

        Schema::table('umum', function (Blueprint $table) {
            $table->string('kabupaten')->nullable()->after('pekerjaan')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex(['kabupaten']);
            $table->dropColumn('kabupaten');
        });

        Schema::table('komunitas', function (Blueprint $table) {
            $table->dropIndex(['kabupaten']);
            $table->dropColumn('kabupaten');
        });

        Schema::table('umum', function (Blueprint $table) {
            $table->dropIndex(['kabupaten']);
            $table->dropColumn('kabupaten');
        });
    }
};
