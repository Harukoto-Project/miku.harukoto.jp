<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignApiTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-tokens:assign 
                            {--force : Force assignment even if user already has a token}
                            {--permissions= : Comma-separated list of permissions to assign}
                            {--expiration= : Token expiration time in hours}
                            {--user-id= : Assign token to specific user ID only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign API tokens to users with default permissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting API token assignment...');

        // Get options
        $force = $this->option('force');
        $permissions = $this->getPermissionsOption();
        $expiration = $this->getExpirationOption();
        $userId = $this->option('user-id');

        // Build query
        $query = User::query();
        
        if ($userId) {
            $query->where('id', $userId);
        }

        if (!$force) {
            $query->whereNull('api_token');
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn('No users found for token assignment.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) for token assignment.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $assignedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                if (!$force && !empty($user->api_token)) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                // Generate token with specified permissions and expiration
                $token = $user->generateApiToken($permissions, $expiration);
                
                $assignedCount++;
                
                // Log successful assignment
                if (config('api-tokens.security.logging.enabled', true)) {
                    Log::info('API token assigned via command', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'permissions' => $permissions,
                        'expiration_hours' => $expiration,
                    ]);
                }

            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display results
        $this->info("Token assignment completed!");
        $this->info("✅ Successfully assigned: {$assignedCount}");
        
        if ($skippedCount > 0) {
            $this->warn("⏭️  Skipped: {$skippedCount}");
        }

        if (!empty($errors)) {
            $this->error("❌ Errors: " . count($errors));
            
            if ($this->option('verbose')) {
                $this->table(['User ID', 'Email', 'Error'], collect($errors)->map(function ($error) {
                    return [$error['user_id'], $error['email'], $error['error']];
                })->toArray());
            }
        }

        // Display configuration used
        $this->newLine();
        $this->info('Configuration used:');
        $this->line("  • Permissions: " . implode(', ', $permissions));
        $this->line("  • Expiration: {$expiration} hours");
        $this->line("  • Force mode: " . ($force ? 'Yes' : 'No'));

        return empty($errors) ? 0 : 1;
    }

    /**
     * Get permissions from command option or use defaults
     */
    private function getPermissionsOption(): array
    {
        $permissionsOption = $this->option('permissions');
        
        if ($permissionsOption) {
            return array_map('trim', explode(',', $permissionsOption));
        }

        return config('api-tokens.default_permissions', ['welcomer.read']);
    }

    /**
     * Get expiration from command option or use defaults
     */
    private function getExpirationOption(): int
    {
        $expirationOption = $this->option('expiration');
        
        if ($expirationOption) {
            $expiration = (int) $expirationOption;
            
            // Validate expiration
            $maxExpiration = config('api-tokens.token.max_expiration_hours', 168);
            if ($expiration > $maxExpiration) {
                $this->warn("Expiration time {$expiration} hours exceeds maximum {$maxExpiration} hours. Using maximum value.");
                $expiration = $maxExpiration;
            }
            
            return $expiration;
        }

        return config('api-tokens.token.default_expiration_hours', 24);
    }
}
