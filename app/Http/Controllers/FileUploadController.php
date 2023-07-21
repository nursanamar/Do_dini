<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileUploadRequest;
use App\Http\Requests\UpdateFileUploadRequest;
use App\Models\FileUpload;

class FileUploadController extends BaseController
{
    // public function getAll()
    // {
    //     $data = FasilitasKesehatan::all();
    //     return $this->sendResponse($data, 'Data fasilitas kesehatan Fetched Success');
    // }

    public function index()
    {
        $module = 'Deteksi Drop Out';
        return view('file_upload.index', compact('module'));
    }

    // public function store(StoreFasilitasKesehatanRequest $request)
    // {
    //     $data = array();
    //     try {
    //         $data = new FasilitasKesehatan();
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
    //     return $this->sendResponse($data, 'Fasilitas Kesehatan Added success');
    // }

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
