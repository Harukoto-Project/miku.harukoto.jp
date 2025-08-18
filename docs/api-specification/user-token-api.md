# User Token API Specification

## Overview

This document specifies the HTTP API endpoints for user token validation and permission checking in the Miku system. The API is designed to be implemented in TypeScript and provides secure token validation for external services.

## Base URL

```
https://your-domain.com/api
```

## Authentication

All endpoints require no authentication as they are designed to validate tokens from external services.

## Endpoints

### 1. Validate User Token

Validates a user's API token and returns user information and permissions.

**Endpoint:** `POST /api/validate-token`

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```typescript
interface ValidateTokenRequest {
  token: string;      // User's API token (max 64 characters)
  user_id: number;    // User ID to validate
}
```

**Response Types:**

**Success Response (200):**
```typescript
interface ValidateTokenSuccessResponse {
  valid: true;
  user: {
    id: number;
    name: string;
    discord_username: string | null;
    discord_display_name: string | null;
  };
  permissions: string[];
  expires_at: string; // ISO 8601 timestamp
  message: string;
}
```

**Error Responses:**

**Token Not Found (401):**
```typescript
interface ValidateTokenErrorResponse {
  valid: false;
  error: "Token not found";
  message: "APIトークンが設定されていません";
  code: 401;
}
```

**Invalid Token (401):**
```typescript
interface ValidateTokenErrorResponse {
  valid: false;
  error: "Invalid token";
  message: "無効なAPIトークンです";
  code: 401;
}
```

**Token Expired (401):**
```typescript
interface ValidateTokenErrorResponse {
  valid: false;
  error: "Token expired";
  message: "APIトークンの有効期限が切れています";
  code: 401;
}
```

**User Not Found (404):**
```typescript
interface ValidateTokenErrorResponse {
  valid: false;
  error: "User not found";
  message: "指定されたユーザーが見つかりません";
  code: 404;
}
```

**Validation Error (400):**
```typescript
interface ValidateTokenValidationErrorResponse {
  valid: false;
  error: "Validation failed";
  message: "リクエストの形式が正しくありません";
  details: Record<string, string[]>;
  code: 400;
}
```

**Internal Server Error (500):**
```typescript
interface ValidateTokenErrorResponse {
  valid: false;
  error: "Internal server error";
  message: "サーバー内部エラーが発生しました";
  code: 500;
}
```

### 2. Check User Permission

Checks if a user has a specific permission after validating their token.

**Endpoint:** `POST /api/check-permission`

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```typescript
interface CheckPermissionRequest {
  token: string;      // User's API token (max 64 characters)
  user_id: number;    // User ID to check
  permission: string; // Permission to check (e.g., "welcomer.read")
}
```

**Response Types:**

**Success Response (200):**
```typescript
interface CheckPermissionSuccessResponse {
  has_permission: boolean;
  user_id: number;
  required_permission: string;
  user_permissions: string[];
  message: string;
}
```

**Error Responses:**

Same error response structure as validate-token endpoint, but with `has_permission: false`.

## Permission System

### Permission Levels

| Permission | Description | Scope |
|------------|-------------|-------|
| `*` | All permissions (admin) | All endpoints |
| `welcomer.read` | Read Welcomer settings | Welcomer configuration |
| `welcomer.write` | Write Welcomer settings | Welcomer configuration |

### Permission Checking Logic

1. **Wildcard Permission**: If user has `*` permission, they have access to everything
2. **Specific Permission**: User must have the exact permission requested
3. **No Permission**: If user lacks required permission, access is denied

## Implementation Examples

### TypeScript Implementation

#### Basic Token Validation

```typescript
interface TokenValidationService {
  validateToken(token: string, userId: number): Promise<ValidateTokenResponse>;
  checkPermission(token: string, userId: number, permission: string): Promise<CheckPermissionResponse>;
}

class MikuTokenValidationService implements TokenValidationService {
  private baseUrl: string;

  constructor(baseUrl: string) {
    this.baseUrl = baseUrl;
  }

  async validateToken(token: string, userId: number): Promise<ValidateTokenResponse> {
    try {
      const response = await fetch(`${this.baseUrl}/validate-token`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ token, user_id: userId }),
      });

      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${data.message}`);
      }

      return data;
    } catch (error) {
      console.error('Token validation failed:', error);
      throw error;
    }
  }

  async checkPermission(token: string, userId: number, permission: string): Promise<CheckPermissionResponse> {
    try {
      const response = await fetch(`${this.baseUrl}/check-permission`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ token, user_id: userId, permission }),
      });

      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${data.message}`);
      }

      return data;
    } catch (error) {
      console.error('Permission check failed:', error);
      throw error;
    }
  }
}
```

