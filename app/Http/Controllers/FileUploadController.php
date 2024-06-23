<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileUploadRequest;
use App\Http\Requests\StoreUploadExcelRequest;
use App\Http\Requests\UpdateFileUploadRequest;
use App\Imports\ExcelImport;
use App\Models\FileUpload;
use App\Models\UploadExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadController extends BaseController
{
    public function getAll()
    {
        $data = UploadExcel::all();
        return $this->sendResponse($data, 'Data Excel Fetched Success');
    }

    public function index()
    {
        $module = 'Data';
        return view('file_upload.index', compact('module'));
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

    public function delete()
    {
        // Gantilah dengan logika penghapusan yang sesuai untuk aplikasi Anda
        // Sebagai contoh, kita akan menghapus semua data dari tabel upload_excels
        DB::table('upload_excels')->delete();

        // Redirect atau berikan respons sesuai kebutuhan
        return redirect()->back();
    }
}
