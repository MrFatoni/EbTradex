<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    public function upload($file, $filePath, $fileName, $prefix = '', $suffix = '', $disk = null, $width = null, $height = null, $fileExtension = 'png', $quality = 100)
    {
        if (is_null($disk)) {
            $disk = config('filesystems.default');
        }

        $mimeType = $file->getClientMimeType();
        $imageMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];

        if (in_array($mimeType, $imageMimeTypes)) {
            $imageFile = Image::make($file);

            if (!is_null($width) && !is_null($height) && is_int($width) && is_int($height)) {
                $imageFile->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $background = Image::canvas($width, $height);
                $imageFile = $background->insert($imageFile, 'center');
            }

            $imageFile->encode($fileExtension, $quality);
            $fileName = md5($prefix . '_' . $fileName . '_' . $suffix) . '.' . $fileExtension;
            $path = $filePath . '/' . $fileName;
            $stored = Storage::disk($disk)->put($path, $imageFile->__toString());
        } else {
            $fileName = md5($prefix . '_' . $fileName . '_' . $suffix) . '.' . $file->getClientOriginalExtension();
            $stored = $file->storeAs($filePath, $fileName, $disk);
        }

        return isset($stored) ? $fileName : false;
    }
}