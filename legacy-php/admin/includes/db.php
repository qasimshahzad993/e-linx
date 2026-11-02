<?php
// Database settings (XAMPP defaults). Change only if yours differ.
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'elinx_db';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (Throwable $ex) {
    http_response_code(500);
    exit('Database connection failed. Check XAMPP MySQL is running and includes/db.php settings.');
}

function count_of($conn, $table, $where = '1') {
    try { return (int)$conn->query("SELECT COUNT(*) c FROM `$table` WHERE $where")->fetch_assoc()['c']; }
    catch (Throwable $e) { return 0; }
}

// Finds the first column that exists in a table (so different column names still work)
function col($conn, $table, $candidates) {
    try {
        $have = [];
        $res = $conn->query("SHOW COLUMNS FROM `$table`");
        while ($r = $res->fetch_assoc()) $have[] = $r['Field'];
        foreach ($candidates as $c) if (in_array($c, $have, true)) return $c;
    } catch (Throwable $e) {}
    return null;
}
// Insert (id=0) or update a row. $data = ['column' => [value, 's'|'i'|'d']]
function save_row($conn, $table, $id, $data) {
    $types = ''; $vals = [];
    foreach ($data as $c => [$v, $t]) { $types .= $t; $vals[] = $v; }
    $cols = array_keys($data);
    if ($id) {
        $sql = "UPDATE `$table` SET " . implode(',', array_map(fn($c) => "`$c`=?", $cols)) . " WHERE id=?";
        $types .= 'i'; $vals[] = $id;
    } else {
        $sql = "INSERT INTO `$table` (" . implode(',', array_map(fn($c) => "`$c`", $cols)) . ") VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ")";
    }
    $st = $conn->prepare($sql);
    $st->bind_param($types, ...$vals);
    $st->execute();
}
