<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function useBucket(string $bucketConfigKey): static
    {
        $this->bucket = config('services.supabase.' . $bucketConfigKey);
        return $this;
    }

    public function upload(string $filePath, string $fileName, string $mimeType): string|false
    {
        return $this->uploadToBucket($filePath, $fileName, $mimeType, $this->bucket);
    }

    public function uploadToBucket(string $filePath, string $fileName, string $mimeType, string $bucket): string|false
    {
        $fileContents = file_get_contents($filePath);

        if ($fileContents === false) {
            Log::error('SupabaseStorageService: gagal membaca file', ['path' => $filePath]);
            return false;
        }

        // Encode nama bucket agar spasi dan karakter khusus aman di URL
        $encodedBucket = implode('/', array_map('rawurlencode', explode('/', $this->bucket)));

        $uploadUrl = "{$this->url}/storage/v1/object/{$encodedBucket}/{$fileName}";

        Log::info('SupabaseStorageService: upload ke', ['url' => $uploadUrl]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => $mimeType,
            'x-upsert'      => 'true', // timpa file lama jika ada (user upload ulang)
        ])->withBody($fileContents, $mimeType)
        ->post("{$this->url}/storage/v1/object/{$bucket}/{$fileName}");

        if ($response->successful()) {
            return "{$this->url}/storage/v1/object/public/{$bucket}/{$fileName}";
        }

        Log::error('SupabaseStorageService: upload gagal', [
            'status'   => $response->status(),
            'response' => $response->body(),
        ]);

        return false;
    }

    public function delete(string $fileName): bool
    {
        return $this->deleteFromBucket($fileName, $this->bucket);
    }

    public function deleteFromBucket(string $fileName, string $bucket): bool
    {
        return $this->deleteFromBucket($fileName, $this->bucket);
    }

    public function deleteFromBucket(string $fileName, string $bucket): bool
    {
        $encodedBucket = implode('/', array_map('rawurlencode', explode('/', $this->bucket)));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
        ])->delete("{$this->url}/storage/v1/object/{$bucket}/{$fileName}");

        return $response->successful();
    }
}