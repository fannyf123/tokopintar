<?php

namespace App\Services;

use RuntimeException;

/**
 * Uploader sederhana ke Cloudflare R2 (S3-compatible) memakai AWS Signature V4.
 * Tanpa dependensi tambahan — pakai curl + hash_hmac bawaan PHP.
 * Kredensial diambil dari environment (config/services.php -> r2).
 */
class R2Service
{
    private string $accountId;
    private string $accessKey;
    private string $secretKey;
    private string $bucket;
    private string $endpoint;

    public function __construct()
    {
        $this->accountId = (string) config('services.r2.account_id');
        $this->accessKey = (string) config('services.r2.access_key');
        $this->secretKey = (string) config('services.r2.secret_key');
        $this->bucket = (string) config('services.r2.bucket');
        $this->endpoint = rtrim((string) config('services.r2.endpoint'), '/');
    }

    public function isConfigured(): bool
    {
        return $this->accessKey !== '' && $this->secretKey !== ''
            && $this->bucket !== '' && $this->endpoint !== '';
    }

    /**
     * Upload isi file ke R2. Mengembalikan key objek bila sukses.
     */
    public function put(string $key, string $body, string $contentType = 'application/json'): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('R2 belum dikonfigurasi (cek environment variable).');
        }

        $host = parse_url($this->endpoint, PHP_URL_HOST);
        $region = 'auto';
        $service = 's3';
        $now = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');

        $path = '/' . rawurlencode($this->bucket) . '/' . str_replace('%2F', '/', rawurlencode($key));
        $payloadHash = hash('sha256', $body);

        $canonicalHeaders = "host:{$host}\n"
            . "x-amz-content-sha256:{$payloadHash}\n"
            . "x-amz-date:{$now}\n";
        $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';

        $canonicalRequest = "PUT\n{$path}\n\n{$canonicalHeaders}\n{$signedHeaders}\n{$payloadHash}";

        $scope = "{$date}/{$region}/{$service}/aws4_request";
        $stringToSign = "AWS4-HMAC-SHA256\n{$now}\n{$scope}\n" . hash('sha256', $canonicalRequest);

        $kDate = hash_hmac('sha256', $date, 'AWS4' . $this->secretKey, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', $service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $authorization = "AWS4-HMAC-SHA256 Credential={$this->accessKey}/{$scope}, "
            . "SignedHeaders={$signedHeaders}, Signature={$signature}";

        $ch = curl_init($this->endpoint . $path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => [
                "Host: {$host}",
                "x-amz-content-sha256: {$payloadHash}",
                "x-amz-date: {$now}",
                "Authorization: {$authorization}",
                "Content-Type: {$contentType}",
                'Content-Length: ' . strlen($body),
            ],
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('Gagal koneksi ke R2: ' . $err);
        }
        if ($status < 200 || $status >= 300) {
            throw new RuntimeException("Upload R2 gagal (HTTP {$status}): " . substr((string) $response, 0, 300));
        }

        return $key;
    }
}