#### Usage Example

```typescript
const tokenService = new MikuTokenValidationService('https://your-domain.com/api');

// Validate token
try {
  const validation = await tokenService.validateToken('user_token_here', 123);
  
  if (validation.valid) {
    console.log('Token is valid');
    console.log('User permissions:', validation.permissions);
    console.log('Expires at:', validation.expires_at);
  } else {
    console.log('Token is invalid:', validation.message);
  }
} catch (error) {
  console.error('Validation error:', error);
}

// Check specific permission
try {
  const permissionCheck = await tokenService.checkPermission('user_token_here', 123, 'welcomer.write');
  
  if (permissionCheck.has_permission) {
    console.log('User has required permission');
    // Proceed with operation
  } else {
    console.log('Permission denied:', permissionCheck.message);
  }
} catch (error) {
  console.error('Permission check error:', error);
}
```

#### Error Handling

```typescript
class TokenValidationError extends Error {
  constructor(
    public code: number,
    public error: string,
    public message: string
  ) {
    super(message);
    this.name = 'TokenValidationError';
  }
}

async function handleTokenValidation(token: string, userId: number): Promise<void> {
  try {
    const validation = await tokenService.validateToken(token, userId);
    
    if (!validation.valid) {
      throw new TokenValidationError(
        validation.code,
        validation.error,
        validation.message
      );
    }
    
    // Token is valid, proceed with operation
    console.log('Token validated successfully');
    
  } catch (error) {
    if (error instanceof TokenValidationError) {
      switch (error.code) {
        case 401:
          console.error('Authentication failed:', error.message);
          // Handle authentication error
          break;
        case 404:
          console.error('User not found:', error.message);
          // Handle user not found
          break;
        default:
          console.error('Validation error:', error.message);
      }
    } else {
      console.error('Unexpected error:', error);
    }
  }
}
```

## HTTP Status Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request (validation error) |
| 401 | Unauthorized (token invalid/expired) |
| 404 | Not Found (user not found) |
| 500 | Internal Server Error |

## Rate Limiting

Currently no rate limiting is implemented. Consider implementing rate limiting based on:
- IP address
- User ID
- Token

## Security Considerations

1. **HTTPS Required**: All API calls must use HTTPS
2. **Token Security**: Tokens should be treated as sensitive data
3. **Logging**: All validation attempts are logged for security auditing
4. **Input Validation**: All inputs are validated and sanitized
5. **Error Messages**: Error messages do not expose sensitive information

## Testing

### Test Cases

1. **Valid Token**: Should return 200 with user data
2. **Invalid Token**: Should return 401
3. **Expired Token**: Should return 401
4. **Non-existent User**: Should return 404
5. **Missing Fields**: Should return 400
6. **Invalid Field Types**: Should return 400

### Test Data

```typescript
const testCases = [
  {
    name: 'Valid token',
    request: { token: 'valid_token_123', user_id: 1 },
    expectedStatus: 200,
    expectedValid: true
  },
  {
    name: 'Invalid token',
    request: { token: 'invalid_token', user_id: 1 },
    expectedStatus: 401,
    expectedValid: false
  },
  {
    name: 'Non-existent user',
    request: { token: 'any_token', user_id: 99999 },
    expectedStatus: 404,
    expectedValid: false
  }
];
```

## Integration Guide

### Step 1: Setup

1. Install the token validation service
2. Configure the base URL
3. Set up error handling

### Step 2: Implementation

1. Validate tokens before processing requests
2. Check permissions for protected operations
3. Handle errors appropriately

### Step 3: Testing

1. Test with valid tokens
2. Test with invalid tokens
3. Test error scenarios
4. Verify logging

## Support

For technical support or questions about this API specification, please contact the development team.

---

**Version**: 1.0  
**Last Updated**: 2025-01-20  
**Author**: Miku Development Team
