@extends('layouts.master')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white">Asset List</h2>
        <a href="{{ route('asset.import') }}" class="btn btn-primary">Import Asset</a>
    </div>

    <div class="card bg-dark border-secondary text-white">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr class="border-secondary">
                            <th>ID Asset</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                Belum ada data asset. Silakan lakukan import atau input manual.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection