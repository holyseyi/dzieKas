<?php

/**
 * Image Upload & Resize Helper
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Image
{
    /**
     * Upload and optionally resize an image.
     *
     * @param array<string, mixed> $file $_FILES array element
     * @param array{width: int, height: int}|null $size Target dimensions
     * @return string|false Relative path to saved file
     */
    public static function upload(array $file, string $directory, ?array $size = null): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';

        if ($file['size'] > $config['upload_max_size']) {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $config['allowed_image_types'], true)) {
            return false;
        }

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };

        $filename = Security::generateToken(16) . '.' . $extension;
        $uploadDir = dirname(__DIR__, 2) . '/storage/uploads/' . $directory;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $filename;

        if ($size) {
            return self::resizeAndSave($file['tmp_name'], $destination, $mimeType, $size)
                ? $directory . '/' . $filename
                : false;
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $directory . '/' . $filename;
        }

        return false;
    }

    /**
     * Resize image using GD library.
     *
     * @param array{width: int, height: int} $size
     */
    private static function resizeAndSave(
        string $source,
        string $destination,
        string $mimeType,
        array $size
    ): bool {
        $srcImage = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($source),
            'image/png' => imagecreatefrompng($source),
            'image/webp' => imagecreatefromwebp($source),
            'image/gif' => imagecreatefromgif($source),
            default => false,
        };

        if (!$srcImage) {
            return false;
        }

        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);

        $ratio = min($size['width'] / $srcWidth, $size['height'] / $srcHeight);
        $newWidth = (int) ($srcWidth * $ratio);
        $newHeight = (int) ($srcHeight * $ratio);

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        $saved = match ($mimeType) {
            'image/jpeg' => imagejpeg($dstImage, $destination, 85),
            'image/png' => imagepng($dstImage, $destination, 8),
            'image/webp' => imagewebp($dstImage, $destination, 85),
            'image/gif' => imagegif($dstImage, $destination),
            default => false,
        };

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return (bool) $saved;
    }

    /**
     * Get public URL for an uploaded image.
     */
    public static function url(?string $path): string
    {
        if (!$path) {
            return '/assets/images/placeholder-poster.jpg';
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';
        return rtrim($config['url'], '/') . '/storage/uploads/' . ltrim($path, '/');
    }
}
