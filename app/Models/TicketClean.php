<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketClean extends Model
{
    protected $table = 'ticket_clean';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'ticket_sub_type_name',
        'ticket_status_name',
        'regional',
        'network_operation_and_productivity',
        'teritory_operation',
        'site_id',
        'site_name',
        'assignee_group',
        'assignee',
        'ticket_summary',
        'ticket_created_date',
        'ticket_resolved_date',
        'ticket_cleared_date',
        'working_permit_number',
        'working_permit_status_name',
        'working_permit_status_text',
        'working_permit_activity_name',
        'working_permit_activity_description',
        'working_permit_activity_category',
        'site_owner',
        'working_permit_start_date',
        'working_permit_end_date',
        'working_permit_updated_date',
        'sik_number',
        'sik_status_name',
    ];

    protected $casts = [
        'ticket_created_date' => 'datetime',
        'ticket_resolved_date' => 'datetime',
        'ticket_cleared_date' => 'datetime',
        'working_permit_start_date' => 'datetime',
        'working_permit_end_date' => 'datetime',
        'working_permit_updated_date' => 'datetime',
    ];

    public function tracker()
    {
        return $this->hasOne(Tracker::class, 'ticket_number', 'ticket_number');
    }
}