<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportAudit extends Model
{
    protected $table = 'import_audit';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'file_type',
        'status',
        'row_count',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'row_count' => 'integer',
    ];

    public static function isUploaded($fileType)
    {
        return self::where('file_type', $fileType)
                   ->where('status', 'DONE')
                   ->exists();
    }

    public static function getStatus($fileType)
    {
        return self::where('file_type', $fileType)->first();
    }
}