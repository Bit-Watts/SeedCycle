<?php

/**
 * Returns a safe image src for seed images.
 * Handles both:
 *   - Local relative paths:  assets/uploads/listings/file.jpg
 *   - External absolute URLs: https://perenual.com/storage/...
 *
 * @param string $imageUrl  Raw value from seed_images.image_url
 * @param string $fallback  Returned when $imageUrl is empty
 */
function seedImageSrc(string $imageUrl, string $fallback = ''): string {
    if (empty($imageUrl)) return $fallback;
    // Already an absolute URL — return as-is
    if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
        return $imageUrl;
    }
    // Relative path — return as-is (browser resolves from current page)
    return $imageUrl;
}
