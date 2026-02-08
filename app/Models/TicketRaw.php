<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketRaw extends Model
{
    protected $table = 'ticket_raw';
    protected $primaryKey = 'Ticket Number';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'Ticket Number',
        'Ticket Sub Type Name',
        'Ticket Status Name',
        'Regional',
        'Network Operation and Productivity',
        'Teritory Operation',
        'Site ID',
        'Site Name',
        'Assignee Group',
        'Assignee',
        'Ticket Summary',
        'Ticket Created Date',
        'Ticket Resolved Date',
        'Ticket Cleared Date',
        'Working Permit Number',
        'Working Permit Status Name',
        'Working Permit Status Text',
        'Working Permit Activity Name',
        'Working Permit Activity Description',
        'Working Permit Activity Category',
        'Site Owner',
        'Working Permit Start Date',
        'Working Permit End Date',
        'Working Permit Updated Date',
        'SIK Number',
        'SIK Status Name',
    ];
}