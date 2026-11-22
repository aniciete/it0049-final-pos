<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Format;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploadService
{
    public const AVATAR_DIMENSION = 256;

    public const PRODUCT_DIMENSION = 640;

    public const PRODUCT_THUMB_DIMENSION = 96;

    /**
     * Decode, resize/crop into a display-ready square thumbnail, encode cleanly,
     * and store onto the public filesystem disk.
     *
     * Reading raw file bytes via $file->get() avoids open_basedir/non-listable
     * PHP temp directory issues on shared hosting environments.
     */
    public function storeResized(UploadedFile $file, string $directory, int $dimension): string
    {
        $extension = $this->resolveSafeExtension($file);
        $folder = trim($directory, '/');
        $filename = Str::uuid().'.'.$extension;
        $relativePath = $folder.'/'.$filename;

        $image = Image::decode($file->get())
            ->cover($dimension, $dimension);

        Storage::disk('public')->put($relativePath, $this->encodeForExtension($image, $extension, 85));

        return $relativePath;
    }

    /**
     * Store a 256x256 staff avatar on the public disk and return its relative path.
     */
    public function storeAvatar(UploadedFile $file): string
    {
        return $this->storeResized($file, 'avatars', self::AVATAR_DIMENSION);
    }

    /**
     * Store a 640x640 product image and its 96x96 list thumbnail on the public disk.
     */
    public function storeProductImage(UploadedFile $file): string
    {
        $extension = $this->resolveSafeExtension($file);
        $filename = Str::uuid().'.'.$extension;
        $relativePath = 'products/'.$filename;

        $image = Image::decode($file->get())
            ->cover(self::PRODUCT_DIMENSION, self::PRODUCT_DIMENSION);

        Storage::disk('public')->put($relativePath, $this->encodeForExtension($image, $extension, 85));

        $thumb = $image->cover(self::PRODUCT_THUMB_DIMENSION, self::PRODUCT_THUMB_DIMENSION);
        Storage::disk('public')->put('products/thumbs/'.$filename, $this->encodeForExtension($thumb, $extension, 82));

        return $relativePath;
    }

    /**
     * Derive the thumbnail relative path for a stored product image.
     */
    public static function thumbnailRelativePath(?string $relativePath): ?string
    {
        if (empty($relativePath) || ! str_starts_with($relativePath, 'products/')) {
            return null;
        }

        return 'products/thumbs/'.basename($relativePath);
    }

    /**
     * Remove an existing uploaded image (and any product thumbnail derivative) from disk.
     */
    public function delete(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $deleted = false;
        if (Storage::disk('public')->exists($relativePath)) {
            $deleted = Storage::disk('public')->delete($relativePath);
        }

        $thumbPath = self::thumbnailRelativePath($relativePath);
        if ($thumbPath !== null && Storage::disk('public')->exists($thumbPath)) {
            Storage::disk('public')->delete($thumbPath);
        }

        return $deleted;
    }

    /**
     * Encode an Intervention Image instance using the normalized target format.
     */
    private function encodeForExtension(ImageInterface $image, string $extension, int $quality): string
    {
        $encoded = match ($extension) {
            'png' => $image->encodeUsingFormat(Format::PNG),
            'webp' => $image->encodeUsingFormat(Format::WEBP, quality: $quality),
            default => $image->encodeUsingFormat(Format::JPEG, quality: $quality),
        };

        return (string) $encoded;
    }

    /**
     * Normalize validated image extensions so stored files match their encoded output.
     */
    private function resolveSafeExtension(UploadedFile $file): string
    {
        $guessed = strtolower((string) ($file->guessExtension() ?: $file->extension()));

        return match ($guessed) {
            'png' => 'png',
            'webp' => 'webp',
            default => 'jpg',
        };
    }
}
