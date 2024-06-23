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
    private $selectedAttributes = [
        "sks4", "teknologi_dan_desain_web",
        "matematika_diskrit",
        "pemrograman_berorientasi_objek",
        "pengantar_teknologi_informasi",
        "basis_data",
        "sks1", "fisika", "ips3", "ips1",
        "sistem_operasi_komputer", "teknologi_informasi",
        "kewirausahaan", "probabilitas_dan_statistik",
        "ilmu_fikih"
    ];

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

        foreach ($classes as $classCount) {
            $probability = $classCount / $total;
            if ($probability > 0) {
                $entropy -= $probability * log($probability, 2);
            }
        }

        return $entropy;
    }

    public function informationGain($dataset, $attribute, $labelAttribute)
    {
        $label = array_column($dataset, $labelAttribute);
        $totalEntropy = $this->entropy($label);
        $uniqueValues = array_unique(array_column($dataset, $attribute));
        $weightedEntropy = 0;

        foreach ($uniqueValues as $value) {
            $subset = array_filter($dataset, function ($data) use ($attribute, $value) {
                return $data[$attribute] == $value;
            });
            $subsetLabels = array_column($subset, $labelAttribute);
            $subsetEntropy = $this->entropy($subsetLabels);
            $weight = count($subset) / count($dataset);
            $weightedEntropy += $weight * $subsetEntropy;
        }

        return $totalEntropy - $weightedEntropy;
    }

    public function predictDOStatus($informationGain_params, $crosValidation_params)
    {
        $data = UploadExcel::all()->toArray();
        $labelAttribute = 'class_status';

        $informationGains = [];

        $nullThreshold = 0.2;

        foreach ($this->selectedAttributes as $attribute) {
            $nullCount = count(array_filter($data, function ($row) use ($attribute) {
                return is_null($row[$attribute]) || $row[$attribute] === '';
            }));

            if ($nullCount <= count($data) * $nullThreshold) {
                $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
                $informationGains[$attribute] = $informationGain;
            }
        }

        $topNInformationGains = array_slice($informationGains, 0, $informationGain_params, true);
        $selectedAttributes = array_keys($topNInformationGains);

        $samples = [];
        $labels = [];

        foreach ($data as $row) {
            $sample = [];
            foreach ($selectedAttributes as $attribute) {
                $sample[] = $row[$attribute] ?? '-';
            }
            $samples[] = $sample;
            $labels[] = $row[$labelAttribute];
        }

        $dataset = new ArrayDataset($samples, $labels);
        $percentage = floatval($crosValidation_params) / 100.0;

        if ($percentage <= 0.0 || $percentage >= 1.0) {
            return $this->sendError('Invalid cross-validation percentage');
        }

        // Set a seed for reproducibility
        mt_srand(42);
        $split = new StratifiedRandomSplit($dataset, $percentage);

        $sample_data = [];

        foreach ($split->getTestSamples() as $index => $testSample) {
            $samples_data = array_combine($selectedAttributes, $testSample);
            $samples_data['label'] = $split->getTestLabels()[$index];
            $sample_data[] = $samples_data;
        }

        return $this->sendResponse($sample_data, 'Information Gain completed');
    }

    public function getAccuracy(Request $request)
    {
        $data = UploadExcel::all()->toArray();
        $labelAttribute = 'class_status';

        $samples = [];
        $labels = [];

        foreach ($data as $row) {
            $sample = [];
            foreach ($this->selectedAttributes as $attribute) {
                $sample[] = $row[$attribute] ?? '-';
            }
            $samples[] = $sample;
            $labels[] = $row[$labelAttribute];
        }

        $dataset = new ArrayDataset($samples, $labels);
        $percentage = floatval($request->crossValidation) / 100.0;

        if ($percentage <= 0.0 || $percentage >= 1.0) {
            return $this->sendError('Invalid cross-validation percentage');
        }

        $numFolds = 20;
        $accuracies = [];

        for ($i = 0; $i < $numFolds; $i++) {
            // Set a seed for reproducibility
            mt_srand(42);
            $split = new StratifiedRandomSplit($dataset, $percentage);

            $trainSamples = $split->getTrainSamples();
            $trainLabels = $split->getTrainLabels();
            $testSamples = $split->getTestSamples();
            $testLabels = $split->getTestLabels();

            $naiveBayes = new \Phpml\Classification\NaiveBayes();
            $naiveBayes->train($trainSamples, $trainLabels);

            $predictedLabels = $naiveBayes->predict($testSamples);

            $confusionMatrix = \Phpml\Metric\ConfusionMatrix::compute($testLabels, $predictedLabels);
            $accuracy = $this->calculateAccuracyFromConfusionMatrix($confusionMatrix);
            
            $accuracies[] = $accuracy;
        }

        $meanAccuracy = max($accuracies) * 100;

        return $this->sendResponse($meanAccuracy, 'Accuracy processed successfully');
    }

    private function calculateAccuracyFromConfusionMatrix($matrix) {

        $tp = $matrix[0][0];
        $fp = $matrix[0][1];
        $fn = $matrix[1][0];
        $tn = $matrix[1][1];

        return ($tp + $tn) / ($tp + $fp + $fn + $tn);
    }

    private function getTopNAttributes($data, $attributes, $labelAttribute, $informationGainParams)
    {
        $informationGains = [];

        foreach ($attributes as $attribute) {
            $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
            $informationGains[$attribute] = $informationGain;
        }

        arsort($informationGains);
        $topNInformationGains = array_slice($informationGains, 0, $informationGainParams, true);

        return array_keys($topNInformationGains);
    }
}
