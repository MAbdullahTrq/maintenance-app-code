<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        try {
            // Generate unique filename
            $extension = strtolower($image->getClientOriginalExtension());
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $storagePath = $path . '/' . $filename;
            
            // Create directory if it doesn't exist
            $fullDir = storage_path('app/public/' . $path);
            if (!is_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }
            
            $fullPath = storage_path('app/public/' . $storagePath);
            
            // Try using GD library first (most reliable)
            if (extension_loaded('gd') && $this->resizeImageGD($image->getRealPath(), $fullPath, $width, $height)) {
                Log::info('Image resized using GD library: ' . $storagePath);
                return $storagePath;
            }
            
            // Try to use Intervention Image for resizing (if available)
            if (class_exists('\Intervention\Image\Facades\Image')) {
                try {
                    $img = \Intervention\Image\Facades\Image::make($image->getRealPath());
                    
                    // Resize image maintaining aspect ratio
                    $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    
                    // Save with quality compression
                    $img->save($fullPath, 85);
                    
                    Log::info('Image resized using Intervention Image: ' . $storagePath);
                    return $storagePath;
                } catch (\Exception $e) {
                    Log::warning('Intervention Image failed: ' . $e->getMessage());
                }
            }
            
            // Try to use Spatie Image (if available)
            if (class_exists('\Spatie\Image\Image')) {
                try {
                    // Store original first
                    $image->storeAs('public/' . $path, $filename);
                    
                    \Spatie\Image\Image::load($fullPath)
                        ->width($width)
                        ->height($height)
                        ->save();
                    
                    Log::info('Image resized using Spatie Image: ' . $storagePath);
                    return $storagePath;
                } catch (\Exception $e) {
                    Log::warning('Spatie Image failed: ' . $e->getMessage());
                }
            }
            
            // Fallback: store the original file without resizing
            Log::warning('Image stored without resizing (no processing library available): ' . $storagePath);
            return $image->storeAs($path, $filename, 'public');
            
        } catch (\Exception $e) {
            // Final fallback: store the original file
            Log::error('Image processing completely failed: ' . $e->getMessage());
            return $image->store($path, 'public');
        }
    }
    
    /**
     * Resize image using GD library (most reliable method)
     */
    private function resizeImageGD($sourcePath, $destPath, $maxWidth, $maxHeight)
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) return false;
            
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            $imageType = $imageInfo[2];
            
            // Calculate new dimensions maintaining aspect ratio
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
            
            // Don't upscale if original is smaller
            if ($ratio > 1) {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }
            
            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG and GIF
            if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
                imagefill($newImage, 0, 0, $transparent);
            }
            
            // Create source image based on type
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = imagecreatefromwebp($sourcePath);
                    } else {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
            
            if (!$sourceImage) return false;
            
            // Resize with high quality
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            
            // Save based on type
            $success = false;
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $success = imagejpeg($newImage, $destPath, 85);
                    break;
                case IMAGETYPE_PNG:
                    $success = imagepng($newImage, $destPath, 6); // Compression level 6
                    break;
                case IMAGETYPE_GIF:
                    $success = imagegif($newImage, $destPath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagewebp')) {
                        $success = imagewebp($newImage, $destPath, 85);
                    }
                    break;
            }
            
            // Clean up
            imagedestroy($sourceImage);
            imagedestroy($newImage);
            
            return $success;
            
        } catch (\Exception $e) {
            Log::error('GD image processing failed: ' . $e->getMessage());
            return false;
        }
    }
} 