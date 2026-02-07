@extends('layouts.master')

@section('content')
<div class="container-fluid p-4">
    <h2 class="text-white mb-4">Bill of Quantity (BOQ)</h2>
    
    <div class="card bg-dark border-secondary text-white">
        <div class="card-body p-0">
            <div class="p-4 border-bottom border-secondary">
                <p class="mb-0">Perhitungan estimasi biaya dan volume material disposal.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-dark mb-0">
                    <thead class="bg-black">
                        <tr>
                            <th>Item Description</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-4">Data BOQ belum tersedia.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection