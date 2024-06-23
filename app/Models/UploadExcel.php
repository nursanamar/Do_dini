<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class UploadExcel extends Model
{
    use HasFactory;
    protected $table = 'upload_excels';
    protected $fillable = [
        'id',
        'uuid',
        'id_masked',
        'kategori_sekolah',
        'jenis_kelamin',
        'jalur_seleksi',
        'ips1',
        'sks1',
        'ips2',
        'sks2',
        'ips3',
        'sks3',
        'ips4',
        'sks4',
        'akidah_akhlak',
        'algoritma_pemrograman',
        'bahasa_arab',
        'bahasa_indonesia',
        'bahasa_inggris',
        'basis_data',
        'elektronika',
        'etika_profesi',
        'fisika',
        'ilmu_al_quran',
        'ilmu_fikih',
        'ilmu_hadis',
        'interaksi_manusia_dan_komputer',
        'jaringan_komputer',
        'kecerdasan_buatan',
        'kepemimpinan_dan_teamwork',
        'kewirausahaan',
        'logika_informatika',
        'manajemen_proyek_teknologi_informasi',
        'manajemen_umum',
        'matematika_diskrit',
        'matematika_komputer',
        'matematika_komputer_dasar',
        'mikroprosesor',
        'organisasi_dan_arsitektur_komputer',
        'pemrograman_berorientasi_objek',
        'pemrograman_terstruktur',
        'pemrograman_visual',
        'pemrograman_web_2',
        'pend_pancasila_dan_kewarganegaraan',
        'pengantar_teknologi_informasi',
        'probabilitas_dan_statistik',
        'rekayasa_perangkat_lunak',
        'sejarah_peradaban_islam',
        'sistem_operasi_komputer',
        'struktur_data',
        'teknologi_dan_desain_web',
        'teknologi_informasi',
        'teknologi_multimedia_dan_game',
        'teori_bahasa_dan_automata',
        'class_status',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event listener untuk membuat UUID sebelum menyimpan
        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
