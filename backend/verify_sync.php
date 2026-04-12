<?php

use App\Models\Category;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Running Verification...\n";

$sqliteCount = Category::where('name', 'Sync Test Category')->count();
$mysqlCount = DB::connection('mysql')->table('categories')->where('name', 'Sync Test Category')->count();

echo "SQLite 'Sync Test Category' count: $sqliteCount\n";
echo "MySQL 'Sync Test Category' count: $mysqlCount\n";

if ($sqliteCount > 0 && $sqliteCount === $mysqlCount) {
    echo "SUCCESS: Synchronization is working!\n";
    // Cleanup the test category
    Category::where('name', 'Sync Test Category')->delete();
    echo "Test record deleted.\n";
} else {
    echo "FAILURE: Synchronization failed.\n";
}
