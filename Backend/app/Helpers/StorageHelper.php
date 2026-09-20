<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
// use Illuminate\Support\Facades\File;

class StorageHelper
{
    /**
     * Upload file to active external storage or fallback to local.
     *
     * @param string $fileContent Binary content of the file
     * @param string $fileName Name of the file (e.g., user_123.png)
     * @param string $type Optional folder type: 'profile', 'blog', 'document', etc.
     * @return string Full public URL (for external storage) or relative path (for local)
     * @throws Exception
     */
    public static function uploadToActiveStorage($fileContent, $fileName, $type = 'uploads')
    {
        try {
            // Check for first active storage provider from systemflag
            $activeStorageFlag = DB::table('systemflag')
                ->where('name', 'storege_provider')
                ->where('isActive', 1)
                ->where('isDelete', 0)
                ->first();

            // Fallback to local storage if no active provider found
            if (!$activeStorageFlag || $activeStorageFlag->value == 'local') {
                $uploadDir = public_path("storage/{$type}/");
                File::ensureDirectoryExists($uploadDir);
                $fullFilePath = $uploadDir . $fileName;
                file_put_contents($fullFilePath, $fileContent);
                $relativePath = "public/storage/{$type}/" . $fileName;
                return $relativePath;
            }

            $storageName = $activeStorageFlag->value;

            // Get storage credentials for this provider
            $keys = DB::table('systemflag')
                ->where('value', $storageName)
                ->pluck('value', 'name')
                ->toArray();

            $disk = null;
            $baseUrl = null;

            // Configure disk dynamically based on storageName
            switch ($storageName) {
                case 'digital_ocean':
                    config([
                        'filesystems.disks.digitalocean' => [
                            'driver' => 's3',
                            'key' => $keys['DigitalOceanKey'] ?? null,
                            'secret' => $keys['DigitalOceanSecretKey'] ?? null,
                            'region' => $keys['DigitalOceanRegion'] ?? null,
                            'bucket' => $keys['DigitalOceanBucket'] ?? null,
                            'endpoint' => $keys['DigitalOceanEndPoint'] ?? null,
                            'use_path_style_endpoint' => filter_var($keys['DigitalOceanPathStyle'] ?? true, FILTER_VALIDATE_BOOLEAN),
                        ]
                    ]);
                    $disk = Storage::disk('digitalocean');
                    $baseUrl = rtrim($keys['DigitalOceanEndPoint'], '/');
                    break;

                case 'aws_bucket':
                    config([
                        'filesystems.disks.awss3' => [
                            'driver' => 's3',
                            'key' => $keys['AWSAccessKey'] ?? null,
                            'secret' => $keys['AWSSecretKey'] ?? null,
                            'region' => $keys['AWSDefaultRegion'] ?? null,
                            'bucket' => $keys['AWSBucket'] ?? null,
                            'use_path_style_endpoint' => filter_var($keys['AWSUsePathStyleEndpoint'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        ]
                    ]);
                    $disk = Storage::disk('awss3');
                    $baseUrl = 'https://' . $keys['AWSBucket'] . '.s3.' . $keys['AWSDefaultRegion'] . '.amazonaws.com';
                    break;

                case 'google_bucket':
                    // Fetch Google credentials properly (same pattern as other providers)
                    $googleKeys = DB::table('systemflag')
                        ->whereIn('name', [
                            'GoogleBucketName',
                            'GoogleAccessKey',
                            'GoogleSecretKey',
                        ])
                        ->pluck('value', 'name')
                        ->toArray();

                    if (empty($googleKeys['GoogleBucketName'])) {
                        throw new Exception('Google Bucket credentials are missing or incomplete.');
                    }

                    config([
                        'filesystems.disks.google' => [
                            'driver' => 's3',
                            'key' => $googleKeys['GoogleAccessKey'] ?? '',
                            'secret' => $googleKeys['GoogleSecretKey'] ?? '',
                            'region' => 'auto',
                            'bucket' => $googleKeys['GoogleBucketName'],
                            'endpoint' => 'https://storage.googleapis.com',
                            'use_path_style_endpoint' => false,
                        ]
                    ]);

                    $disk = Storage::disk('google');
                    $baseUrl = 'https://storage.googleapis.com/' . $googleKeys['GoogleBucketName'];
                    break;

                default:
                    throw new Exception("Unsupported storage type: {$storageName}");
            }

            if (!$disk) {
                throw new Exception("Your storage credentials do not match. Please check {$storageName} credentials.");
            }

            // Build dynamic folder path
            $uploadPath = "{$type}/{$fileName}";

            // Upload file to external storage with public visibility
            $disk->put($uploadPath, $fileContent, [
                'visibility' => 'public',
            ]);

            // Build full URL safely
            $baseUrl = rtrim($baseUrl, '/');
            $uploadPath = ltrim($uploadPath, '/');
            $fullUrl = $baseUrl . '/' . $uploadPath;

            return $fullUrl;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
