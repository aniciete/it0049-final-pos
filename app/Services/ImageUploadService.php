<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploadService
{
    public const AVATAR_DIMENSION = 256;

    public const PRODUCT_DIMENSION = 640;

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

        $image = Image::decode($file->get())
            ->cover($dimension, $dimension);

        $encoded = match ($extension) {
            'png' => $image->encodeUsingFormat(Format::PNG),
            'webp' => $image->encodeUsingFormat(Format::WEBP, quality: 85),
            default => $image->encodeUsingFormat(Format::JPEG, quality: 85),
        };

        $relativePath = $folder.'/'.Str::uuid().'.'.$extension;

        Storage::disk('public')->put($relativePath, (string) $encoded);

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
     * Store a 640x640 product image on the public disk and return its relative path.
     */
    public function storeProductImage(UploadedFile $file): string
    {
        return $this->storeResized($file, 'products', self::PRODUCT_DIMENSION);
    }

    /**
     * Remove an existing uploaded image from the public filesystem disk.
     */
    public function delete(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        if (! Storage::disk('public')->exists($relativePath)) {
            return false;
        }

        return Storage::disk('public')->delete($relativePath);
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
