# Automatic API Token Generation System

## Overview

The Miku system now automatically generates API tokens for new users with configurable default permissions. This document explains how the system works and how to use it.

## How It Works

### 1. Automatic Token Generation

When a new user is created, the system automatically:
- Generates a secure random API token
- Assigns default permissions from configuration
- Sets an expiration time
- Logs the token generation

### 2. Configuration-Driven

All settings are managed through the `config/api-tokens.php` file:
- Default permissions
- Token expiration times
- Security settings
- Logging preferences

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# API Token Configuration
API_TOKEN_DEFAULT_EXPIRATION_HOURS=24
API_TOKEN_MAX_EXPIRATION_HOURS=168
API_TOKEN_LENGTH_BYTES=32
API_TOKEN_AUTO_GENERATE=true
API_TOKEN_AUTO_RENEW=false

# Security Settings
API_TOKEN_HASH_TOKENS=false
API_TOKEN_RATE_LIMIT_MAX=50
API_TOKEN_RATE_LIMIT_DECAY=15
API_TOKEN_LOGGING_ENABLED=true
API_TOKEN_LOGGING_LEVEL=info

# Notification Settings
API_TOKEN_NOTIFY_ON_GENERATION=false
API_TOKEN_NOTIFY_ON_EXPIRATION=true
API_TOKEN_EXPIRATION_NOTIFICATION_HOURS=1
```

### Default Permissions

Edit `config/api-tokens.php` to modify default permissions:

```php
'default_permissions' => [
    'welcomer.read',
    'welcomer.write',
    // Add more as needed
],
```

## Usage Examples

### 1. Creating a New User

```php
use App\Models\User;

// Create a new user - token will be generated automatically
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
]);

// Token is automatically generated and assigned
echo "Generated token: " . $user->api_token;
echo "Permissions: " . implode(', ', $user->api_permissions);
echo "Expires at: " . $user->api_token_expires_at;
```

### 2. Manual Token Generation

```php
// Generate token with custom permissions
$token = $user->generateApiToken([
    'welcomer.read',
    'welcomer.write',
    'admin.read'
], 48); // 48 hours expiration

// Generate token with default settings
$token = $user->generateApiToken();
```

### 3. Permission Checking

```php
// Check specific permission
if ($user->hasApiPermission('welcomer.write')) {
    // User can write welcomer settings
}

// Check multiple permissions
if ($user->hasAnyApiPermission(['welcomer.read', 'welcomer.write'])) {
    // User has at least one of these permissions
}

if ($user->hasAllApiPermissions(['welcomer.read', 'welcomer.write'])) {
    // User has all of these permissions
}
```

### 4. Token Management

```php
// Check token status
$tokenInfo = $user->getApiTokenInfo();

if ($tokenInfo['is_expired']) {
    // Token is expired
}

// Renew expired token
if ($user->renewApiTokenIfExpired()) {
    echo "Token renewed successfully";
}

// Revoke token
$user->revokeApiToken();
```

## Artisan Commands

### 1. Assign API Tokens

Assign tokens to existing users:

```bash
# Assign tokens to all users without tokens
php artisan api-tokens:assign

# Force assignment (overwrite existing tokens)
php artisan api-tokens:assign --force

# Assign with custom permissions
php artisan api-tokens:assign --permissions="welcomer.read,welcomer.write"

# Assign with custom expiration
php artisan api-tokens:assign --expiration=72

# Assign to specific user
php artisan api-tokens:assign --user-id=123
```

### 2. Manage API Tokens

Comprehensive token management:

```bash
# List all users and their token status
php artisan api-tokens:manage list

# Show token info for specific user
php artisan api-tokens:manage info --user-id=123
php artisan api-tokens:manage info --email=user@example.com

# Revoke tokens
php artisan api-tokens:manage revoke --user-id=123
php artisan api-tokens:manage revoke --expired-only

# Renew expired tokens
php artisan api-tokens:manage renew --expired-only

# Clean up expired tokens
php artisan api-tokens:manage cleanup

# Dry run (show what would be done)
php artisan api-tokens:manage revoke --expired-only --dry-run
```

## Integration with Existing Code

### 1. MikuApiService

The existing `MikuApiService` can now be used with automatically generated tokens:

```php
use App\Services\MikuApiService;

// Get user with auto-generated token
$user = User::find(1);

// Initialize service with user
$mikuApi = new MikuApiService($user);

// Use the service - token validation is automatic
$settings = $mikuApi->getWelcomerSettings('guild_id');
```

### 2. API Token Validation

The token validation API works seamlessly with auto-generated tokens:

```php
// Validate auto-generated token
$response = Http::post('/api/validate-token', [
    'token' => $user->api_token,
    'user_id' => $user->id
]);

if ($response->successful()) {
    $result = $response->json();
    // Token is valid with default permissions
}
```

## Security Features

### 1. Automatic Logging

All token operations are logged:
- Token generation
- Token revocation
- Permission changes
- Validation attempts

### 2. Configurable Expiration

- Default expiration: 24 hours
- Maximum expiration: 1 week
- Configurable per token

### 3. Permission Validation

- Wildcard permissions (`*`) for admin access
- Granular permission control
- Permission inheritance

## Best Practices

### 1. Token Management

- Use short expiration times for security
- Implement token rotation
- Monitor token usage

### 2. Permission Design

- Follow principle of least privilege
- Use descriptive permission names
- Group related permissions

### 3. Monitoring

- Enable logging for security auditing
- Monitor token generation patterns
- Track permission usage

## Troubleshooting

### 1. Token Not Generated

Check configuration:
```bash
php artisan config:show api-tokens
```

Verify auto-generation is enabled:
```php
config('api-tokens.token.auto_generate') // Should be true
```

### 2. Permission Issues

Check default permissions:
```php
config('api-tokens.default_permissions')
```

Verify user permissions:
```php
$user->api_permissions // Should contain expected permissions
```

### 3. Expiration Issues

Check expiration settings:
```php
config('api-tokens.token.default_expiration_hours')
config('api-tokens.token.max_expiration_hours')
```

## Migration from Manual Tokens

If you have existing users without tokens:

```bash
# Assign tokens to all existing users
php artisan api-tokens:assign

# Verify assignment
php artisan api-tokens:manage list

# Check specific user
php artisan api-tokens:manage info --user-id=1
```

## Support

For issues with the automatic token generation system:
1. Check the logs: `storage/logs/laravel.log`
2. Verify configuration: `config/api-tokens.php`
3. Test with Artisan commands
4. Contact the development team

---

**Version**: 1.0  
**Last Updated**: 2025-01-20  
**Author**: Miku Development Team
