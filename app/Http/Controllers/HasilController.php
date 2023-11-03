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


    public function predictDOStatus($params)
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

        foreach ($attributes as $attribute) {
            $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
            $informationGains[$attribute] = $informationGain;
        }

        // Atribut dengan Information Gain tertinggi
        arsort($informationGains);

        // Mengambil N atribut teratas sesuai dengan nilai $params
        $topNInformationGains = array_slice($informationGains, 0, $params);

        // Ambil atribut dari N teratas
        $selectedAttributes = array_keys($topNInformationGains);

        $samples = [];

        foreach ($data as $row) {
            $sample = [];
            foreach ($selectedAttributes as $attribute) {
                // Gantilah nilai null dengan tanda strip "-"
                $sample[$attribute] = $row[$attribute] ?? '-';
            }

            $sample['label'] = $row[$labelAttribute];

            $samples[] = $sample;
        }

        return $this->sendResponse($samples, 'Cross-validation completed');
    }

    public function getAcuracy($params)
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

        foreach ($attributes as $attribute) {
            $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
            $informationGains[$attribute] = $informationGain;
        }

        // Atribut dengan Information Gain tertinggi
        arsort($informationGains);

        // Mengambil N atribut teratas sesuai dengan nilai $params
        $topNInformationGains = array_slice($informationGains, 0, $params);

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

        // Lakukan pemisahan data dengan RandomSplit
        $split = new StratifiedRandomSplit($dataset, 0.5);

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
