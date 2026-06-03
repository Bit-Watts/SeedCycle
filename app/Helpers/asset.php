<?php

/**
 * Asset helper — appends a cache-busting query string to local asset URLs.
 *
 * Uses the file's last-modified timestamp so the version updates automatically
 * whenever the file changes on disk, without any manual version bumping.
 *
 * Usage:
 *   <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
 *   <script src="<?= asset('assets/js/app.js') ?>"></script>
 *
 * For admin views that use '../assets/css/...' paths, the function
 * strips the leading '../' for filesystem resolution but preserves it
 * in the returned URL so the browser resolves the path correctly.
 *
 * @param string $path  URL-relative path to the asset (e.g. 'assets/css/base.css'
 *                      or '../assets/css/base.css' for admin views one level deep)
 */
function asset(string $path): string
{
    // Normalise path for filesystem lookup (strip leading ../ or /)
    $fsPath = ltrim(preg_replace('#^\.\./+#', '', $path), '/');

    // Resolve absolute path from the public root
    $publicRoot = dirname(__DIR__, 2) . '/public/';
    $absPath    = $publicRoot . $fsPath;

    $version = file_exists($absPath) ? filemtime($absPath) : time();

    return $path . '?v=' . $version;
}
