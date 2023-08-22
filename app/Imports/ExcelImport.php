<?php

namespace App\Imports;

use App\Models\UploadExcel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        return new UploadExcel([
            'nim' => $row['nim'],
            'nama' => $row['nama'],
            'ipk' => $row['ipk'],
            'semester' => $row['semester']
        ]);
    }
}
