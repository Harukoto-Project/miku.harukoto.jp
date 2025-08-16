<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMinioConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MinIO connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing MinIO connection...');

        try {
            // MinIO設定の確認
            $this->info('MinIO Configuration:');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['URL', config('filesystems.disks.minio.url') ?: 'Not set'],
                    [
                        'Endpoint',
                        config('filesystems.disks.minio.endpoint') ?: 'Not set',
                    ],
                    [
                        'Bucket',
                        config('filesystems.disks.minio.bucket') ?: 'Not set',
                    ],
                    [
                        'Region',
                        config('filesystems.disks.minio.region') ?: 'Not set',
                    ],
                    ['Key', config('filesystems.disks.minio.key') ?: 'Not set'],
                    [
                        'Secret',
                        config('filesystems.disks.minio.secret') ?: 'Not set',
                    ],
                    [
                        'Use Path Style',
                        config(
                            'filesystems.disks.minio.use_path_style_endpoint',
                        )
                            ? 'Yes'
                            : 'No',
                    ],
                ],
            );

            // 接続テスト
            $this->info('Testing connection...');

            // バケットの存在確認（空文字列ではなく、テストファイルで確認）
            $this->info('Testing bucket access...');

            // テストファイルの書き込みテスト
            $testContent = 'test_' . time();
            $testPath = 'test/test_' . time() . '.txt';

            $this->info("Testing file write to: {$testPath}");
            $testResult = \Storage::disk('minio')->put($testPath, $testContent);

            if ($testResult) {
                $this->info('✓ File write test successful');

                // ファイルの存在確認
                $fileExists = \Storage::disk('minio')->exists($testPath);
                $this->info(
                    'File exists check: ' . ($fileExists ? 'Yes' : 'No'),
                );

                // ファイルの内容確認
                $readContent = \Storage::disk('minio')->get($testPath);
                $this->info(
                    'File content check: ' .
                        ($readContent === $testContent ? 'Match' : 'Mismatch'),
                );

                // テストファイルを削除
                $deleteResult = \Storage::disk('minio')->delete($testPath);
                $this->info(
                    'File delete test: ' .
                        ($deleteResult ? 'Success' : 'Failed'),
                );

                $this->info('✓ MinIO connection test completed successfully');
                return 0;
            } else {
                $this->error('✗ File write test failed');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('MinIO connection test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
