<?php

namespace App\Imports;

use App\Models\UploadExcel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        return new UploadExcel([
            'id_masked' => $row['id_masked'],
            'kategori_sekolah' => $row['kategori_sekolah'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'jalur_seleksi' => $row['jalur_seleksi'],
            'ips1' => $row['ips1'],
            'sks1' => $row['sks1'],
            'ips2' => $row['ips2'],
            'sks2' => $row['sks2'],
            'ips3' => $row['ips3'],
            'sks3' => $row['sks3'],
            'ips4' => $row['ips4'],
            'sks4' => $row['sks4'],
            'akidah_akhlak' => $row['akidah_akhlak'],
            'algoritma_pemrograman' => $row['algoritma_pemrograman'],
            'bahasa_arab' => $row['bahasa_arab'],
            'bahasa_indonesia' => $row['bahasa_indonesia'],
            'bahasa_inggris' => $row['bahasa_inggris'],
            'basis_data' => $row['basis_data'],
            'elektronika' => $row['elektronika'],
            'etika_profesi' => $row['etika_profesi'],
            'fisika' => $row['fisika'],
            'ilmu_al_quran' => $row['ilmu_al_quran'],
            'ilmu_fikih' => $row['ilmu_fikih'],
            'ilmu_hadis' => $row['ilmu_hadis'],
            'interaksi_manusia_dan_komputer' => $row['interaksi_manusia_dan_komputer'],
            'jaringan_komputer' => $row['jaringan_komputer'],
            'kecerdasan_buatan' => $row['kecerdasan_buatan'],
            'kepemimpinan_dan_teamwork' => $row['kepemimpinan_dan_teamwork'],
            'kewirausahaan' => $row['kewirausahaan'],
            'logika_informatika' => $row['logika_informatika'],
            'manajemen_proyek_teknologi_informasi' => $row['manajemen_proyek_teknologi_informasi'],
            'manajemen_umum' => $row['manajemen_umum'],
            'matematika_diskrit' => $row['matematika_diskrit'],
            'matematika_komputer' => $row['matematika_komputer'],
            'matematika_komputer_dasar' => $row['matematika_komputer_dasar'],
            'mikroprosesor' => $row['mikroprosesor'],
            'organisasi_dan_arsitektur_komputer' => $row['organisasi_dan_arsitektur_komputer'],
            'pemrograman_berorientasi_objek' => $row['pemrograman_berorientasi_objek'],
            'pemrograman_terstruktur' => $row['pemrograman_terstruktur'],
            'pemrograman_visual' => $row['pemrograman_visual'],
            'pemrograman_web_2' => $row['pemrograman_web_2'],
            'pend_pancasila_dan_kewarganegaraan' => $row['pend_pancasila_dan_kewarganegaraan'],
            'pengantar_teknologi_informasi' => $row['pengantar_teknologi_informasi'],
            'probabilitas_dan_statistik' => $row['probabilitas_dan_statistik'],
            'rekayasa_perangkat_lunak' => $row['rekayasa_perangkat_lunak'],
            'sejarah_peradaban_islam' => $row['sejarah_peradaban_islam'],
            'sistem_operasi_komputer' => $row['sistem_operasi_komputer'],
            'struktur_data' => $row['struktur_data'],
            'teknologi_dan_desain_web' => $row['teknologi_dan_desain_web'],
            'teknologi_informasi' => $row['teknologi_informasi'],
            'teknologi_multimedia_dan_game' => $row['teknologi_multimedia_dan_game'],
            'teori_bahasa_dan_automata' => $row['teori_bahasa_dan_automata'],
            'class_status' => $row['class_status'],
        ]);
    }
}
