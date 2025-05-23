<?php

namespace App\Services;

use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    /**
     * Optimize and resize an image using Spatie Image and Image Optimizer
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $path
     * @param int $width
     * @param int $height
     * @return string
     */
    public function optimizeAndResize($image, $path, $width = 600, $height = 400)
    {
        // Generate unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $storagePath = storage_path('app/public/' . $path);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        $fullPath = $storagePath . '/' . $filename;

        // Move the uploaded file to storage
        $image->move($storagePath, $filename);

        // Resize using Spatie Image
        Image::load($fullPath)
            ->width($width)
            ->height($height)
            ->save();

        // Optimize using Spatie Image Optimizer
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($fullPath);

        // Return the relative path for storage
        return $path . '/' . $filename;
    }
} 