<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @brief Processes About slider images into normalized WebP files.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
class AboutPhotoImageProcessor
{
    private const TARGET_WIDTH = 960;
    private const TARGET_HEIGHT = 550;
    private const TARGET_RELATIVE_DIR = 'uploads/about';

    /**
     * @brief Converts an uploaded image to a cropped WebP file.
     *
     * @param UploadedFile $file The uploaded image file.
     * @param string $projectDir The project root directory.
     * @return string The relative public path of the generated WebP file.
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function processToWebp(UploadedFile $file, string $projectDir): string
    {
        $this->assertGdSupport();
        $sourceImage = $this->createSourceImage($file);

        $sourceWidth = (int) imagesx($sourceImage);
        $sourceHeight = (int) imagesy($sourceImage);
        [$cropX, $cropY, $cropWidth, $cropHeight] = $this->computeCenteredCrop(
            $sourceWidth,
            $sourceHeight,
            self::TARGET_WIDTH,
            self::TARGET_HEIGHT
        );

        $targetImage = imagecreatetruecolor(self::TARGET_WIDTH, self::TARGET_HEIGHT);
        imagealphablending($targetImage, true);
        imagesavealpha($targetImage, true);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            self::TARGET_WIDTH,
            self::TARGET_HEIGHT,
            $cropWidth,
            $cropHeight
        );

        $uploadDir = rtrim($projectDir, '/\\') . '/public/' . self::TARGET_RELATIVE_DIR;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            imagedestroy($sourceImage);
            imagedestroy($targetImage);
            throw new \RuntimeException('Unable to create destination directory for About photos.');
        }

        $filename = sprintf('about-photo-%s.webp', bin2hex(random_bytes(8)));
        $absolutePath = $uploadDir . '/' . $filename;
        $written = imagewebp($targetImage, $absolutePath, 85);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if ($written === false) {
            throw new \RuntimeException('Unable to convert image to WebP.');
        }

        return self::TARGET_RELATIVE_DIR . '/' . $filename;
    }

    /**
     * @brief Validates GD extension and WebP support.
     *
     * @return void
     * @date 2026-03-19
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
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function createSourceImage(UploadedFile $file)
    {
        $path = $file->getPathname();
        $mimeType = (string) $file->getMimeType();

        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            default => throw new \RuntimeException('Unsupported image format for conversion.'),
        };
    }

    /**
     * @brief Computes a centered crop rectangle preserving target ratio.
     *
     * @param int $sourceWidth The source width.
     * @param int $sourceHeight The source height.
     * @param int $targetWidth The target width.
     * @param int $targetHeight The target height.
     * @return array{0:int,1:int,2:int,3:int} The crop x, y, width and height.
     * @date 2026-03-19
     * @author Stephane H.
     */
    private function computeCenteredCrop(int $sourceWidth, int $sourceHeight, int $targetWidth, int $targetHeight): array
    {
        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropX = (int) floor(($sourceWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) floor(($sourceHeight - $cropHeight) / 2);
        }

        return [$cropX, $cropY, $cropWidth, $cropHeight];
    }
}

