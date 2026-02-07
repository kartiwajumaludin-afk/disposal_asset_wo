<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function dashboard() {
        return view('dashboard');
    }

    public function index() {
        return view('asset_index');
    }

    public function import() {
        return view('asset_import');
    }

    public function tracker() {
        return view('asset_tracker');
    }

    public function activity() {
        return view('asset_activity');
    }

    public function sitemap() {
        return view('asset_sitemap');
    }

    public function boq() {
        return view('asset_boq');
    }

    public function inbound() {
        return view('asset_inbound');
    }
}