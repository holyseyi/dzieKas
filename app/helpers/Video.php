<?php

/**
 * Video Upload Helper
 *
 * @package DzieKas\Helpers
 */

declare(strict_types=1);

namespace App\Helpers;

class Video
{
    /**
     * Upload a video file.
     *
     * @param array<string, mixed> $file $_FILES array element
     * @return string|false Relative path to saved file
     */
    public static function upload(array $file, string $directory): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';

        $maxSize = $config['upload_max_video_size'] ?? 500 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $config['allowed_video_types'] ?? ['video/mp4', 'video/webm'], true)) {
            return false;
        }

        $extension = match ($mimeType) {
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            default => 'mp4',
        };

        $filename = Security::generateToken(16) . '.' . $extension;
        $uploadDir = dirname(__DIR__, 2) . '/public/storage/uploads/' . $directory;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            self::generateThumbnail($destination, $uploadDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg');
            return $directory . '/' . $filename;
        }

        return false;
    }

    /**
     * Generate a video thumbnail using ffmpeg.
     */
    private static function generateThumbnail(string $videoPath, string $thumbnailPath): bool
    {
        if (!file_exists($videoPath)) {
            return false;
        }

        // Check if exec() is available
        $disabled = explode(',', ini_get('disable_functions'));
        if (in_array('exec', $disabled, true)) {
            error_log('Video::generateThumbnail: exec() is disabled on this server');
            return false;
        }

        // Check if ffmpeg is installed
        exec('which ffmpeg 2>/dev/null', $whichOutput, $whichReturnCode);
        if ($whichReturnCode !== 0) {
            error_log('Video::generateThumbnail: ffmpeg is not installed on this server');
            return false;
        }

        $cmd = sprintf(
            'ffmpeg -y -i %s -ss 00:00:01 -vframes 1 -q:v 2 %s 2>/dev/null',
            escapeshellarg($videoPath),
            escapeshellarg($thumbnailPath)
        );

        exec($cmd, $output, $returnCode);

        return $returnCode === 0 && file_exists($thumbnailPath);
    }

    /**
     * Get public URL for an uploaded video.
     */
    public static function url(?string $path): string
    {
        if (!$path) {
            return '';
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';
        return rtrim($config['url'], '/') . '/storage/uploads/' . ltrim($path, '/');
    }
}
