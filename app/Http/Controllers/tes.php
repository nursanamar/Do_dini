<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\UploadExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Phpml\Classification\NaiveBayes;
use Phpml\CrossValidation\RandomSplit;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\Metric\Accuracy;

class HasilController extends BaseController
{
    public function index()
    {
        $module = 'Deteksi Drop Out';
        return view('hasil.index', compact('module'));
    }

    public function predictDOStatus($params)
    {
        // Inisialisasi model Naive Bayes
        $naiveBayes = new NaiveBayes();

        // Ambil data dari database menggunakan model UploadExcel
        $data = UploadExcel::all();

        $nimToData = []; // Array untuk mengelompokkan data berdasarkan NIM

        // Loop melalui data dan kelompokkan berdasarkan NIM
        foreach ($data as $row) {
            $nim = $row->nim; // NIM
            $ipk = $row->ipk; // IPK
            $semester = $row->semester; // Semester

            if (!isset($nimToData[$nim])) {
                // Inisialisasi data NIM jika belum ada
                $nimToData[$nim] = [
                    'nim' => $nim,
                    'total_ipk' => 0,
                    'total_semester' => 0,
                    'data' => [],
                ];
            }

            // Tambahkan data IPK dan Semester ke dalam data NIM yang sesuai
            $nimToData[$nim]['total_ipk'] += $ipk;
            $nimToData[$nim]['total_semester']++;
            $nimToData[$nim]['data'][] = [
                'nama' => $row->nama, // Nama
                'ipk' => $ipk, // IPK
                'semester' => $semester, // Semester
            ];
        }

        $samples = [];
        $labels = [];
        $predictedData = []; // Array untuk menyimpan data yang terdeteksi DO

        // Loop melalui data NIM yang sudah dikelompokkan
        foreach ($nimToData as $nimData) {
            $nim = $nimData['nim'];
            $totalIpk = $nimData['total_ipk'];
            $totalSemester = $nimData['total_semester'];

            // Hitung rata-rata IPK berdasarkan jumlah semester
            $averageIpk = $totalIpk / $totalSemester;

            $label = ($averageIpk < 2) ? 'DO' : 'Tidak DO';

            // Tambahkan data ke samples dan labels
            foreach ($nimData['data'] as $data) {
                $samples[] = [$nim, $data['nama'], $totalSemester, $averageIpk]; // Tambahkan NIM, Nama, dan Semester ke samples
                $labels[] = $label;
            }
        }

        // Buat dataset
        $dataset = new ArrayDataset($samples, $labels);

        // Lanjutkan dengan menggunakan $percentage seperti biasa
        $split = new StratifiedRandomSplit($dataset, 0.5);

        // dd($split);
        $accuracies = [];

        foreach ($split->getTrainSamples() as $trainIndexes) {
            $trainSamples = $dataset->getSamples($trainIndexes);
            $trainLabels = $dataset->getTargets($trainIndexes);

            // Gunakan data uji yang tidak pernah digunakan dalam pelatihan
            $testIndexes = array_diff(range(0, count($samples) - 1), $trainIndexes);
            $testSamples = $dataset->getSamples($testIndexes);
            $testLabels = $dataset->getTargets($testIndexes);

            // Latih model pada data pelatihan
            $naiveBayes->train($trainSamples, $trainLabels);

            // Lakukan prediksi pada data uji
            $predictedLabels = $naiveBayes->predict($testSamples);

            // Loop melalui hasil prediksi
            foreach ($testIndexes as $index) {
                // Data ini terdeteksi sebagai DO, simpan data ke dalam array predictedData
                // if ($predictedLabels[$index] === 'DO') {
                $predictedData[] = [
                    'nim' => $samples[$index][0], // NIM dari data terdeteksi DO
                    'nama' => $samples[$index][1], // Nama dari data terdeteksi DO
                    'semester' => $samples[$index][2], // Semester dari data terdeteksi DO
                    'ipk' => $samples[$index][3], // IPK dari data terdeteksi DO
                    'label' => $labels[$index] // Label DO
                ];
                // }
            }

            // Hitung akurasi
            $accuracy = Accuracy::score($testLabels, $predictedLabels);
            $accuracies[] = $accuracy;
        }

        $meanAccuracy = array_sum($accuracies) / count($accuracies);


        // Tampilkan hasil akurasi rata-rata dan data yang terdeteksi DO

        $predictedData = collect($predictedData); // Konversi $predictedData menjadi koleksi

        $groupedMahasiswa = $predictedData->groupBy('nim')->map(function ($group) {
            $mahasiswa = $group->first();

            // Inisialisasi nilai IPK semester
            $ipkSemester = [];

            // Loop untuk mengisi nilai IPK semester
            foreach ($group as $data) {
                $semesterKey = 'semester_' . $data['semester'];
                $ipkSemester[$semesterKey] = $data['ipk'];
            }

            // Tambahkan IPK semester ke data mahasiswa
            $mahasiswa = array_merge($mahasiswa, $ipkSemester);

            $mahasiswa['keterangan'] = ($mahasiswa['label'] === 'DO') ? 'DO' : 'Tidak DO';

            return $mahasiswa;
        });

        return $this->sendResponse($groupedMahasiswa->values(), 'Cross-validation completed');
    }

