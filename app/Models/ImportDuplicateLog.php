<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportDuplicateLog extends Model
{
    protected $table = 'import_duplicate_log';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'source',
        'action',
        'reason',
        'filter_col_1',
        'filter_col_2',
        'filter_col_3',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByTicket($query, $ticketNumber)
    {
        return $query->where('ticket_number', $ticketNumber);
    }
}
