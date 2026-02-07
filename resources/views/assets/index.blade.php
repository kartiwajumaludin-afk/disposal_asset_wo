@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Asset List</h1>
    <p>Daftar seluruh asset yang tersedia dalam sistem disposal.</p>
    
    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Asset</th>
                    <th>Kategori</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center">Data masih kosong (Menunggu Phase selanjutnya).</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection