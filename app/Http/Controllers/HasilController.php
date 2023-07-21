<?php

namespace App\Http\Controllers;

use App\Models\Hasil;

class HasilController extends BaseController
     {
        public function index()
        {
            $module = 'Deteksi Drop Out';
            return view('hasil.index', compact('module'));
        }
     }