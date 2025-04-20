<?php

namespace App\Application\Shared\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait FileUploadTrait
{
    /**
     * @throws Throwable
     */
    public function uploadToS3AndReturnUrl($sheet): string
    {
        $filePath = Storage::disk('public')->path($sheet);
        $file = new UploadedFile($filePath, $sheet);
        $uploadFile = $this->uploadFile($file, $sheet);

        $url = Storage::disk('s3')->url($uploadFile);

        return $this->formatS3Url($url);
    }

    public function uploadFile(array|UploadedFile|null $file, $path, string $disk = 's3'): array|string
    {
        if (is_array($file)) {
            $uploadedFiles = [];

            collect($file)->each(function ($file) use (&$uploadedFiles, $path, $disk) {
                $uploadedFiles[] = $file->storePublicly($path, $disk);
            });

            return $uploadedFiles;
        }

        if (empty($file)) {
            Storage::disk($disk)->put($path, '');
        } else {
            Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));
        }

        return $path;
    }

    public function formatS3Url(string $url): string
    {
        $bucket = config('filesystems.disks.s3.bucket');

        if (! str_contains($url, $bucket)) {
            $url = preg_replace("/^https:\/\//", 'https://'.$bucket.'.', $url);
        }

        return $url;
    }

    /**
     * @throws Throwable
     */
    public function deleteFile($file, string $disk = 's3'): void
    {
        if (! Storage::disk($disk)->exists($file)) {
            throw_if(true, NotFoundHttpException::class);
        } else {
            Storage::disk($disk)->delete($file);
        }
    }
}
