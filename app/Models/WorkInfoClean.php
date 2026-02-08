<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkInfoClean extends Model
{
    protected $table = 'workinfo_clean';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'site_id',
        'site_name',
        'ticket_sub_type_name',
        'regional',
        'network_operation_and_productivity',
        'teritory_operation',
        'work_info_user_updater',
        'work_info_role_updater',
        'work_info_status_name',
        'work_info_note',
        'work_info_updated_date',
    ];

    protected $casts = [
        'work_info_updated_date' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketClean::class, 'ticket_number', 'ticket_number');
    }

    public function scopeLatestStatus($query, $ticketNumber)
    {
        return $query->where('ticket_number', $ticketNumber)
                     ->orderBy('work_info_updated_date', 'desc')
                     ->first();
    }
}