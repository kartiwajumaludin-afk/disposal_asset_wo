<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $table = 'tracker';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'site_id',
        'site_name',
        'ticket_status_name',
        'regional',
        'network_operation_and_productivity',
        'teritory_operation',
        'workable_status',
        'general_status',
        'asset_status',
        'ticket_summary',
        'ticket_batch',
        'ticket_sub_type_name',
        'ticket_created_date',
        'jumlah_asset',
        'cat_asset',
        'asset_position',
        'percentage_asset_actual',
        'plan_asset_dismantle',
        'actual_asset_dismantle',
        'assignee_group',
        'tp_company',
        'latitude',
        'longitude',
        'caf_status',
        'working_permit_start_date',
        'working_permit_end_date',
        'working_permit_status_name',
        'start_permit_tp_date',
        'end_permit_tp_date',
        'status_permit_tp',
        'site_status',
        'site_issue',
        'category_issue',
        'detail_issue',
        'remark_dismantle',
        'mom',
        'cat_pending_approval',
        'aging_pending_approval',
        'submit_before',
        'approve_before',
        'dismantle',
        'submit_after',
        'approve_after',
        'pcaa_approve',
        'closed',
        'partner_company',
        'plan_dismantle_date',
        'plan_dismantle_week',
        'pic_team',
        'no_handphone',
    ];

    protected $casts = [
        'ticket_created_date' => 'datetime',
        'working_permit_start_date' => 'datetime',
        'working_permit_end_date' => 'datetime',
        'start_permit_tp_date' => 'date',
        'end_permit_tp_date' => 'date',
        'submit_before' => 'datetime',
        'approve_before' => 'datetime',
        'pcaa_approve' => 'datetime',
        'closed' => 'datetime',
        'plan_dismantle_date' => 'date',
        'jumlah_asset' => 'integer',
        'aging_pending_approval' => 'integer',
        'percentage_asset_actual' => 'integer',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketClean::class, 'ticket_number', 'ticket_number');
    }

    public function assets()
    {
        return $this->hasMany(AssetClean::class, 'ticket_number', 'ticket_number');
    }

    public function workInfos()
    {
        return $this->hasMany(WorkInfoClean::class, 'ticket_number', 'ticket_number');
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('ticket_status_name', [
            'Waiting TO Review',
            'Waiting NOP Approval',
            'Waiting PCAA Approval'
        ]);
    }

    public function scopeClosed($query)
    {
        return $query->where('ticket_status_name', 'Closed');
    }

    public function scopeByRegional($query, $regional)
    {
        return $query->where('regional', $regional);
    }
}