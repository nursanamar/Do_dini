<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class UploadExcel extends Model
{
    use HasFactory;
    protected $table = 'upload_excels';
    protected $fillable = [
        'id',
        'uuid',
        'nim',
        'nama',
        'ipk',
        'semester',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event listener untuk membuat UUID sebelum menyimpan
        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
