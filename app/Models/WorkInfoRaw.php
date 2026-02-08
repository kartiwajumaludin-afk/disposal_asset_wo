<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkInfoRaw extends Model
{
    protected $table = 'workinfo_raw';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'Ticket Number',
        'Ticket Sub Type Name',
        'Regional',
        'Network Operation and Productivity',
        'Teritory Operation',
        'Site ID',
        'Site Name',
        'Work Info Updated Date',
        'Work Info Status Name',
        'Work Info Note',
        'Work Info User Updater',
        'Work Info Role Updater',
    ];
}