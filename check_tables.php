<?php
require_once 'php/config.php';

$db = getDBConnection();
$result = $db->query("SHOW TABLES LIKE '%activity%'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($result as $table) {
    echo $table . PHP_EOL;
}
