<?php

use App\Models\Category;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Running Verification (Create/Verify/Delete)...\n";

// 1. Cleanup old test data
Category::where('name', 'Sync Test Category')->delete();
DB::connection('mysql')->table('categories')->where('name', 'Sync Test Category')->delete();

// 2. Create in SQLite (via Eloquent)
echo "Step 1: Creating category in SQLite...\n";
$cat = Category::create(['name' => 'Sync Test Category']);
echo "Category ID: {$cat->id}\n";

// 3. Verify in SQLite
$sqliteCount = Category::where('name', 'Sync Test Category')->count();
echo "SQLite 'Sync Test Category' count: $sqliteCount\n";

// 4. Verify in MySQL (via DB facade)
$mysqlCount = DB::connection('mysql')->table('categories')->where('name', 'Sync Test Category')->count();
echo "MySQL 'Sync Test Category' count: $mysqlCount\n";

if ($sqliteCount === 1 && $mysqlCount === 1) {
    echo "SUCCESS: Write Synchronization is working!\n";
    
    // 5. Test Deletion
    echo "Step 2: Deleting category from SQLite...\n";
    $cat->delete();
    
    $sqliteCountFinal = Category::where('name', 'Sync Test Category')->count();
    $mysqlCountFinal = DB::connection('mysql')->table('categories')->where('name', 'Sync Test Category')->count();
    
    echo "Final SQLite count: $sqliteCountFinal\n";
    echo "Final MySQL count: $mysqlCountFinal\n";
    
    if ($sqliteCountFinal === 0 && $mysqlCountFinal === 0) {
        echo "SUCCESS: Delete Synchronization is also working!\n";
    } else {
        echo "FAILURE: Delete Synchronization failed.\n";
    }
} else {
    echo "FAILURE: Write Synchronization failed.\n";
}
