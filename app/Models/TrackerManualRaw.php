<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerManualRaw extends Model
{
    protected $table = 'tracker_manual_raw';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_number',
        'tp_company',
        'latitude',
        'longitude',
        'caf_status',
        'general_status',
        'start_permit_tp_date_raw',
        'end_permit_tp_date_raw',
        'status_permit_tp',
        'ticket_batch',
        'site_status',
        'site_issue',
        'category_issue',
        'detail_issue',
        'remark_dismantle',
        'mom',
        'partner_company',
        'plan_dismantle_date_raw',
        'pic_team',
        'no_handphone',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];
}