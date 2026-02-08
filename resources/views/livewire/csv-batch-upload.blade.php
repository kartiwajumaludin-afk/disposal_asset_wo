<div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Import</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">📊 CSV Batch Import</span>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📁 CSV Batch Import</h3>
                    </div>
                    <div class="card-body">
                        
                        @if(!$importComplete)
                        
                        <!-- Upload Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>📋 Instructions:</strong><br>
                                    • Upload <strong>3 mandatory files</strong>: Ticket, Asset, Workinfo<br>
                                    • Optional: Manual Update CSV<br>
                                    • Max file size: 10MB per file
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Forms -->
                        <div class="row">
                            <!-- Ticket -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>✅ TICKET CSV</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="file" wire:model="ticketFile" class="form-control" accept=".csv">
                                @if($ticketFile)
                                    <small class="text-success">✓ {{ $ticketFile->getClientOriginalName() }}</small>
                                @endif
                                @error('ticketFile') <small class="text-danger">{{ $message }}</small> @enderror
                                <div wire:loading wire:target="ticketFile" class="text-info">
                                    <small>⏳ Validating...</small>
                                </div>
                            </div>

                            <!-- Asset -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>✅ ASSET CSV</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="file" wire:model="assetFile" class="form-control" accept=".csv">
                                @if($assetFile)
                                    <small class="text-success">✓ {{ $assetFile->getClientOriginalName() }}</small>
                                @endif
                                @error('assetFile') <small class="text-danger">{{ $message }}</small> @enderror
                                <div wire:loading wire:target="assetFile" class="text-info">
                                    <small>⏳ Validating...</small>
                                </div>
                            </div>

                            <!-- Workinfo -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>✅ WORKINFO CSV</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="file" wire:model="workinfoFile" class="form-control" accept=".csv">
                                @if($workinfoFile)
                                    <small class="text-success">✓ {{ $workinfoFile->getClientOriginalName() }}</small>
                                @endif
                                @error('workinfoFile') <small class="text-danger">{{ $message }}</small> @enderror
                                <div wire:loading wire:target="workinfoFile" class="text-info">
                                    <small>⏳ Validating...</small>
                                </div>
                            </div>

                            <!-- Manual Update -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>⭕ MANUAL UPDATE CSV</strong> <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="file" wire:model="manualFile" class="form-control" accept=".csv">
                                @if($manualFile)
                                    <small class="text-success">✓ {{ $manualFile->getClientOriginalName() }}</small>
                                @endif
                                @error('manualFile') <small class="text-danger">{{ $message }}</small> @enderror
                                <div wire:loading wire:target="manualFile" class="text-info">
                                    <small>⏳ Validating...</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button 
                                    wire:click="import" 
                                    class="btn btn-primary btn-lg"
                                    @if(!$ticketFile || !$assetFile || !$workinfoFile || $isImporting) disabled @endif
                                >
                                    <span wire:loading.remove wire:target="import">🚀 Import All Files</span>
                                    <span wire:loading wire:target="import">⏳ Importing...</span>
                                </button>
                                
                                <button 
                                    wire:click="resetUpload" 
                                    class="btn btn-secondary btn-lg"
                                    @if($isImporting) disabled @endif
                                >
                                    🗑️ Clear All
                                </button>
                            </div>
                        </div>

                        @endif

                        <!-- Progress Section -->
                        @if(!empty($importProgress))
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>📊 Import Progress</h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($importProgress as $type => $progress)
                                        <div class="mb-2">
                                            <strong class="text-uppercase">{{ $type }}:</strong>
                                            <span class="
                                                @if($progress['status'] === 'success') text-success
                                                @elseif($progress['status'] === 'error') text-danger
                                                @else text-info
                                                @endif
                                            ">
                                                {{ $progress['message'] }}
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Success Message -->
                        @if($importComplete)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <h4>🎉 Import Completed Successfully!</h4>
                                    <p>All files have been imported to the database.</p>
                                    <button wire:click="resetUpload" class="btn btn-primary">
                                        📤 Upload New Files
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
</div>