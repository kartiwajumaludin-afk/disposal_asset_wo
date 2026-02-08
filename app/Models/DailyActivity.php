<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $table = 'daily_activity';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'site_id',
        'site_name',
        'regional',
        'network_operation_and_productivity',
        'teritory_operation',
        'ticket_status_name',
        'update_ticket_status_name',
        'plan_dismantle_date',
        'pic_team',
        'assigned_by',
        'assignment_status',
        'task_status',
        'category_issue',
        'detail_issue',
        'remark_dismantle',
        'activity_date',
    ];

    protected $casts = [
        'plan_dismantle_date' => 'date',
        'activity_date' => 'date',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('activity_date', today());
    }

    public function scopeByPic($query, $picTeam)
    {
        return $query->where('pic_team', $picTeam);
    }
}