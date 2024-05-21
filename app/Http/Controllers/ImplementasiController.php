<?php

namespace App\Http\Controllers;

use App\Imports\ImplementasiImport;
use App\Models\Implementasi;
use App\Models\UploadExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use Phpml\Classification\NaiveBayes;
use Phpml\CrossValidation\RandomSplit;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\Metric\Accuracy;

class ImplementasiController extends BaseController
{
    public function show()
    {
        $data = Implementasi::all();
        return $this->sendResponse($data, 'Implementasi processed successfully');
    }

    public function index()
    {
        $module = 'Implementasi';
        return view('implementasi.index', compact('module'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'file_excel' => 'nullable|mimes:xls,xlsx,csv|max:20048', // Batas ukuran file diatur menjadi 2MB (sesuaikan sesuai kebutuhan)
            ]);

            if ($request->hasFile('file_excel')) {
                $file = $request->file('file_excel');

                // Generate unique filename
                $filename = time() . '_' . $file->getClientOriginalName();

                // Store the file in the 'upload_excels' directory within the storage disk
                $filePath = $file->storeAs('implementasis', $filename);

                // Import data from the Excel file
                $data = new ImplementasiImport();
                Excel::import($data, Storage::disk('local')->path($filePath));

                // Impor berhasil jika tidak ada exception yang dilempar
                return $this->sendResponse($data, 'Excel data uploaded and saved successfully');
            } else {
                return $this->sendError('No Excel file uploaded.', 'No Excel file uploaded.', 200);
            }
        } catch (\Exception $e) {
            return $this->sendError('Error uploading and saving Excel: ' . $e->getMessage(), $e->getMessage(), 200);
        }
    }

    public function predict()
    {
        // Ambil data dari database menggunakan model UploadExcel
        $data = UploadExcel::all()->toArray();
        $labelAttribute = 'class_status'; // Atur atribut label yang sesuai

        $samples = [];
        $labels = [];

        // Membuat array kolom yang akan digunakan
        $columnsToUse = [
            'id_masked', 'kategori_sekolah', 'jenis_kelamin', 'jalur_seleksi',
            'ips1', 'sks1', 'ips2', 'sks2', 'ips3', 'sks3', 'ips4', 'sks4',
            'akidah_akhlak', 'algoritma_pemrograman', 'bahasa_arab', 'bahasa_indonesia',
            'bahasa_inggris', 'basis_data', 'elektronika', 'etika_profesi', 'fisika',
            'ilmu_al_quran', 'ilmu_fikih', 'ilmu_hadis', 'interaksi_manusia_dan_komputer',
            'jaringan_komputer', 'kecerdasan_buatan', 'kepemimpinan_dan_teamwork',
            'kewirausahaan', 'logika_informatika', 'manajemen_proyek_teknologi_informasi',
            'manajemen_umum', 'matematika_diskrit', 'matematika_komputer',
            'matematika_komputer_dasar', 'mikroprosesor', 'organisasi_dan_arsitektur_komputer',
            'pemrograman_berorientasi_objek', 'pemrograman_terstruktur', 'pemrograman_visual',
            'pemrograman_web_2', 'pend_pancasila_dan_kewarganegaraan',
            'pengantar_teknologi_informasi', 'probabilitas_dan_statistik',
            'rekayasa_perangkat_lunak', 'sejarah_peradaban_islam', 'sistem_operasi_komputer',
            'struktur_data', 'teknologi_dan_desain_web', 'teknologi_informasi',
            'teknologi_multimedia_dan_game', 'teori_bahasa_dan_automata'
        ];

        foreach ($data as $row) {
            // Filter hanya kolom yang diperlukan
            $sample = [];
            foreach ($columnsToUse as $attribute) {
                // Gantilah nilai null dengan tanda strip "-"
                $sample[] = $row[$attribute] ?? '-';
            }
            $samples[] = $sample;
            $labels[] = $row[$labelAttribute];
        }

        $dataset = new ArrayDataset($samples, $labels);

        // Lakukan pemisahan data dengan StratifiedRandomSplit
        $split = new StratifiedRandomSplit($dataset, 0.5);

        // Ambil data pelatihan dan data uji
        $trainSamples = $split->getTrainSamples();
        $trainLabels = $split->getTrainLabels();
        $testSamples = $split->getTestSamples();
        $testLabels = $split->getTestLabels();

        $naiveBayes = new \Phpml\Classification\NaiveBayes();
        $naiveBayes->train($trainSamples, $trainLabels);

        // Lakukan prediksi pada data uji
        $predictedLabels = $naiveBayes->predict($testSamples);

        // Ambil data baru untuk prediksi setelah model dilatih
        $newData = Implementasi::all()->toArray();
        $samplesNew = [];

        foreach ($newData as $row) {
            $sampleNew = [];
            foreach ($columnsToUse as $attribute) {
                // Gantilah nilai null dengan tanda strip "-"
                $sampleNew[] = $row[$attribute] ?? '-';
            }
            $samplesNew[] = $sampleNew;
        }

        $predictedNewLabels = $naiveBayes->predict($samplesNew);

        foreach ($newData as $key => $row) {
            $newData[$key]['predicted_label'] = $predictedNewLabels[$key];
        }

        // Konversi $newData untuk format respons
        $samples_training = [];
        $labels_training = [];

        foreach ($newData as $row) {
            $sample = [];
            foreach ($columnsToUse as $attribute) {
                // Gantilah nilai null dengan tanda strip "-"
                $sample[] = $row[$attribute] ?? '-';
            }
            $samples_training[] = $sample;
            $labels_training[] = $row['predicted_label'];
        }

        $dataset_training = new ArrayDataset($samples_training, $labels_training);

        // Lakukan pemisahan data dengan StratifiedRandomSplit untuk pelatihan
        $split_training = new StratifiedRandomSplit($dataset_training, 0.8);

        $sample_data = [];

        foreach ($split_training->getTrainSamples() as $trainSample) {
            $sample_data[] = array_combine($columnsToUse, $trainSample);
        }

        foreach ($split_training->getTestSamples() as $index => $testSample) {
            $sample_data[] = array_combine($columnsToUse, $testSample);
            $sample_data[$index]['predicted_label'] = $split_training->getTestLabels()[$index];
        }

        return $this->sendResponse($sample_data, 'Prediction and accuracy processed successfully');
    }

    public function delete()
    {
        // Gantilah dengan logika penghapusan yang sesuai untuk aplikasi Anda
        // Sebagai contoh, kita akan menghapus semua data dari tabel implementasis
        DB::table('implementasis')->delete();

        // Redirect atau berikan respons sesuai kebutuhan
        return redirect()->back();
    }

    public function hasilImplementasi()
    {
        $module = 'Hasil Uji Data';
        return view('implementasi.hasil', compact('module'));
    }
}
