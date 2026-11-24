<?php

try {
    $db = new PDO(
        'sqlite:' . __DIR__ . '/database/database.sqlite'
    );

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "PEMERIKSAAN DATABASE SQLITE\n";
    echo "============================\n\n";

    $tables = $db->query("
        SELECT name
        FROM sqlite_master
        WHERE type = 'table'
        AND name NOT LIKE 'sqlite_%'
    ")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $count = $db->query(
            "SELECT COUNT(*) FROM \"$table\""
        )->fetchColumn();

        echo $table . " : " . $count . " data\n";
    }

    echo "\nPemeriksaan selesai.";

} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}