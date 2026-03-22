<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @brief Converts home service card images to WebP format without resizing.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class HomeServiceCardImageProcessor
{
    private const TARGET_RELATIVE_DIR = 'uploads/home-service-cards';

    /**
     * @brief Converts an uploaded image to WebP, preserving original dimensions.
     *
     * @param UploadedFile $file The uploaded image file.
     * @param string $projectDir The project root directory.
     * @param int $slot The card slot (1–5).
     * @return string The relative public path of the generated WebP file.
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function processToWebp(UploadedFile $file, string $projectDir, int $slot): string
    {
        if ($slot < 1 || $slot > 5) {
            throw new \InvalidArgumentException('Service card slot must be between 1 and 5.');
        }

        $this->assertGdSupport();
        $sourceImage = $this->createSourceImage($file);

        imagealphablending($sourceImage, true);
        imagesavealpha($sourceImage, true);

        $uploadDir = rtrim($projectDir, '/\\') . '/public/' . self::TARGET_RELATIVE_DIR;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            imagedestroy($sourceImage);
            throw new \RuntimeException('Unable to create destination directory for home service card images.');
        }

        $filename = sprintf('home-service-card-%d-%s.webp', $slot, bin2hex(random_bytes(8)));
        $absolutePath = $uploadDir . '/' . $filename;
        $written = imagewebp($sourceImage, $absolutePath, 85);

        imagedestroy($sourceImage);

        if ($written === false) {
            throw new \RuntimeException('Unable to convert home service card image to WebP.');
        }

        return self::TARGET_RELATIVE_DIR . '/' . $filename;
    }

    /**
     * @brief Validates GD extension and WebP support.
     *
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function assertGdSupport(): void
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagewebp')) {
            throw new \RuntimeException('GD extension with WebP support is required.');
        }
    }

    /**
     * @brief Creates a GD image resource from an uploaded file.
     *
     * @param UploadedFile $file The uploaded file.
     * @return \GdImage|resource The source image resource.
     * @date 2026-03-22
     * @author Stephane H.
     */
    private function createSourceImage(UploadedFile $file)
    {
        $path = $file->getPathname();
        $mimeType = (string) $file->getMimeType();

        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/gif' => imagecreatefromgif($path),
            default => throw new \RuntimeException('Unsupported image format for conversion.'),
        };
    }
}
