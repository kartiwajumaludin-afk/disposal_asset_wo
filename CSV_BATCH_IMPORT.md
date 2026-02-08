# 📊 CSV Batch Import Feature Documentation

## 🎯 Overview

Fitur untuk upload dan memproses multiple CSV files sekaligus dalam satu kali eksekusi.

**Target files:**
- `ticket.csv` (mandatory)
- `asset.csv` (mandatory)  
- `workinfo.csv` (mandatory)
- `update_tracker_manual.csv` (optional)

---

## 👤 User Flow

```
1. User login ke aplikasi
   ↓
2. Redirect ke Landing Page (Dark Aesthetic UI)
   ↓
3. Click tab "Import CSV" di submenu
   ↓
4. Redirect ke halaman upload form
   ↓
5. Select 4 CSV files dari komputer
   ↓
6. Click button "Import All Files"
   ↓
7. System processing (progress bar muncul)
   ↓
8. Results ditampilkan (success/error + row counts)
```

---

## 🏗️ Technical Architecture

### **Routes**

| Method | URL | Controller | Action |
|--------|-----|------------|--------|
| GET | `/csv-batch-import` | `CsvBatchImportController` | `index()` - Display upload form |
| POST | `/csv-batch-import/process` | `CsvBatchImportController` | `process()` - Handle upload & processing |

### **Controllers**

**File:** `app/Http/Controllers/CsvBatchImportController.php`

**Methods:**
- `index()` - Return view upload form
- `process(Request $request)` - Main processing logic
- `countCsvRows($filePath)` - Count total rows in CSV
- `uploadToStgChunked($filePath, $table)` - Chunked insert to STG table

### **Views**

**File:** `resources/views/index.blade.php`
- Landing page (Dark Aesthetic UI)
- Sidebar navigation
- Submenu tabs (termasuk "Import CSV")
- Data table & filters

**File:** `resources/views/csv-batch-import.blade.php`
- Upload form (4 file inputs)
- Progress bar
- Results display

---

## 🔄 Processing Flow

```
Upload CSV Files
    ↓
Get PHP Temp File Paths (getRealPath())
    ↓
Count Total Rows
    ↓
Upload to STG Tables (Chunked 1000 rows)
├── ticket.csv → ticket_raw_stg (TRUNCATE + INSERT)
├── asset.csv → asset_raw_stg (TRUNCATE + INSERT)
└── workinfo.csv → workinfo_raw_stg (TRUNCATE + INSERT)
    ↓
Execute Artisan Commands (ETL Pipeline)
├── php artisan import:ticket (stg → raw → clean)
├── php artisan import:asset (stg → raw → clean)
└── php artisan import:workinfo (stg → raw → clean)
    ↓
Return Results
├── Total rows per file
├── STG table row count
└── CLEAN table row count
```

---

## ⚙️ Configuration

### **File Upload Limits**

```php
'max_file_size' => 524288, // 512MB in KB
'allowed_types' => ['csv', 'txt'],
'chunk_size' => 1000, // rows per insert
```

### **Validation Rules**

```php
'ticketFile' => 'required|file|mimes:csv,txt|max:524288',
'assetFile' => 'required|file|mimes:csv,txt|max:524288',
'workinfoFile' => 'required|file|mimes:csv,txt|max:524288',
'manualFile' => 'nullable|file|mimes:csv,txt|max:524288',
```

---

## 📊 Database Tables

### **STG Tables (Staging - Temporary)**

| Table | Purpose | Strategy |
|-------|---------|----------|
| `ticket_raw_stg` | Staging for ticket data | TRUNCATE + INSERT |
| `asset_raw_stg` | Staging for asset data | TRUNCATE + INSERT |
| `workinfo_raw_stg` | Staging for workinfo data | TRUNCATE + INSERT |

### **RAW Tables (Persistent - Append Mode)**

| Table | Purpose | Strategy |
|-------|---------|----------|
| `ticket_raw` | Historical ticket data | INSERT (append) |
| `asset_raw` | Historical asset data | INSERT (append) |
| `workinfo_raw` | Historical workinfo data | INSERT (append) |

### **CLEAN Tables (Business Logic Applied)**

| Table | Purpose | Strategy |
|-------|---------|----------|
| `ticket_clean` | Clean ticket data (duplicates removed, validated) | INSERT/UPDATE (skipsert) |
| `asset_clean` | Clean asset data (duplicates removed, validated) | INSERT/UPDATE (skipsert) |
| `workinfo_clean` | Clean workinfo data (duplicates removed, validated) | INSERT/UPDATE (skipsert) |

---

## 🚀 Performance Optimization

### **Chunked Processing**

```php
$chunkSize = 1000; // Process 1000 rows at a time

while (($row = fgetcsv($handle)) !== false) {
    $chunk[] = $row;
    
    if (count($chunk) >= $chunkSize) {
        DB::table($table)->insert($chunk);
        $chunk = [];
        usleep(10000); // 10ms delay to reduce DB load
    }
}
```

