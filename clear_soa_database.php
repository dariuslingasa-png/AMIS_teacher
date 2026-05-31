<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Starting clean of Statement of Account (SOA) database tables...\n";

try {
    Schema::disableForeignKeyConstraints();
    
    echo "Truncating student_account_payments...\n";
    DB::table('student_account_payments')->truncate();
    
    echo "Truncating soa_monthly_billings...\n";
    DB::table('soa_monthly_billings')->truncate();
    
    echo "Truncating student_accounts...\n";
    DB::table('student_accounts')->truncate();
    
    Schema::enableForeignKeyConstraints();
    
    echo "SUCCESS: All SOA tables successfully cleared! Database is empty and ready for fresh enrollment approval testing.\n";
} catch (\Exception $e) {
    echo "ERROR during truncation: " . $e->getMessage() . "\n";
}
