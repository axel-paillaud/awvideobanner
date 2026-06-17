<?php
/**
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2007-2024 Axelweb
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace Axelweb\AwVideoBanner\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoHelper
{
    const UPLOAD_SUBDIR = 'video/awvideobanner/';
    const MAX_SIZE_BYTES = 209715200; // 200 MB
    const WARN_SIZE_BYTES = 31457280;  // 30 MB
    const ALLOWED_EXTENSIONS = ['mp4', 'webm'];
    const ALLOWED_MIME_TYPES = ['video/mp4', 'video/webm'];

    public static function getUploadDir(): string
    {
        return _PS_IMG_DIR_ . self::UPLOAD_SUBDIR;
    }

    public static function getUploadUrl(): string
    {
        return __PS_BASE_URI__ . 'img/' . self::UPLOAD_SUBDIR;
    }

    public static function ensureUploadDir(): bool
    {
        $dir = self::getUploadDir();

        if (is_dir($dir)) {
            return true;
        }

        return mkdir($dir, 0755, true);
    }

    public static function getCurrentVideoUrl(): string
    {
        foreach (self::ALLOWED_EXTENSIONS as $ext) {
            if (file_exists(self::getUploadDir() . 'banner.' . $ext)) {
                return self::getUploadUrl() . 'banner.' . $ext;
            }
        }

        return '';
    }

    /**
     * Validates and moves an uploaded video file.
     *
     * @return array{filename?: string, warning?: string, error?: string}
     */
    public static function processUpload(UploadedFile $file): array
    {
        $size = $file->getSize();
        $extension = strtolower($file->getClientOriginalExtension());

        if ($size > self::MAX_SIZE_BYTES) {
            return ['error' => sprintf(
                'File too large (%d MB). Maximum allowed is 200 MB.',
                (int) ($size / 1048576)
            )];
        }

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return ['error' => 'Only .mp4 and .webm files are accepted.'];
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return ['error' => sprintf(
                'Invalid MIME type "%s". Only video/mp4 and video/webm are accepted.',
                $mimeType
            )];
        }

        if (!self::validateMagicBytes($file->getPathname(), $extension)) {
            return ['error' => 'File content does not match a valid video format.'];
        }

        self::ensureUploadDir();

        $filename = 'banner.' . $extension;

        // Remove the other format to avoid leaving stale files
        foreach (self::ALLOWED_EXTENSIONS as $ext) {
            if ($ext !== $extension) {
                $stale = self::getUploadDir() . 'banner.' . $ext;
                if (file_exists($stale)) {
                    unlink($stale);
                }
            }
        }

        try {
            $file->move(self::getUploadDir(), $filename);
        } catch (\Exception $e) {
            return ['error' => 'Failed to save video file: ' . $e->getMessage()];
        }

        $result = ['filename' => $filename];

        if ($size > self::WARN_SIZE_BYTES) {
            $result['warning'] = sprintf(
                'The video (%d MB) is large for a web background. Consider optimizing it for better performance.',
                (int) ($size / 1048576)
            );
        }

        return $result;
    }

    private static function validateMagicBytes(string $filePath, string $extension): bool
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $bytes = fread($handle, 12);
        fclose($handle);

        if (!is_string($bytes)) {
            return false;
        }

        if ($extension === 'mp4') {
            // MP4: 'ftyp' box at bytes 4–7
            return strlen($bytes) >= 8 && substr($bytes, 4, 4) === 'ftyp';
        }

        if ($extension === 'webm') {
            // WebM EBML magic: 1A 45 DF A3
            return strlen($bytes) >= 4 && substr($bytes, 0, 4) === "\x1a\x45\xdf\xa3";
        }

        return false;
    }
}
