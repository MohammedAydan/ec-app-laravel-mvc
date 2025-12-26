<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageService
{
    private const MAX_FILE_SIZE = 5120; // 5MB in KB
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const PREVIEW_WIDTH = 400;
    private const THUMBNAIL_SIZE = 200;
    private const WATERMARK_OPACITY = 35;

    /**
     * Save uploaded image with security measures
     */
    public function saveImage($image): array
    {
        // Validate image
        $this->validateImage($image);

        // Generate secure filename
        $extension = $image->getClientOriginalExtension();
        $filename = $this->generateSecureFilename($extension);
        $path = 'images/items/' . $filename;

        // Save original image
        $image->move(public_path('images/items'), $filename);

        // Apply security watermark
        $fullPath = public_path($path);
        $this->applyInvisibleWatermark($fullPath);

        // Generate tracking hash
        $hash = $this->generateSecurityHash($filename);

        return [
            'url' => url($path),
            'path' => $path,
            'hash' => $hash,
            'filename' => $filename
        ];
    }

    /**
     * Generate preview with modern watermark
     */
    public function generatePreview(string $imagePath): string
    {
        $fullPath = public_path($imagePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('Image not found');
        }

        $info = getimagesize($fullPath);
        $sourceImage = $this->createImageResource($fullPath, $info[2]);

        if (!$sourceImage) {
            throw new \Exception('Unable to create image resource');
        }

        // Create preview with proper dimensions
        $preview = $this->createResizedImage($sourceImage, self::PREVIEW_WIDTH, $info);

        // Apply modern watermark
        $this->applyModernWatermark($preview);

        // Save preview
        $previewPath = $this->savePreviewImage($preview, $imagePath, $info[2]);

        // Cleanup
        imagedestroy($sourceImage);
        imagedestroy($preview);

        return url($previewPath);
    }

    /**
     * Create thumbnail without watermark
     */
    public function createThumbnail(string $imagePath, int $size = self::THUMBNAIL_SIZE): string
    {
        $fullPath = public_path($imagePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('Image not found');
        }

        $info = getimagesize($fullPath);
        $sourceImage = $this->createImageResource($fullPath, $info[2]);

        if (!$sourceImage) {
            throw new \Exception('Unable to create image resource');
        }

        $thumbnail = $this->createSquareThumbnail($sourceImage, $size);

        // Save thumbnail
        $thumbnailPath = $this->saveThumbnailImage($thumbnail, $imagePath, $info[2]);

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return url($thumbnailPath);
    }

    /**
     * Delete image and its variants
     */
    public function deleteImage(string $imagePath): bool
    {
        $fullPath = public_path($imagePath);
        $directory = dirname($fullPath);
        $filename = basename($fullPath);

        $deleted = false;

        // Delete original
        if (file_exists($fullPath)) {
            unlink($fullPath);
            $deleted = true;
        }

        // Delete preview
        $previewPath = $directory . '/preview_' . $filename;
        if (file_exists($previewPath)) {
            unlink($previewPath);
        }

        // Delete thumbnail
        $thumbnailPath = $directory . '/thumb_' . $filename;
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        return $deleted;
    }

    /**
     * Validate image security
     */
    public function validateImageAccess(string $imagePath, string $hash): bool
    {
        if (!file_exists(public_path($imagePath))) {
            return false;
        }

        $filename = basename($imagePath);
        $expectedHash = $this->generateSecurityHash($filename);

        return hash_equals($expectedHash, $hash);
    }

    // ============ Private Helper Methods ============

    private function validateImage($image): void
    {
        // Check file size
        if ($image->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw new \Exception('File size exceeds maximum allowed size');
        }

        // Check MIME type
        if (!in_array($image->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Invalid file type');
        }

        // Additional security check - verify it's actually an image
        $imageInfo = getimagesize($image->getRealPath());
        if ($imageInfo === false) {
            throw new \Exception('File is not a valid image');
        }
    }

    private function generateSecureFilename(string $extension): string
    {
        return date('Y-m-d') . '_' . Str::random(32) . '.' . $extension;
    }

    private function generateSecurityHash(string $filename): string
    {
        return hash_hmac('sha256', $filename, config('app.key'));
    }

    private function createImageResource(string $path, int $type)
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => null,
        };
    }

    private function createResizedImage($sourceImage, int $targetWidth, array $info)
    {
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        $targetHeight = (int)floor($originalHeight * ($targetWidth / $originalWidth));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency
        if ($info[2] == IMAGETYPE_PNG || $info[2] == IMAGETYPE_GIF || $info[2] == IMAGETYPE_WEBP) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled(
            $resized,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $originalWidth,
            $originalHeight
        );

        return $resized;
    }

    private function createSquareThumbnail($sourceImage, int $size)
    {
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // Calculate crop dimensions
        $minDimension = min($originalWidth, $originalHeight);
        $srcX = (int)(($originalWidth - $minDimension) / 2);
        $srcY = (int)(($originalHeight - $minDimension) / 2);

        $thumbnail = imagecreatetruecolor($size, $size);

        // Preserve transparency
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
        imagefilledrectangle($thumbnail, 0, 0, $size, $size, $transparent);

        imagecopyresampled(
            $thumbnail,
            $sourceImage,
            0,
            0,
            $srcX,
            $srcY,
            $size,
            $size,
            $minDimension,
            $minDimension
        );

        return $thumbnail;
    }

    /**
     * Apply modern, professional watermark
     */
    private function applyModernWatermark($image): void
    {
        $width = imagesx($image);
        $height = imagesy($image);

        // Create watermark overlay
        $watermarkHeight = (int)($height * 0.12);
        $watermarkWidth = (int)($width * 0.7);

        $x = (int)(($width - $watermarkWidth) / 2);
        $y = (int)(($height - $watermarkHeight) / 2);

        // Modern frosted glass effect
        $this->applyFrostedGlassEffect($image, $x, $y, $watermarkWidth, $watermarkHeight);

        // Add text
        $this->addModernWatermarkText($image, $x, $y, $watermarkWidth, $watermarkHeight);

        // Add subtle corner badge
        $this->addCornerBadge($image, $width, $height);
    }

    private function applyFrostedGlassEffect($image, int $x, int $y, int $width, int $height): void
    {
        // Create semi-transparent background with blur effect
        $overlay = imagecreatetruecolor($width, $height);
        imagealphablending($overlay, false);
        imagesavealpha($overlay, true);

        // Copy area to blur
        imagecopy($overlay, $image, 0, 0, $x, $y, $width, $height);

        // Apply blur
        for ($i = 0; $i < 5; $i++) {
            imagefilter($overlay, IMG_FILTER_GAUSSIAN_BLUR);
        }

        // Add frosted glass tint
        $tint = imagecolorallocatealpha($overlay, 255, 255, 255, 90);
        imagefilledrectangle($overlay, 0, 0, $width, $height, $tint);

        // Add gradient border
        $this->addGradientBorder($overlay, $width, $height);

        // Merge back
        imagecopy($image, $overlay, $x, $y, 0, 0, $width, $height);
        imagedestroy($overlay);
    }

    private function addGradientBorder($image, int $width, int $height): void
    {
        $borderSize = 2;

        // Top and bottom gradient
        for ($i = 0; $i < $borderSize; $i++) {
            $alpha = (int)(50 + ($i * 20));
            $color = imagecolorallocatealpha($image, 100, 150, 255, $alpha);

            imageline($image, 0, $i, $width, $i, $color);
            imageline($image, 0, $height - $i - 1, $width, $height - $i - 1, $color);
            imageline($image, $i, 0, $i, $height, $color);
            imageline($image, $width - $i - 1, 0, $width - $i - 1, $height, $color);
        }
    }

    private function addModernWatermarkText($image, int $x, int $y, int $width, int $height): void
    {
        $fontPath = public_path('fonts/arial.ttf');

        if (!file_exists($fontPath)) {
            $this->addFallbackText($image, $x, $y, $width, $height);
            return;
        }

        // Main text
        $text = "PREVIEW";
        $fontSize = (int)($height * 0.35);

        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        $textX = $x + (int)(($width - $textWidth) / 2);
        $textY = $y + (int)(($height + $textHeight) / 2) - 5;

        // Text shadow
        $shadow = imagecolorallocatealpha($image, 0, 0, 0, 70);
        imagettftext($image, $fontSize, 0, $textX + 2, $textY + 2, $shadow, $fontPath, $text);

        // Main text with gradient
        $textColor = imagecolorallocatealpha($image, 80, 130, 255, 40);
        imagettftext($image, $fontSize, 0, $textX, $textY, $textColor, $fontPath, $text);

        // Subtitle
        $subtitle = "For Display Only";
        $subFontSize = (int)($fontSize * 0.35);

        $subBbox = imagettfbbox($subFontSize, 0, $fontPath, $subtitle);
        $subWidth = $subBbox[2] - $subBbox[0];

        $subX = $x + (int)(($width - $subWidth) / 2);
        $subY = $textY + 20;

        $subColor = imagecolorallocatealpha($image, 120, 120, 120, 50);
        imagettftext($image, $subFontSize, 0, $subX, $subY, $subColor, $fontPath, $subtitle);
    }

    private function addFallbackText($image, int $x, int $y, int $width, int $height): void
    {
        $text = "PREVIEW";
        $font = 5;

        $textWidth = imagefontwidth($font) * strlen($text);
        $textX = $x + (int)(($width - $textWidth) / 2);
        $textY = $y + (int)($height / 2) - 10;

        $color = imagecolorallocatealpha($image, 100, 150, 255, 60);
        imagestring($image, $font, $textX, $textY, $text, $color);
    }

    private function addCornerBadge($image, int $width, int $height): void
    {
        $badgeSize = 60;
        $x = $width - $badgeSize - 15;
        $y = 15;

        // Create badge circle
        $badge = imagecreatetruecolor($badgeSize, $badgeSize);
        imagealphablending($badge, false);
        imagesavealpha($badge, true);

        $transparent = imagecolorallocatealpha($badge, 0, 0, 0, 127);
        imagefill($badge, 0, 0, $transparent);

        // Draw circle
        $badgeColor = imagecolorallocatealpha($badge, 80, 130, 255, 80);
        imagefilledellipse($badge, $badgeSize / 2, $badgeSize / 2, $badgeSize - 4, $badgeSize - 4, $badgeColor);

        // Add border
        $borderColor = imagecolorallocatealpha($badge, 255, 255, 255, 60);
        imageellipse($badge, $badgeSize / 2, $badgeSize / 2, $badgeSize - 4, $badgeSize - 4, $borderColor);

        // Add icon (simple lock symbol)
        $this->drawLockIcon($badge, $badgeSize);

        // Merge badge
        imagecopy($image, $badge, $x, $y, 0, 0, $badgeSize, $badgeSize);
        imagedestroy($badge);
    }

    private function drawLockIcon($image, int $size): void
    {
        $center = (int)($size / 2);
        $lockColor = imagecolorallocatealpha($image, 255, 255, 255, 50);

        // Lock body
        $bodyWidth = (int)($size * 0.4);
        $bodyHeight = (int)($size * 0.3);
        $bodyX = $center - (int)($bodyWidth / 2);
        $bodyY = $center - (int)($bodyHeight / 4);

        imagefilledrectangle($image, $bodyX, $bodyY, $bodyX + $bodyWidth, $bodyY + $bodyHeight, $lockColor);

        // Lock shackle
        $shackleWidth = (int)($size * 0.25);
        $shackleHeight = (int)($size * 0.2);
        $shackleX = $center - (int)($shackleWidth / 2);
        $shackleY = $bodyY - $shackleHeight;

        imagearc($image, $center, $bodyY, $shackleWidth * 2, $shackleHeight * 2, 180, 0, $lockColor);
    }

    /**
     * Apply invisible steganographic watermark for security
     */
    private function applyInvisibleWatermark(string $path): void
    {
        $info = getimagesize($path);
        $image = $this->createImageResource($path, $info[2]);

        if (!$image) return;

        $width = imagesx($image);
        $height = imagesy($image);

        // Add imperceptible pattern
        $pattern = $this->generateSecurityPattern($width, $height);

        for ($x = 0; $x < $width; $x += 50) {
            for ($y = 0; $y < $height; $y += 50) {
                if (isset($pattern[$x][$y])) {
                    $color = imagecolorallocatealpha($image, 255, 255, 255, 127);
                    imagesetpixel($image, $x, $y, $color);
                }
            }
        }

        // Save modified image
        $this->saveImageResource($image, $path, $info[2]);
        imagedestroy($image);
    }

    private function generateSecurityPattern(int $width, int $height): array
    {
        $pattern = [];
        $seed = (int)substr(hash('crc32', config('app.key')), 0, 8);

        for ($x = 0; $x < $width; $x += 50) {
            for ($y = 0; $y < $height; $y += 50) {
                if (($x + $y + $seed) % 100 == 0) {
                    $pattern[$x][$y] = true;
                }
            }
        }

        return $pattern;
    }

    private function saveImageResource($image, string $path, int $type): void
    {
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($image, $path, 92),
            IMAGETYPE_PNG => imagepng($image, $path, 8),
            IMAGETYPE_GIF => imagegif($image, $path),
            IMAGETYPE_WEBP => imagewebp($image, $path, 90),
            default => null,
        };
    }

    private function savePreviewImage($image, string $originalPath, int $type): string
    {
        $filename = 'preview_' . basename($originalPath);
        $previewPath = 'images/items/' . $filename;
        $fullPath = public_path($previewPath);

        $this->saveImageResource($image, $fullPath, $type);

        return $previewPath;
    }

    private function saveThumbnailImage($image, string $originalPath, int $type): string
    {
        $filename = 'thumb_' . basename($originalPath);
        $thumbnailPath = 'images/items/' . $filename;
        $fullPath = public_path($thumbnailPath);

        $this->saveImageResource($image, $fullPath, $type);

        return $thumbnailPath;
    }
}
