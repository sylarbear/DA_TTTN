<?php
// Run migration v2
require_once __DIR__ . '/../app/config/database.php';
$db = getDB();
$sql = file_get_contents(__DIR__ . '/../database/migration_v2.sql');
// Split by semicolons and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));
$ok = 0; $err = 0;
foreach ($statements as $stmt) {
    if (empty($stmt) || strpos($stmt, '--') === 0) continue;
    try {
        $db->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        echo "WARN: " . $e->getMessage() . "\n";
        $err++;
    }
}
echo "Done: $ok OK, $err warnings\n";
