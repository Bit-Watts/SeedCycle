<?php

/**
 * DB — lightweight query helper to eliminate repeated mysqli boilerplate.
 * All methods accept the connection, SQL, type string, and params array.
 */
class DB {

    /** Execute a query and return a single row, or null */
    public static function fetch($conn, string $sql, string $types = '', array $params = []): ?array {
        $stmt = mysqli_prepare($conn, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /** Execute a query and return all rows as an array */
    public static function fetchAll($conn, string $sql, string $types = '', array $params = []): array {
        if ($params) {
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rows   = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $rows;
        }
        $result = mysqli_query($conn, $sql);
        $rows   = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /** Execute a query and return true/false */
    public static function execute($conn, string $sql, string $types = '', array $params = []): bool {
        $stmt = mysqli_prepare($conn, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Check if a row exists */
    public static function exists($conn, string $sql, string $types = '', array $params = []): bool {
        $stmt = mysqli_prepare($conn, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    /** Execute and return the last insert ID */
    public static function insert($conn, string $sql, string $types = '', array $params = []): int|false {
        $stmt = mysqli_prepare($conn, $sql);
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }
        $id = (int)mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $id;
    }

    /** Count rows from a simple count query */
    public static function count($conn, string $sql, string $types = '', array $params = []): int {
        $row = self::fetch($conn, $sql, $types, $params);
        return (int)($row[array_key_first($row ?? [])] ?? 0);
    }
}
