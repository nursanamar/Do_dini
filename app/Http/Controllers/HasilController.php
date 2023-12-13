<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\Hasil;
use App\Models\UploadExcel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Phpml\Classification\NaiveBayes;
use Phpml\CrossValidation\RandomSplit;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\Metric\Accuracy;
use Phpml\FeatureExtraction\InformationGain;
use Phpml\CrossValidation\Split;

class HasilController extends BaseController
{
    public function index()
    {
        $module = 'Deteksi Drop Out';
        return view('hasil.index', compact('module'));
    }

    public function entropy($data)
    {
        $total = count($data);
        $classes = array_count_values($data);
        $entropy = 0;

        foreach ($classes as $class) {
            $probability = $class / $total;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }

    public function informationGain($dataset, $attribute, $labelAttribute)
    {
        $label = array_column($dataset, $labelAttribute); // Ubah ini menjadi array
        $totalEntropy = $this->entropy($label);
        $uniqueValues = array_unique(array_column($dataset, $attribute));
        $weightedEntropy = 0;

        foreach ($uniqueValues as $value) {
            $subset = array_filter($dataset, function ($data) use ($attribute, $value) {
                return $data[$attribute] == $value;
            });
            $subsetLabels = array_column($subset, $labelAttribute); // Ubah ini menjadi array
            $subsetEntropy = $this->entropy($subsetLabels);
            $weight = count($subset) / count($dataset);
            $weightedEntropy += $weight * $subsetEntropy;
        }

        return $totalEntropy - $weightedEntropy;
    }

    public function predictDOStatus($informationGain_params, $crosValidation_params)
    {
        // // Ambil data dari database menggunakan model UploadExcel
        // $data = UploadExcel::all()->toArray();

        // $labelAttribute = 'class_status'; // Atur atribut label yang sesuai

        // $informationGains = [];

        // // Menghitung Information Gain untuk setiap atribut
        // $attributes = array_keys($data[0]); // Mengambil atribut dari model pertama
        // $labelAttributeIndex = array_search($labelAttribute, $attributes);

        // if ($labelAttributeIndex !== false) {
        //     unset($attributes[$labelAttributeIndex]); // Hapus atribut label dari array atribut
        // }

        // // Mengabaikan atribut 'id' dan 'uuid'
        // $ignoredAttributes = ['id', 'uuid', 'created_at', 'updated_at'];
        // $attributes = array_diff($attributes, $ignoredAttributes);

        // // Tentukan batas jumlah nilai null yang dapat diterima
        // $nullThreshold = 0.2; // 20%

        // foreach ($attributes as $attribute) {
        //     // Hitung jumlah nilai null dalam atribut
        //     $nullCount = 0;
        //     foreach ($data as $row) {
        //         if ($row[$attribute] === null || $row[$attribute] === '') {
        //             $nullCount++;
        //         }
        //     }

        //     // Hitung Information Gain hanya jika jumlah nilai null tidak melebihi batas
        //     if ($nullCount <= count($data) * $nullThreshold) {
        //         $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
        //         $informationGains[$attribute] = $informationGain;
        //     }
        // }

        // // Atribut dengan Information Gain tertinggi
        // arsort($informationGains);

        // // Mengambil N atribut teratas sesuai dengan nilai $params
        // $topNInformationGains = array_slice($informationGains, 0, $informationGain_params);

        // // Ambil atribut dari N teratas
        // $selectedAttributes = array_keys($topNInformationGains);

        // $samples = [];

        // foreach ($data as $row) {
        //     $sample = [];
        //     foreach ($selectedAttributes as $attribute) {
        //         // Gantilah nilai null dengan tanda strip "-"
        //         $sample[$attribute] = $row[$attribute] ?? '-';
        //     }

        //     $sample['label'] = $row[$labelAttribute];

        //     $samples[] = $sample;
        // }

        // Ambil data dari database menggunakan model UploadExcel
        $data = UploadExcel::all()->toArray();

        $labelAttribute = 'class_status'; // Atur atribut label yang sesuai

        $informationGains = [];

        // Menghitung Information Gain untuk setiap atribut
        $attributes = array_keys($data[0]); // Mengambil atribut dari model pertama
        $labelAttributeIndex = array_search($labelAttribute, $attributes);

        if ($labelAttributeIndex !== false) {
            unset($attributes[$labelAttributeIndex]); // Hapus atribut label dari array atribut
        }

        // Mengabaikan atribut 'id' dan 'uuid'
        $ignoredAttributes = ['id', 'uuid', 'created_at', 'updated_at'];
        $attributes = array_diff($attributes, $ignoredAttributes);

        // Tentukan batas jumlah nilai null yang dapat diterima
        $nullThreshold = 0.2; // 80%

        foreach ($attributes as $attribute) {
            // Hitung jumlah nilai null dalam atribut
            $nullCount = 0;
            foreach ($data as $row) {
                if ($row[$attribute] === null || $row[$attribute] === '') {
                    $nullCount++;
                }
            }

            // Hitung Information Gain hanya jika jumlah nilai null tidak melebihi batas
            if ($nullCount <= count($data) * $nullThreshold) {
                $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
                $informationGains[$attribute] = $informationGain;
            }
        }

        // Atribut dengan Information Gain tertinggi
        arsort($informationGains);

        // Mengambil N atribut teratas sesuai dengan nilai $params
        $topNInformationGains = array_slice($informationGains, 0, $informationGain_params);

        // Ambil atribut dari N teratas
        $selectedAttributes = array_keys($topNInformationGains);

        $samples = [];
        $labels = [];

        foreach ($data as $row) {
            $sample = [];
            foreach ($selectedAttributes as $attribute) {
                // Gantilah nilai null dengan tanda strip "-"
                $sample[] = $attribute;
            }
            $samples[] = $sample;
            $labels[] = $row[$labelAttribute];
        }

        $dataset = new ArrayDataset($samples, $labels);

        $crossValidationValue = $crosValidation_params;
        // Mengonversi ke float
        $percentage = floatval($crossValidationValue) / 100.0;

        // Verifikasi bahwa nilai berada dalam rentang yang valid
        if ($percentage <= 0.0 || $percentage >= 1.0) {
            // Nilai tidak valid, berikan respons atau tindakan yang sesuai
            return $this->sendError('Invalid cross-validation percentage');
        }

        // Lanjutkan dengan menggunakan $percentage seperti biasa
        $split = new StratifiedRandomSplit($dataset, $percentage);

        $sample_data = [];

        foreach ($split->getTestSamples() as $attribute_sample) {
            $sample_test = [];

            foreach ($attribute_sample as $attribute) {
                foreach ($data as $row) {
                    // Pastikan atribut dan indeks data tersedia sebelum mencoba mengakses
                    if (isset($row[$attribute])) {
                        $sample_test[$attribute] = $row[$attribute];
                    } else {
                        // Gantilah nilai null dengan tanda strip "-"
                        $sample_test[$attribute] = '-';
                    }
                }
            }

            $sample_test['label'] = $row[$labelAttribute];
            $sample_data[] = $sample_test;
        }

        // $accuracies = [];

        // foreach ($split->getTrainSamples() as $trainIndexes) {
        //     $trainSamples = $dataset->getSamples($trainIndexes);
        //     $trainLabels = $dataset->getTargets($trainIndexes);

        //     // Gunakan data uji yang tidak pernah digunakan dalam pelatihan
        //     $testIndexes = array_diff(range(0, count($samples) - 1), $trainIndexes);
        //     $testSamples = $dataset->getSamples($testIndexes);
        //     dd($testSamples);
        //     $testLabels = $dataset->getTargets($testIndexes);

        //     $naiveBayes = new NaiveBayes();
        //     $naiveBayes->train($trainSamples, $trainLabels);

        //     // Lakukan prediksi pada data uji
        //     $predictedLabels = $naiveBayes->predict($testSamples);
        // }

        return $this->sendResponse($sample_data, 'Information Gain completed');
    }

    public function getAcuracy(Request $request)
    {
        // Ambil data dari database menggunakan model UploadExcel
        $data = UploadExcel::all()->toArray();

        $labelAttribute = 'class_status'; // Atur atribut label yang sesuai

        $informationGains = [];

        // Menghitung Information Gain untuk setiap atribut
        $attributes = array_keys($data[0]); // Mengambil atribut dari model pertama
        $labelAttributeIndex = array_search($labelAttribute, $attributes);

        if ($labelAttributeIndex !== false) {
            unset($attributes[$labelAttributeIndex]); // Hapus atribut label dari array atribut
        }

        // Mengabaikan atribut 'id' dan 'uuid'
        $ignoredAttributes = ['id', 'uuid', 'created_at', 'updated_at'];
        $attributes = array_diff($attributes, $ignoredAttributes);

        // Tentukan batas jumlah nilai null yang dapat diterima
        $nullThreshold = 0.2; // 80%

        foreach ($attributes as $attribute) {
            // Hitung jumlah nilai null dalam atribut
            $nullCount = 0;
            foreach ($data as $row) {
                if ($row[$attribute] === null || $row[$attribute] === '') {
                    $nullCount++;
                }
            }

            // Hitung Information Gain hanya jika jumlah nilai null tidak melebihi batas
            if ($nullCount <= count($data) * $nullThreshold) {
                $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
                $informationGains[$attribute] = $informationGain;
            }
        }

        // Atribut dengan Information Gain tertinggi
        arsort($informationGains);

        // Mengambil N atribut teratas sesuai dengan nilai $params
        $topNInformationGains = array_slice($informationGains, 0, $request->informationGain);

        // Ambil atribut dari N teratas
        $selectedAttributes = array_keys($topNInformationGains);

        $samples = [];
        $labels = [];

        foreach ($data as $row) {
            $sample = [];
            foreach ($selectedAttributes as $attribute) {
                // Gantilah nilai null dengan tanda strip "-"
                $sample[] = $attribute;
            }
            $samples[] = $sample;
            $labels[] = $row[$labelAttribute];
        }

        $dataset = new ArrayDataset($samples, $labels);

        $crossValidationValue = $request->crossValidation;
        // Mengonversi ke float
        $percentage = floatval($crossValidationValue) / 100.0;

        // Verifikasi bahwa nilai berada dalam rentang yang valid
        if ($percentage <= 0.0 || $percentage >= 1.0) {
            // Nilai tidak valid, berikan respons atau tindakan yang sesuai
            return $this->sendError('Invalid cross-validation percentage');
        }

        // Lanjutkan dengan menggunakan $percentage seperti biasa
        $split = new StratifiedRandomSplit($dataset, $percentage);

        $accuracies = [];

        foreach ($split->getTrainSamples() as $trainIndexes) {
            $trainSamples = $dataset->getSamples($trainIndexes);
            $trainLabels = $dataset->getTargets($trainIndexes);

            // Gunakan data uji yang tidak pernah digunakan dalam pelatihan
            $testIndexes = array_diff(range(0, count($samples) - 1), $trainIndexes);
            $testSamples = $dataset->getSamples($testIndexes);
            $testLabels = $dataset->getTargets($testIndexes);

            $naiveBayes = new NaiveBayes();
            $naiveBayes->train($trainSamples, $trainLabels);

            // Lakukan prediksi pada data uji
            $predictedLabels = $naiveBayes->predict($testSamples);

            // Hitung akurasi
            $accuracy = Accuracy::score($testLabels, $predictedLabels);
            $accuracies[] = $accuracy;
        }

        $meanAccuracy = (array_sum($accuracies) / count($accuracies)) * 100;

        return $this->sendResponse($meanAccuracy, 'Accuracy processed successfully');
    }
}
