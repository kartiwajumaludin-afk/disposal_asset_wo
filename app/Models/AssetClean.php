<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetClean extends Model
{
    protected $table = 'asset_clean';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'site_id',
        'site_name',
        'ticket_sub_type_name',
        'ticket_status_name',
        'assignee_group',
        'assignee',
        'ticket_summary',
        'ticket_created_date',
        'ticket_resolved_date',
        'ticket_cleared_date',
        'barcode_number',
        'part_code',
        'part_name',
        'brand_name',
        'asset_physical_group_name',
        'asset_po_number',
        'asset_status_name',
        'asset_flag_name',
        'asset_mflag',
    ];

    protected $casts = [
        'ticket_created_date' => 'datetime',
        'ticket_resolved_date' => 'datetime',
        'ticket_cleared_date' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketClean::class, 'ticket_number', 'ticket_number');
    }

    public function scopeDisposed($query)
    {
        return $query->where('asset_mflag', 'LIKE', '%Disposed%');
    }
}