<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRawStg extends Model
{
    protected $table = 'asset_raw_stg';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'Ticket Number',
        'Ticket Sub Type Name',
        'Ticket Status Name',
        'Site ID',
        'Site Name',
        'Assignee Group',
        'Assignee',
        'Ticket Summary',
        'Ticket Created Date',
        'Ticket Resolved Date',
        'Ticket Cleared Date',
        'Barcode Number',
        'Part Code',
        'Part Name',
        'Brand Name',
        'Asset Physical Group Name',
        'Asset PO Number',
        'Asset Status Name',
        'Asset Flag Name',
        'Asset mFlag',
    ];
}