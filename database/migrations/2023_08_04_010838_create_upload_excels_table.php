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
        Schema::create('upload_excels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->timestamps();

            $table->string('id_masked')->nullable();
            $table->string('kategori_sekolah')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('jalur_seleksi')->nullable();
            $table->string('ips1')->nullable();
            $table->string('sks1')->nullable();
            $table->string('ips2')->nullable();
            $table->string('sks2')->nullable();
            $table->string('ips3')->nullable();
            $table->string('sks3')->nullable();
            $table->string('ips4')->nullable();
            $table->string('sks4')->nullable();
            $table->string('akidah_akhlak')->nullable();
            $table->string('algoritma_pemrograman')->nullable();
            $table->string('bahasa_arab')->nullable();
            $table->string('bahasa_indonesia')->nullable();
            $table->string('bahasa_inggris')->nullable();
            $table->string('basis_data')->nullable();
            $table->string('elektronika')->nullable();
            $table->string('etika_profesi')->nullable();
            $table->string('fisika')->nullable();
            $table->string('ilmu_al_quran')->nullable();
            $table->string('ilmu_fikih')->nullable();
            $table->string('ilmu_hadis')->nullable();
            $table->string('interaksi_manusia_dan_komputer')->nullable();
            $table->string('jaringan_komputer')->nullable();
            $table->string('kecerdasan_buatan')->nullable();
            $table->string('kepemimpinan_dan_teamwork')->nullable();
            $table->string('kewirausahaan')->nullable();
            $table->string('logika_informatika')->nullable();
            $table->string('manajemen_proyek_teknologi_informasi')->nullable();
            $table->string('manajemen_umum')->nullable();
            $table->string('matematika_diskrit')->nullable();
            $table->string('matematika_komputer')->nullable();
            $table->string('matematika_komputer_dasar')->nullable();
            $table->string('mikroprosesor')->nullable();
            $table->string('organisasi_dan_arsitektur_komputer')->nullable();
            $table->string('pemrograman_berorientasi_objek')->nullable();
            $table->string('pemrograman_terstruktur')->nullable();
            $table->string('pemrograman_visual')->nullable();
            $table->string('pemrograman_web_2')->nullable();
            $table->string('pend_pancasila_dan_kewarganegaraan')->nullable();
            $table->string('pengantar_teknologi_informasi')->nullable();
            $table->string('probabilitas_dan_statistik')->nullable();
            $table->string('rekayasa_perangkat_lunak')->nullable();
            $table->string('sejarah_peradaban_islam')->nullable();
            $table->string('sistem_operasi_komputer')->nullable();
            $table->string('struktur_data')->nullable();
            $table->string('teknologi_dan_desain_web')->nullable();
            $table->string('teknologi_informasi')->nullable();
            $table->string('teknologi_multimedia_dan_game')->nullable();
            $table->string('teori_bahasa_dan_automata')->nullable();
            $table->string('class_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_excels');
    }
};
