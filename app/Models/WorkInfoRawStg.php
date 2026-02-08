<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkInfoRawStg extends Model
{
    protected $table = 'workinfo_raw_stg';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

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