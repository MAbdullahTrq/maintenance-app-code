<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    /**
     * Optimize and resize an image
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $path
     * @param int $width
     * @param int $height
     * @return string
     */
    public function optimizeAndResize($image, $path, $width = 600, $height = 400)
    {
        // Create image instance
        $img = Image::make($image);

        // Get original dimensions
        $originalWidth = $img->width();
        $originalHeight = $img->height();

        // Calculate new dimensions while maintaining aspect ratio
        if ($originalWidth > $originalHeight) {
            // Landscape image
            $newWidth = $width;
            $newHeight = ($originalHeight / $originalWidth) * $width;
        } else {
            // Portrait image
            $newHeight = $height;
            $newWidth = ($originalWidth / $originalHeight) * $height;
        }

        // Resize image
        $img->resize($newWidth, $newHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Optimize image quality
        $img->encode('jpg', 80);

        // Generate unique filename
        $filename = uniqid() . '.jpg';

        // Store the image
        Storage::put('public/' . $path . '/' . $filename, $img->stream());

        return $path . '/' . $filename;
    }
} 