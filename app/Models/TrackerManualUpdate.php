<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerManualUpdate extends Model
{
    protected $table = 'tracker_manual_update';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ticket_number',
        'tp_company',
        'latitude',
        'longitude',
        'caf_status',
        'general_status',
        'start_permit_tp_date',
        'end_permit_tp_date',
        'status_permit_tp',
        'ticket_batch',
        'site_status',
        'site_issue',
        'category_issue',
        'detail_issue',
        'remark_dismantle',
        'mom',
        'partner_company',
        'plan_dismantle_date',
        'plan_dismantle_date_raw',
        'pic_team',
        'no_handphone',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'start_permit_tp_date' => 'date',
        'end_permit_tp_date' => 'date',
        'plan_dismantle_date' => 'date',
    ];
}