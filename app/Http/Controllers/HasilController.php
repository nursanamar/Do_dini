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
        $attributes = array_keys($data[0]);
        $ignoredAttributes = ['id', 'uuid', 'created_at', 'updated_at', 'id_masked', $labelAttribute];
        $attributes = array_diff($attributes, $ignoredAttributes);

        $nullThreshold = 0.2;

        foreach ($attributes as $attribute) {
            $nullCount = count(array_filter($data, function ($row) use ($attribute) {
                return is_null($row[$attribute]) || $row[$attribute] === '';
            }));

            if ($nullCount <= count($data) * $nullThreshold) {
                $informationGain = $this->informationGain($data, $attribute, $labelAttribute);
                $informationGains[$attribute] = $informationGain;
            }
        }

        arsort($informationGains);
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

        $attributes = array_keys($data[0]);
        $ignoredAttributes = ['id', 'uuid', 'created_at', 'updated_at', 'id_masked', $labelAttribute];
        $attributes = array_diff($attributes, $ignoredAttributes);

        $selectedAttributes = $this->getTopNAttributes($data, $attributes, $labelAttribute, $request->informationGain);

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
        $percentage = floatval($request->crossValidation) / 100.0;

        if ($percentage <= 0.0 || $percentage >= 1.0) {
            return $this->sendError('Invalid cross-validation percentage');
        }

        $numFolds = max(intval(1 / $percentage), 2);
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

            $accuracy = \Phpml\Metric\Accuracy::score($testLabels, $predictedLabels);
            $accuracies[] = $accuracy;
        }

        $meanAccuracy = (array_sum($accuracies) / count($accuracies)) * 100;

        return $this->sendResponse($meanAccuracy, 'Accuracy processed successfully');
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
