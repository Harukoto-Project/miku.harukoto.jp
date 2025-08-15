<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class MinioService
{
    private string $disk;
    private string $bucket;
    private string $publicUrl;

    public function __construct()
    {
        \Log::info('MinioService constructor called');

        $this->disk = config('filesystems.default') ?: 'minio';
        \Log::info('Disk set to: ' . $this->disk);

        // MinIO設定を直接取得
        $minioConfig = config('filesystems.disks.minio');
        \Log::info('MinIO config: ' . json_encode($minioConfig));

        if (!$minioConfig) {
            throw new \Exception(
                'MinIO configuration not found. Please check your filesystems config.',
            );
        }

        $this->bucket =
            $minioConfig['bucket'] ??
            throw new \Exception('MinIO bucket not configured');
        $this->publicUrl =
            $minioConfig['url'] ??
            throw new \Exception('MinIO public URL not configured');

        \Log::info('Bucket set to: ' . $this->bucket);
        \Log::info('Public URL set to: ' . $this->publicUrl);

        // 設定値の検証
        if (empty($this->bucket) || empty($this->publicUrl)) {
            \Log::warning(
                'MinIO configuration incomplete, falling back to local storage',
            );
            $this->disk = 'local';
            $this->bucket = 'local';
            $this->publicUrl = url('/storage');
        }

        \Log::info('MinioService constructor completed successfully');
    }

    /**
     * 署名付きURLを生成（アップロード用）
     */
    public function generatePresignedUrl(
        string $fileName,
        string $contentType,
        int $expiresIn = 3600,
    ): array {
        try {
            $s3Client = Storage::disk($this->disk)->getClient();

            $command = $s3Client->getCommand('PutObject', [
                'Bucket' => $this->bucket,
                'Key' => "portfolio/avatars/{$fileName}",
                'ContentType' => $contentType,
            ]);

            $request = $s3Client->createPresignedRequest(
                $command,
                "+{$expiresIn} seconds",
            );

            return [
                'signedUrl' => (string) $request->getUri(),
                'fileName' => $fileName,
                'publicUrl' => "{$this->publicUrl}/{$this->bucket}/portfolio/avatars/{$fileName}",
                'expiresAt' => now()->addSeconds($expiresIn)->toISOString(),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate presigned URL', [
                'file_name' => $fileName,
                'content_type' => $contentType,
                'expires_in' => $expiresIn,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception(
                'Failed to generate presigned URL: ' . $e->getMessage(),
            );
        }
    }

    /**
     * ファイルを削除
     */
    public function deleteFile(string $filePath): bool
    {
        try {
            if (empty($filePath)) {
                \Log::warning('Attempted to delete empty file path');
                return false;
            }

            $result = Storage::disk($this->disk)->delete($filePath);

            if ($result) {
                \Log::info('File deleted successfully', [
                    'file_path' => $filePath,
                ]);
            } else {
                \Log::warning('File deletion returned false', [
                    'file_path' => $filePath,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('MinIO file deletion failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * ファイルの存在確認
     */
    public function fileExists(string $filePath): bool
    {
        try {
            if (empty($filePath)) {
                return false;
            }

            return Storage::disk($this->disk)->exists($filePath);
        } catch (\Exception $e) {
            \Log::error('File existence check failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * ファイルのURLを取得
     */
    public function getFileUrl(string $filePath): string
    {
        try {
            if (empty($filePath)) {
                throw new \InvalidArgumentException(
                    'File path cannot be empty',
                );
            }

            return Storage::disk($this->disk)->url($filePath);
        } catch (\Exception $e) {
            \Log::error('Failed to get file URL', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * アバターファイルのパスを取得
     */
    public function getAvatarPath(string $fileName): string
    {
        return "portfolio/avatars/{$fileName}";
    }

    /**
     * ファイルサイズを取得
     */
    public function getFileSize(string $filePath): int
    {
        try {
            return Storage::disk($this->disk)->size($filePath);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * ファイルのメタデータを取得
     */
    public function getFileMetadata(string $filePath): array
    {
        try {
            return Storage::disk($this->disk)->getMetadata($filePath);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 古いファイルをクリーンアップ
     */
    public function cleanupOldFiles(string $directory, int $maxAge = 86400): int
    {
        $deletedCount = 0;
        $files = Storage::disk($this->disk)->files($directory);

        foreach ($files as $file) {
            $lastModified = Storage::disk($this->disk)->lastModified($file);

            if (time() - $lastModified > $maxAge) {
                if ($this->deleteFile($file)) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * ファイルの一時URLを生成（ダウンロード用）
     */
    public function generateTemporaryUrl(
        string $filePath,
        int $expiresIn = 3600,
    ): string {
        try {
            return Storage::disk($this->disk)->temporaryUrl(
                $filePath,
                now()->addSeconds($expiresIn),
            );
        } catch (\Exception $e) {
            // 一時URLがサポートされていない場合は通常のURLを返す
            return $this->getFileUrl($filePath);
        }
    }
}