    public function getAcuracy($params)
    {
        // Inisialisasi model Naive Bayes
        $naiveBayes = new NaiveBayes();

        // Ambil data dari database menggunakan model UploadExcel
        $data = UploadExcel::all();

        $nimToData = []; // Array untuk mengelompokkan data berdasarkan NIM

        // Loop melalui data dan kelompokkan berdasarkan NIM
        foreach ($data as $row) {
            $nim = $row->nim; // NIM
            $ipk = $row->ipk; // IPK
            $semester = $row->semester; // Semester

            if (!isset($nimToData[$nim])) {
                // Inisialisasi data NIM jika belum ada
                $nimToData[$nim] = [
                    'nim' => $nim,
                    'total_ipk' => 0,
                    'total_semester' => 0,
                    'data' => [],
                ];
            }

            // Tambahkan data IPK dan Semester ke dalam data NIM yang sesuai
            $nimToData[$nim]['total_ipk'] += $ipk;
            $nimToData[$nim]['total_semester']++;
            $nimToData[$nim]['data'][] = [
                'nama' => $row->nama, // Nama
                'ipk' => $ipk, // IPK
                'semester' => $semester, // Semester
            ];
        }

        $samples = [];
        $labels = [];
        $predictedData = []; // Array untuk menyimpan data yang terdeteksi DO

        // Loop melalui data NIM yang sudah dikelompokkan
        foreach ($nimToData as $nimData) {
            $nim = $nimData['nim'];
            $totalIpk = $nimData['total_ipk'];
            $totalSemester = $nimData['total_semester'];

            // Hitung rata-rata IPK berdasarkan jumlah semester
            $averageIpk = $totalIpk / $totalSemester;

            $label = ($averageIpk < 2) ? 'DO' : 'Tidak DO';

            // Tambahkan data ke samples dan labels
            foreach ($nimData['data'] as $data) {
                $samples[] = [$nim, $data['nama'], $totalSemester, $averageIpk]; // Tambahkan NIM, Nama, dan Semester ke samples
                $labels[] = $label;
            }
        }

        // Buat dataset
        $dataset = new ArrayDataset($samples, $labels);

        $crossValidationValue = $params;
        // Mengonversi ke float
        $percentage = floatval($crossValidationValue) / 100.0;

        // Verifikasi bahwa nilai berada dalam rentang yang valid
        if ($percentage <= 0.0 || $percentage >= 1.0) {
            // Nilai tidak valid, berikan respons atau tindakan yang sesuai
            return $this->sendError('Invalid cross-validation percentage');
        }

        // Lanjutkan dengan menggunakan $percentage seperti biasa
        $split = new StratifiedRandomSplit($dataset, $percentage);

        // dd($split);
        $accuracies = [];

        foreach ($split->getTrainSamples() as $trainIndexes) {
            $trainSamples = $dataset->getSamples($trainIndexes);
            $trainLabels = $dataset->getTargets($trainIndexes);

            // Gunakan data uji yang tidak pernah digunakan dalam pelatihan
            $testIndexes = array_diff(range(0, count($samples) - 1), $trainIndexes);
            $testSamples = $dataset->getSamples($testIndexes);
            $testLabels = $dataset->getTargets($testIndexes);

            // Latih model pada data pelatihan
            $naiveBayes->train($trainSamples, $trainLabels);

            // Lakukan prediksi pada data uji
            $predictedLabels = $naiveBayes->predict($testSamples);

            // Loop melalui hasil prediksi
            foreach ($testIndexes as $index) {
                // Data ini terdeteksi sebagai DO, simpan data ke dalam array predictedData
                // if ($predictedLabels[$index] === 'DO') {
                $predictedData[] = [
                    'nim' => $samples[$index][0], // NIM dari data terdeteksi DO
                    'nama' => $samples[$index][1], // Nama dari data terdeteksi DO
                    'semester' => $samples[$index][2], // Semester dari data terdeteksi DO
                    'ipk' => $samples[$index][3], // IPK dari data terdeteksi DO
                    'label' => $labels[$index] // Label DO
                ];
                // }
            }

            // Hitung akurasi
            $accuracy = Accuracy::score($testLabels, $predictedLabels);
            $accuracies[] = $accuracy;
        }

        $meanAccuracy = (array_sum($accuracies) / count($accuracies)) * 100;

        return $this->sendResponse($meanAccuracy, 'Accuracy processed successfully');
    }
}


// Latih model Naive Bayes pada data pelatihan
$naiveBayes = new NaiveBayes();
$naiveBayes->train($trainSamples, $trainLabels);

// Lakukan prediksi pada data uji
$predictedLabels = $naiveBayes->predict($testSamples);

// Hitung akurasi
$accuracy = Accuracy::score($testLabels, $predictedLabels);
$accuracies[] = $accuracy;

// Hitung rata-rata akurasi dari semua iterasi
$meanAccuracy = array_sum($accuracies) / count($accuracies);

// Sekarang Anda dapat menggunakan $naiveBayes untuk melakukan prediksi pada data baru:
$newData = [...]; // Gantilah dengan data yang ingin Anda prediksi
$predictedLabel = $naiveBayes->predict($newData);

// $predictedLabel akan berisi prediksi kelas untuk data baru.

// Selanjutnya, Anda dapat menggunakan model $naiveBayes untuk membuat prediksi kelas pada data lain.

// Untuk hasil akhir, Anda dapat mengembalikan akurasi atau prediksi sesuai kebutuhan Anda dalam metodenya.
