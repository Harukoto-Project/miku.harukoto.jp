<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ManageApiTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-tokens:manage 
                            {action : Action to perform (list|revoke|renew|info|cleanup)}
                            {--user-id= : Target specific user ID}
                            {--email= : Target user by email}
                            {--expired-only : Only process expired tokens (for renew/revoke actions)}
                            {--dry-run : Show what would be done without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage API tokens for users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        switch ($action) {
            case 'list':
                return $this->listTokens();
            case 'revoke':
                return $this->revokeTokens($dryRun);
            case 'renew':
                return $this->renewTokens($dryRun);
            case 'info':
                return $this->showTokenInfo();
            case 'cleanup':
                return $this->cleanupExpiredTokens($dryRun);
            default:
                $this->error("Unknown action: {$action}");
                $this->error('Available actions: list, revoke, renew, info, cleanup');
                return 1;
        }
    }

    /**
     * List all users with their token status
     */
    private function listTokens(): int
    {
        $users = User::all(['id', 'name', 'email', 'api_token', 'api_token_expires_at', 'api_permissions']);

        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return 0;
        }

        $this->info('User API Token Status:');
        $this->newLine();

        $tableData = [];
        foreach ($users as $user) {
            $status = $this->getTokenStatus($user);
            $permissions = $user->api_permissions ? implode(', ', $user->api_permissions) : 'None';
            
            $tableData[] = [
                $user->id,
                $user->name,
                $user->email,
                $status,
                $permissions,
                $user->api_token_expires_at?->format('Y-m-d H:i:s') ?? 'N/A',
            ];
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Token Status', 'Permissions', 'Expires At'],
            $tableData
        );

        return 0;
    }

    /**
     * Revoke tokens for users
     */
    private function revokeTokens(bool $dryRun): int
    {
        $users = $this->getTargetUsers();
        
        if ($users->isEmpty()) {
            $this->warn('No users found for token revocation.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) for token revocation.");

        if (!$dryRun && !$this->confirm('Are you sure you want to revoke these tokens?')) {
            $this->info('Token revocation cancelled.');
            return 0;
        }

        $revokedCount = 0;
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if (!empty($user->api_token)) {
                if (!$dryRun) {
                    $user->revokeApiToken();
                }
                $revokedCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("DRY RUN: Would have revoked {$revokedCount} token(s)");
        } else {
            $this->info("Successfully revoked {$revokedCount} token(s)");
        }

        return 0;
    }

    /**
     * Renew expired tokens
     */
    private function renewTokens(bool $dryRun): int
    {
        $users = $this->getTargetUsers();
        
        if ($users->isEmpty()) {
            $this->warn('No users found for token renewal.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) for token renewal.");

        if (!$dryRun && !$this->confirm('Are you sure you want to renew these tokens?')) {
            $this->info('Token renewal cancelled.');
            return 0;
        }

        $renewedCount = 0;
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if ($user->isApiTokenExpired()) {
                if (!$dryRun) {
                    $user->generateApiToken();
                }
                $renewedCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("DRY RUN: Would have renewed {$renewedCount} token(s)");
        } else {
            $this->info("Successfully renewed {$renewedCount} token(s)");
        }

        return 0;
    }

    /**
     * Show detailed token information for a specific user
     */
    private function showTokenInfo(): int
    {
        $users = $this->getTargetUsers();
        
        if ($users->isEmpty()) {
            $this->error('No users found. Please specify --user-id or --email option.');
            return 1;
        }

        foreach ($users as $user) {
            $this->info("Token Information for User: {$user->name} ({$user->email})");
            $this->line('─' . str_repeat('─', 50));
            
            $tokenInfo = $user->getApiTokenInfo();
            
            $this->line("Token Status: " . ($tokenInfo['has_token'] ? '✅ Active' : '❌ None'));
            
            if ($tokenInfo['has_token']) {
                $this->line("Expires At: " . $tokenInfo['expires_at']);
                $this->line("Is Expired: " . ($tokenInfo['is_expired'] ? 'Yes' : 'No'));
                $this->line("Permissions: " . implode(', ', $tokenInfo['permissions']));
                $this->line("Can Auto-Renew: " . ($tokenInfo['can_renew'] ? 'Yes' : 'No'));
            }
            
            $this->newLine();
        }

        return 0;
    }

    /**
     * Clean up expired tokens
     */
    private function cleanupExpiredTokens(bool $dryRun): int
    {
        $users = User::whereNotNull('api_token')
            ->where('api_token_expires_at', '<', now())
            ->get();

        if ($users->isEmpty()) {
            $this->info('No expired tokens found.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) with expired tokens.");

        if (!$dryRun && !$this->confirm('Are you sure you want to clean up expired tokens?')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        $cleanedCount = 0;
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if (!$dryRun) {
                $user->revokeApiToken();
            }
            $cleanedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("DRY RUN: Would have cleaned up {$cleanedCount} expired token(s)");
        } else {
            $this->info("Successfully cleaned up {$cleanedCount} expired token(s)");
        }

        return 0;
    }

    /**
     * Get target users based on options
     */
    private function getTargetUsers()
    {
        $query = User::query();

        if ($userId = $this->option('user-id')) {
            $query->where('id', $userId);
        }

        if ($email = $this->option('email')) {
            $query->where('email', $email);
        }

        if ($this->option('expired-only')) {
            $query->whereNotNull('api_token')
                  ->where('api_token_expires_at', '<', now());
        }

        return $query->get();
    }

    /**
     * Get human-readable token status
     */
    private function getTokenStatus(User $user): string
    {
        if (empty($user->api_token)) {
            return '❌ No Token';
        }

        if ($user->isApiTokenExpired()) {
            return '⚠️  Expired';
        }

        return '✅ Active';
    }
}
