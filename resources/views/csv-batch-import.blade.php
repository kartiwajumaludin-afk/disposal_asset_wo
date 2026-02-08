<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSV Batch Import</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .content {
            padding: 40px;
        }

        .instructions {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .instructions h3 {
            color: #1976d2;
            margin-bottom: 10px;
        }

        .instructions ul {
            margin-left: 20px;
            color: #555;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .form-group label.required::after {
            content: " *";
            color: #e53935;
        }

        .form-group label.optional::after {
            content: " (Optional)";
            color: #999;
            font-weight: normal;
            font-size: 12px;
        }

        .file-input-wrapper {
            position: relative;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            background: #fafafa;
            cursor: pointer;
        }

        .file-input-wrapper:hover {
            border-color: #667eea;
            background: #f5f7ff;
        }

        .file-input-wrapper.has-file {
            border-color: #4caf50;
            background: #f1f8f4;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-label {
            color: #666;
            font-size: 14px;
            pointer-events: none;
        }

        .file-name {
            margin-top: 10px;
            color: #4caf50;
            font-weight: 600;
            font-size: 13px;
            display: none;
        }

        .file-name.show {
            display: block;
        }

        .file-icon {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .results {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .results h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            font-weight: 600;
            color: #555;
        }

        .result-value {
            color: #28a745;
            font-weight: 600;
        }

        /* PROGRESS BAR STYLES */
        .progress-container {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .progress-container.active {
            display: block;
        }

        .progress-step {
            margin-bottom: 20px;
        }

        .progress-step h4 {
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-step .icon {
            font-size: 18px;
        }

        .progress-bar-wrapper {
            background: #e0e0e0;
            border-radius: 10px;
            height: 25px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .progress-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-pending { color: #999; }
        .status-processing { color: #ff9800; }
        .status-complete { color: #4caf50; }
        .status-error { color: #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 CSV Batch Import</h1>
            <p>Upload and process multiple CSV files at once</p>
        </div>

        <div class="content">
            <div class="instructions">
                <h3>📝 Instructions:</h3>
                <ul>
                    <li>Upload <strong>3 mandatory files</strong>: Ticket, Asset, Workinfo</li>
                    <li>Optional: Manual Update CSV</li>
                    <li>Max file size: <strong>512MB per file</strong></li>
                    <li>Accepted formats: <strong>.csv, .txt</strong></li>
                </ul>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @if(session('results'))
                <div class="results">
                    <h3>📊 Import Results:</h3>
                    @foreach(session('results') as $type => $data)
                        @if($type === 'manual')
                            <div class="result-item">
                                <span class="result-label">✏️ Manual Update:</span>
                                <span class="result-value">{{ $data['rows'] }} rows</span>
                            </div>
                        @else
                            <div class="result-item">
                                <span class="result-label">{{ ucfirst($type) }} - Total CSV:</span>
                                <span class="result-value">{{ number_format($data['total']) }} rows</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">{{ ucfirst($type) }} - STG Inserted:</span>
                                <span class="result-value">{{ number_format($data['stg']) }} rows</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">{{ ucfirst($type) }} - CLEAN Final:</span>
                                <span class="result-value">{{ number_format($data['clean']) }} rows</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <form action="{{ route('csv.batch.import.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <!-- TICKET FILE -->
                <div class="form-group">
                    <label class="required">📋 TICKET CSV</label>
                    <div class="file-input-wrapper" id="ticketWrapper">
                        <input type="file" name="ticketFile" id="ticketFile" accept=".csv,.txt" required>
                        <div class="file-icon">📋</div>
                        <div class="file-input-label">Click to browse or drag Ticket CSV here</div>
                        <div class="file-name" id="ticketName"></div>
                    </div>
                    @error('ticketFile')
                        <small style="color: #e53935;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- ASSET FILE -->
                <div class="form-group">
                    <label class="required">💾 ASSET CSV</label>
                    <div class="file-input-wrapper" id="assetWrapper">
                        <input type="file" name="assetFile" id="assetFile" accept=".csv,.txt" required>
                        <div class="file-icon">💾</div>
                        <div class="file-input-label">Click to browse or drag Asset CSV here</div>
                        <div class="file-name" id="assetName"></div>
                    </div>
                    @error('assetFile')
                        <small style="color: #e53935;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- WORKINFO FILE -->
                <div class="form-group">
                    <label class="required">⚙️ WORKINFO CSV</label>
                    <div class="file-input-wrapper" id="workinfoWrapper">
                        <input type="file" name="workinfoFile" id="workinfoFile" accept=".csv,.txt" required>
                        <div class="file-icon">⚙️</div>
                        <div class="file-input-label">Click to browse or drag Workinfo CSV here</div>
                        <div class="file-name" id="workinfoName"></div>
                    </div>
                    @error('workinfoFile')
                        <small style="color: #e53935;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- MANUAL FILE (OPTIONAL) -->
                <div class="form-group">
                    <label class="optional">✏️ MANUAL UPDATE CSV</label>
                    <div class="file-input-wrapper" id="manualWrapper">
                        <input type="file" name="manualFile" id="manualFile" accept=".csv,.txt">
                        <div class="file-icon">✏️</div>
                        <div class="file-input-label">Click to browse or drag Manual Update CSV here</div>
                        <div class="file-name" id="manualName"></div>
                    </div>
                    @error('manualFile')
                        <small style="color: #e53935;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    🚀 Import All Files
                </button>
            </form>

            <!-- PROGRESS BAR CONTAINER -->
            <div class="progress-container" id="progressContainer">
                <h3 style="margin-bottom: 20px;">⏳ Processing...</h3>
                
                <div class="progress-step">
                    <h4><span class="icon">📤</span> <span id="uploadStatus" class="status-pending">Upload Files</span></h4>
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar" id="uploadProgress" style="width: 0%;">0%</div>
                    </div>
                    <div class="progress-text" id="uploadText">Waiting...</div>
                </div>

                <div class="progress-step">
                    <h4><span class="icon">📋</span> <span id="ticketStatus" class="status-pending">Ticket Import</span></h4>
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar" id="ticketProgress" style="width: 0%;">0%</div>
                    </div>
                    <div class="progress-text" id="ticketText">Waiting...</div>
                </div>

                <div class="progress-step">
                    <h4><span class="icon">💾</span> <span id="assetStatus" class="status-pending">Asset Import</span></h4>
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar" id="assetProgress" style="width: 0%;">0%</div>
                    </div>
                    <div class="progress-text" id="assetText">Waiting...</div>
                </div>

                <div class="progress-step">
                    <h4><span class="icon">⚙️</span> <span id="workinfoStatus" class="status-pending">Workinfo Import</span></h4>
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar" id="workinfoProgress" style="width: 0%;">0%</div>
                    </div>
                    <div class="progress-text" id="workinfoText">Waiting...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // FILE NAME DISPLAY
        function updateFileName(input, targetId, wrapperId) {
            const target = document.getElementById(targetId);
            const wrapper = document.getElementById(wrapperId);
            
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                
                target.textContent = `✓ ${fileName} (${fileSize} MB)`;
                target.classList.add('show');
                wrapper.classList.add('has-file');
            } else {
                target.textContent = '';
                target.classList.remove('show');
                wrapper.classList.remove('has-file');
            }
        }

        document.getElementById('ticketFile').addEventListener('change', function() {
            updateFileName(this, 'ticketName', 'ticketWrapper');
        });

        document.getElementById('assetFile').addEventListener('change', function() {
            updateFileName(this, 'assetName', 'assetWrapper');
        });

        document.getElementById('workinfoFile').addEventListener('change', function() {
            updateFileName(this, 'workinfoName', 'workinfoWrapper');
        });

        document.getElementById('manualFile').addEventListener('change', function() {
            updateFileName(this, 'manualName', 'manualWrapper');
        });

        // PROGRESS BAR SIMULATION
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const progressContainer = document.getElementById('progressContainer');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Processing... Please wait...';
            
            // Show progress container
            progressContainer.classList.add('active');
            
            // Simulate upload progress
            simulateProgress('upload', 'Upload Files', 3000);
            
            // Simulate ticket progress
            setTimeout(() => {
                simulateProgress('ticket', 'Ticket Import', 5000);
            }, 3000);
            
            // Simulate asset progress
            setTimeout(() => {
                simulateProgress('asset', 'Asset Import', 8000);
            }, 8000);
            
            // Simulate workinfo progress
            setTimeout(() => {
                simulateProgress('workinfo', 'Workinfo Import', 12000);
            }, 16000);
        });

        function simulateProgress(type, label, duration) {
            const statusEl = document.getElementById(type + 'Status');
            const progressEl = document.getElementById(type + 'Progress');
            const textEl = document.getElementById(type + 'Text');
            
            statusEl.textContent = label;
            statusEl.className = 'status-processing';
            textEl.innerHTML = '<span class="spinner"></span> Processing...';
            
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 100) progress = 100;
                
                progressEl.style.width = progress + '%';
                progressEl.textContent = Math.floor(progress) + '%';
                
                if (progress >= 100) {
                    clearInterval(interval);
                    statusEl.className = 'status-complete';
                    textEl.textContent = '✓ Complete';
                }
            }, duration / 20);
        }
    </script>
</body>
</html>