**Benefits:**
- ✅ Avoid memory exhaustion (large files)
- ✅ Reduce DB lock time
- ✅ Enable progress tracking

### **Direct Temp File Read**

```php
$tempPath = $request->file('csvFile')->getRealPath();
// Example: C:\Windows\Temp\phpABC123.tmp
```

**Benefits:**
- ✅ No disk I/O overhead (save to project folder)
- ✅ Faster processing
- ✅ Auto-cleanup by PHP after request

---

## ❌ Error Handling

### **Validation Errors**

```php
// File type error
"The ticket file must be a file of type: csv, txt."

// File size error
"The asset file may not be greater than 524288 kilobytes."

// Required file missing
"The workinfo file field is required."
```

### **Processing Errors**

```php
try {
    DB::beginTransaction();
    // ... processing logic
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Import Error: ' . $e->getMessage());
    return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
}
```

**Error Types:**
- File not found
- Invalid CSV format (no header)
- Database connection error
- Artisan command execution error
- Row count mismatch

---

## 📈 Success Criteria

### **Functional Requirements**

- ✅ User dapat upload 4 files (ticket, asset, workinfo, manual)
- ✅ Files ter-validasi (type, size)
- ✅ Data masuk ke STG tables (100% rows)
- ✅ Artisan commands ter-execute tanpa error
- ✅ Data masuk ke CLEAN tables (sesuai business logic)
- ✅ Results ditampilkan (total, STG count, CLEAN count)
- ✅ Error handling (rollback on failure)

### **Non-Functional Requirements**

- ✅ Processing time: <60s for 1M rows total
- ✅ Memory usage: <512MB (chunked processing)
- ✅ UI responsive (progress bar update)
- ✅ No browser crash (large files)
- ✅ Log all processing steps
- ✅ Audit trail (import_audit table)

---

## 🧪 Testing Guide

### **Unit Test**

```bash
# Test 1: Small file (100 rows)
php artisan test --filter CsvBatchImportTest::testSmallFileUpload

# Test 2: Large file (100,000 rows)
php artisan test --filter CsvBatchImportTest::testLargeFileUpload

# Test 3: Invalid file type
php artisan test --filter CsvBatchImportTest::testInvalidFileType
```

### **Manual Test**

```bash
# Step 1: Start Laravel server
php artisan serve

# Step 2: Open browser
http://localhost:8000/csv-batch-import

# Step 3: Upload test files
- ticket.csv (40,000 rows)
- asset.csv (211,000 rows)
- workinfo.csv (739,000 rows)

# Step 4: Click "Import All Files"

# Step 5: Verify results
- Check STG tables count
- Check CLEAN tables count
- Check logs: storage/logs/laravel.log
```

### **Database Verification**

```sql
-- Check STG tables
SELECT COUNT(*) FROM ticket_raw_stg;
SELECT COUNT(*) FROM asset_raw_stg;
SELECT COUNT(*) FROM workinfo_raw_stg;

-- Check CLEAN tables
SELECT COUNT(*) FROM ticket_clean;
SELECT COUNT(*) FROM asset_clean;
SELECT COUNT(*) FROM workinfo_clean;

-- Check audit log
SELECT * FROM import_audit ORDER BY uploaded_at DESC LIMIT 10;
```

---

## 🔧 Troubleshooting

### **Problem: "Maximum execution time exceeded"**

**Solution:**
```php
// In controller
set_time_limit(300); // 5 minutes
ini_set('max_execution_time', 300);
```

### **Problem: "Allowed memory size exhausted"**

**Solution:**
```php
// Reduce chunk size
$chunkSize = 500; // from 1000
```

### **Problem: "SQLSTATE[HY000]: General error: 2006 MySQL server has gone away"**

**Solution:**
```php
// Reconnect to DB after each chunk
DB::reconnect();
```

### **Problem: "File name tidak muncul setelah select"**

**Solution:**
- Check JavaScript loaded: `assets/js/script_dark_aesthetic.js`
- Check browser console for errors (F12)
- Verify file input `onChange` event listener

---

## 📞 Support

**Developer:** Kartiwa Jumaludin  
**GitHub Repo:** [disposal_asset_wo](https://github.com/kartiwajumaludin-afk/disposal_asset_wo)  
**Documentation:** `docs/CSV_BATCH_IMPORT.md`  

**Related Documents:**
- `PROJECT_MASTER_PLAN.md` - Overall project architecture
- `README.md` - Project setup guide

---

## 📝 Changelog

### **v1.0.0 (2025-02-08)**
- ✅ Initial release
- ✅ Upload 4 CSV files (ticket, asset, workinfo, manual)
- ✅ Chunked processing (1000 rows/chunk)
- ✅ Progress bar UI
- ✅ Results display
- ✅ Error handling & rollback

### **Future Enhancements**
- [ ] Real-time progress bar (WebSocket/Server-Sent Events)
- [ ] Download sample CSV templates
- [ ] CSV validation preview before upload
- [ ] Scheduled/background processing (Queue)
- [ ] Email notification on completion
- [ ] Download processing log