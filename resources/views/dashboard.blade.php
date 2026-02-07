@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mt-4">Dashboard Disposal</h1>
            <p class="lead">Selamat datang, Prof! Ini adalah halaman utama yang menggunakan template Dark Aesthetic.</p>
            
            <div class="card bg-secondary text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Status Koneksi</h5>
                    <p class="card-text">Pondasi Phase 1 telah berhasil dihubungkan ke Layout Master.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    console.log('Halaman Dashboard siap!');
</script>
@endpush