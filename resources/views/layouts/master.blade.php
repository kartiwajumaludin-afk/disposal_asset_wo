<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposal System | Dark Aesthetic</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style_dark_aesthetic.css') }}">
    @stack('styles')
</head>
<body style="background-color: #121212; color: #e0e0e0;">

    <div class="d-flex" id="wrapper">
        <div class="bg-black border-end border-secondary" id="sidebar-wrapper" style="min-width: 250px; min-height: 100vh;">
            <div class="sidebar-heading p-4 text-white fs-4 fw-bold border-bottom border-secondary">
                DISPOSAL PRO
            </div>
            <div class="list-group list-group-flush mt-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3 px-4">Dashboard</a>
                <a href="{{ route('asset.index') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3 px-4">Asset List</a>
                <a href="{{ route('asset.inbound') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3 px-4">Inbound</a>
                <a href="{{ route('asset.tracker') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3 px-4">Tracker</a>
                <a href="{{ route('asset.boq') }}" class="list-group-item list-group-item-action bg-transparent text-white border-0 py-3 px-4">BOQ</a>
            </div>
        </div>

        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary p-3">
                <div class="container-fluid">
                    <span class="navbar-brand">Phase 1: Integration</span>
                </div>
            </nav>

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/main_dark_aesthetic.js') }}"></script>
    @stack('scripts')
</body>
</html>