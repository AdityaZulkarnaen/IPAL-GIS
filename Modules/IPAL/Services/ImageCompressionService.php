<?php

namespace Modules\IPAL\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageCompressionService
{
    /**
     * Compress an uploaded image to fit within the target size, then persist it.
     * Returns the stored relative path (suitable for Storage::url()).
     */
    public function compressToMaxKb(UploadedFile $file, int $maxKb, string $subdirectory): string
    {
        $maxBytes = $maxKb * 1024;
        $image    = Image::make($file->getRealPath());

        $image->orientate();

        $quality  = 90;
        $encoded  = $image->encode('jpg', $quality);

        while (strlen($encoded->getEncoded()) > $maxBytes && $quality > 10) {
            $quality -= 5;
            $encoded = $image->encode('jpg', $quality);
        }

        $fileName        = Str::uuid() . '.jpg';
        $relativePath    = 'aduan/' . $subdirectory . '/' . $fileName;
        $absolutePath    = Storage::disk('public')->path($relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        file_put_contents($absolutePath, $encoded->getEncoded());

        return $relativePath;
    }
}
