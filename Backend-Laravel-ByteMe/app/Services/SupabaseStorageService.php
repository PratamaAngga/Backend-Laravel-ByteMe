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
          ->post($uploadUrl);

        if ($response->successful()) {
            $publicUrl = "{$this->url}/storage/v1/object/public/{$encodedBucket}/{$fileName}";
            Log::info('SupabaseStorageService: upload berhasil', ['publicUrl' => $publicUrl]);
            return $publicUrl;
        }

        Log::error('SupabaseStorageService: upload gagal', [
            'status'   => $response->status(),
            'response' => $response->body(),
        ]);

        return false;
    }

    public function delete(string $fileName): bool
    {
        $encodedBucket = implode('/', array_map('rawurlencode', explode('/', $this->bucket)));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
        ])->delete("{$this->url}/storage/v1/object/{$encodedBucket}/{$fileName}");

        return $response->successful();
    }
}