<?php
/**
 * db.php — PDO connection helper.
 * Include this (it includes config.php) wherever DB access is needed.
 */
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        // Bluehost's MySQL server defaults to US time; force this
        // session to Indian Standard Time (+05:30) so CURRENT_TIMESTAMP
        // columns (created_at, etc.) match config.php's timezone.
        $pdo->exec("SET time_zone = '+05:30'");
    }
    return $pdo;
}

/**
 * Run a SELECT and return all rows as an associative array.
 * Returns [] on failure (and logs the error) instead of throwing,
 * so pages can render gracefully even if a query fails.
 */
function query_all(string $sql, array $params = []): array {
    try {
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log('Query Error: ' . $e->getMessage());
        return [];
    }
}

/** Run a SELECT and return the first row, or null. */
function query_one(string $sql, array $params = []): ?array {
    $rows = query_all($sql, $params);
    return $rows[0] ?? null;
}

/**
 * Run an INSERT/UPDATE/DELETE.
 * Returns true on success, false on failure (and logs the error).
 */
function execute(string $sql, array $params = []): bool {
    try {
        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    } catch (Throwable $e) {
        error_log('Query Error: ' . $e->getMessage());
        return false;
    }
}

/** Last insert id from the most recent INSERT on this connection. */
function last_insert_id(): string {
    return db()->lastInsertId();
}
