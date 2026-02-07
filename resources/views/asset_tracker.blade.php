@extends('layouts.master')

@section('content')
<div class="container-fluid p-4">
    <h2 class="text-white mb-4">Asset Tracker</h2>
    
    <div class="card bg-dark border-secondary text-white shadow-sm">
        <div class="card-body">
            <p>Lacak status perpindahan dan proses eksekusi disposal secara real-time.</p>
            <div class="timeline-placeholder py-5 text-center">
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Menghubungkan ke database disposal...</p>
            </div>
        </div>
    </div>
</div>
@endsection