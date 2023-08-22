<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileUploadRequest;
use App\Http\Requests\StoreUploadExcelRequest;
use App\Http\Requests\UpdateFileUploadRequest;
use App\Imports\ExcelImport;
use App\Models\FileUpload;
use App\Models\UploadExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadController extends BaseController
{
    public function getAll()
    {
        $data = UploadExcel::all();

        $groupedMahasiswa = $data->groupBy('nim')->map(function ($group) {
            $mahasiswa = $group->first();
            $mahasiswa['semester_1'] = $group->where('semester', 1)->pluck('ipk')->first();
            $mahasiswa['semester_2'] = $group->where('semester', 2)->pluck('ipk')->first();
            $mahasiswa['semester_3'] = $group->where('semester', 3)->pluck('ipk')->first();
            $mahasiswa['semester_4'] = $group->where('semester', 4)->pluck('ipk')->first();
            return $mahasiswa;
        });
        return $this->sendResponse($groupedMahasiswa->values(), 'Data Excel Fetched Success');
    }

    public function index()
    {
        $module = 'Deteksi Drop Out';
        return view('file_upload.index', compact('module'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'file_excel' => 'required|mimes:xls,xlsx|max:20048', // Batas ukuran file diatur menjadi 2MB (sesuaikan sesuai kebutuhan)
            ]);

            if ($request->hasFile('file_excel')) {
                $file = $request->file('file_excel');

                // Generate unique filename
                $filename = time() . '_' . $file->getClientOriginalName();

                // Store the file in the 'upload_excels' directory within the storage disk
                $filePath = $file->storeAs('upload_excels', $filename);

                // Import data from the Excel file
                $data = new ExcelImport();
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


    // public function update(StoreFasilitasKesehatanRequest $request, $params)
    // {
    //     $data = array();
    //     try {
    //         $data = FasilitasKesehatan::where('uuid', $params)->first();
    //         $data->jenis_fasilitas_kesehatan = $request->jenis_fasilitas_kesehatan;
    //         $data->nama_fasilitas_kesehatan = $request->nama_fasilitas_kesehatan;
    //         $data->jumlah_tempat_tidur = $request->jumlah_tempat_tidur;
    //         $data->kepemilikan_kemenkes = $request->kepemilikan_kemenkes;
    //         $data->kepemilikan_pemprov = $request->kepemilikan_pemprov;
    //         $data->kepemilikan_pemkab = $request->kepemilikan_pemkab;
    //         $data->kepemilikan_tni_polri = $request->kepemilikan_tni_polri;
    //         $data->kepemilikan_bumn = $request->kepemilikan_bumn;
    //         $data->kepemilikan_swasta = $request->kepemilikan_swasta;
    //         $data->save();
    //     } catch (\Exception $e) {
    //         return $this->sendError($e->getMessage(), $e->getMessage(), 200);
    //     }
    //     return $this->sendResponse($data, 'Fasilitas Kesehatan Update success');
    // }

    // public function show($params)
    // {
    //     $data = array();
    //     try {
    //         $data =  DB::table('fasilitaskesehatans')->where('uuid', $params)->first();
    //     } catch (\Exception $e) {
    //         return $this->sendError($e->getMessage(), $e->getMessage(), 200);
    //     }
    //     return $this->sendResponse($data, 'Fasilitas Kesehatan Show success');
    // }

    // public function delete(Request $request, $params)
    // {
    //     $data = array();
    //     try {
    //         $data =  DB::table('fasilitaskesehatans')->where('uuid', $params)->delete();
    //     } catch (\Exception $e) {
    //         return $this->sendError($e->getMessage(), $e->getMessage(), 200);
    //     }
    //     return $this->sendResponse($data, 'Fasilitas Kesehatan Delete success');
    // }
}
