<?php
/**
 * PDO connection: MySQL (sbm_db from database.sql) first,
 * automatic fallback to the bundled SQLite file sbm_database.db.
 */
function get_db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=sbm_db;charset=utf8mb4', 'root', '', $opts + [PDO::ATTR_TIMEOUT => 2]);
    } catch (Throwable $e) {
        $pdo = new PDO('sqlite:' . dirname(__DIR__) . '/sbm_database.db', null, null, $opts);
    }
    return $pdo;
}
