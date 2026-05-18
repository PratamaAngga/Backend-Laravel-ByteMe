<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url    = config('services.supabase.url');
        $this->key    = config('services.supabase.key');
        $this->bucket = config('services.supabase.bucket');
    }

    public function upload(string $filePath, string $fileName, string $mimeType): string|false
    {
        return $this->uploadToBucket($filePath, $fileName, $mimeType, $this->bucket);
    }

    public function uploadToBucket(string $filePath, string $fileName, string $mimeType, string $bucket): string|false
    {
        $fileContents = file_get_contents($filePath);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => $mimeType,
            'x-upsert'      => 'true',
        ])->withBody($fileContents, $mimeType)
        ->post("{$this->url}/storage/v1/object/{$bucket}/{$fileName}");

        if ($response->successful()) {
            return "{$this->url}/storage/v1/object/public/{$bucket}/{$fileName}";
        }

        return false;
    }

    public function delete(string $fileName): bool
    {
        return $this->deleteFromBucket($fileName, $this->bucket);
    }

    public function deleteFromBucket(string $fileName, string $bucket): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
        ])->delete("{$this->url}/storage/v1/object/{$bucket}/{$fileName}");

        return $response->successful();
    }
}