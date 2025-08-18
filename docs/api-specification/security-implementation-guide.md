# Security Implementation Guide

## Overview

This document provides detailed implementation guidance for the security aspects of the User Token API system. It covers database schema, middleware implementation, and security best practices.

## Database Schema

### Users Table

The existing users table should be extended with the following fields:

```sql
ALTER TABLE users ADD COLUMN api_token VARCHAR(64) UNIQUE NULL;
ALTER TABLE users ADD COLUMN api_token_expires_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN api_permissions JSON NULL;

-- Add indexes for performance
CREATE INDEX idx_users_api_token ON users(api_token);
CREATE INDEX idx_users_api_token_expires ON users(api_token_expires_at);
```

### Alternative: Separate Tokens Table

For better security and scalability, consider creating a separate table:

```sql
CREATE TABLE user_api_tokens (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    token_hash VARCHAR(255) NOT NULL, -- Store hashed tokens only
    token_salt VARCHAR(64) NOT NULL,  -- Unique salt per token
    expires_at TIMESTAMP NOT NULL,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP NULL,
    is_revoked BOOLEAN DEFAULT FALSE,
    
    INDEX idx_token_hash (token_hash),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_is_revoked (is_revoked),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Security Implementation

### 1. Token Hashing

Never store plain text tokens in the database. Always hash them:

```typescript
import * as crypto from 'crypto';

interface TokenData {
  token: string;
  salt: string;
  hash: string;
}

class TokenHasher {
  private static readonly HASH_ALGORITHM = 'sha256';
  private static readonly SALT_LENGTH = 32;
  
  static generateToken(): TokenData {
    const token = crypto.randomBytes(32).toString('hex');
    const salt = crypto.randomBytes(this.SALT_LENGTH).toString('hex');
    const hash = this.hashToken(token, salt);
    
    return { token, salt, hash };
  }
  
  static hashToken(token: string, salt: string): string {
    return crypto
      .createHash(this.HASH_ALGORITHM)
      .update(token + salt)
      .digest('hex');
  }
  
  static verifyToken(token: string, salt: string, hash: string): boolean {
    const computedHash = this.hashToken(token, salt);
    return crypto.timingSafeEqual(
      Buffer.from(computedHash, 'hex'),
      Buffer.from(hash, 'hex')
    );
  }
}
```

### 2. Middleware Implementation

#### Token Validation Middleware

```typescript
import { Request, Response, NextFunction } from 'express';

interface AuthenticatedRequest extends Request {
  user?: {
    id: number;
    permissions: string[];
    tokenData: any;
  };
}

export class TokenValidationMiddleware {
  constructor(private tokenService: TokenService) {}
  
  async validateToken(req: AuthenticatedRequest, res: Response, next: NextFunction): Promise<void> {
    try {
      const authHeader = req.headers.authorization;
      const userId = req.headers['x-user-id'];
      
      if (!authHeader || !userId) {
        res.status(400).json({
          error: 'Missing required headers',
          message: 'Authorization and X-User-ID headers are required'
        });
        return;
      }
      
      // Extract token from Bearer format
      const token = authHeader.replace('Bearer ', '');
      
      if (!token) {
        res.status(401).json({
          error: 'Invalid authorization format',
          message: 'Authorization header must be in format: Bearer <token>'
        });
        return;
      }
      
      // Validate token
      const validation = await this.tokenService.validateToken(token, parseInt(userId as string));
      
      if (!validation.valid) {
        res.status(validation.code).json({
          error: validation.error,
          message: validation.message
        });
        return;
      }
      
      // Attach user info to request
      req.user = {
        id: validation.user.id,
        permissions: validation.permissions,
        tokenData: validation
      };
      
      next();
    } catch (error) {
      console.error('Token validation middleware error:', error);
      res.status(500).json({
        error: 'Internal server error',
        message: 'Token validation failed'
      });
    }
  }
  
  async checkPermission(requiredPermission: string) {
    return async (req: AuthenticatedRequest, res: Response, next: NextFunction): Promise<void> => {
      if (!req.user) {
        res.status(401).json({
          error: 'Unauthorized',
          message: 'Token validation required before permission check'
        });
        return;
      }
      
      const hasPermission = req.user.permissions.includes('*') || 
                           req.user.permissions.includes(requiredPermission);
      
      if (!hasPermission) {
        res.status(403).json({
          error: 'Forbidden',
          message: `Insufficient permissions. Required: ${requiredPermission}`,
          user_permissions: req.user.permissions
        });
        return;
      }
      
      next();
    };
  }
}
```

### 3. Rate Limiting

Implement rate limiting to prevent abuse:

```typescript
import rateLimit from 'express-rate-limit';

export const createRateLimiters = () => {
  // General API rate limiting
  const generalLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
    message: {
      error: 'Too many requests',
      message: 'Please try again later'
    },
    standardHeaders: true,
    legacyHeaders: false,
  });
  
  // Token validation specific limiting
  const tokenValidationLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 50, // limit each IP to 50 token validations per windowMs
    message: {
      error: 'Too many token validation requests',
      message: 'Please try again later'
    },
    keyGenerator: (req) => {
      // Use user ID if available, otherwise IP
      return req.headers['x-user-id'] || req.ip;
    }
  });
  
  return { generalLimiter, tokenValidationLimiter };
};
```

### 4. Logging and Monitoring

Implement comprehensive logging for security auditing:

```typescript
import winston from 'winston';

interface SecurityLogData {
  event: string;
  user_id?: number;
  ip_address: string;
  user_agent: string;
  timestamp: Date;
  details: any;
}

class SecurityLogger {
  private logger: winston.Logger;
  
  constructor() {
    this.logger = winston.createLogger({
      level: 'info',
      format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.json()
      ),
      transports: [
        new winston.transports.File({ filename: 'logs/security.log' }),
        new winston.transports.Console({
          format: winston.format.simple()
        })
      ]
    });
  }
  
  logTokenValidation(data: SecurityLogData): void {
    this.logger.info('Token validation attempt', data);
  }
  
  logTokenValidationSuccess(data: SecurityLogData): void {
    this.logger.info('Token validation successful', data);
  }
  
  logTokenValidationFailure(data: SecurityLogData): void {
    this.logger.warn('Token validation failed', data);
  }
  
  logPermissionCheck(data: SecurityLogData): void {
    this.logger.info('Permission check', data);
  }
  
  logSecurityViolation(data: SecurityLogData): void {
    this.logger.error('Security violation detected', data);
  }
}
```

## API Security Headers

Implement security headers in your API responses:

```typescript
import helmet from 'helmet';

export const securityMiddleware = [
  helmet(),
  helmet.hidePoweredBy(),
  helmet.noCache(),
  helmet.referrerPolicy({ policy: 'strict-origin-when-cross-origin' }),
  helmet.contentSecurityPolicy({
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      scriptSrc: ["'self'"],
      imgSrc: ["'self'", "data:", "https:"],
    },
  }),
];
```

## Environment Configuration

### Environment Variables

```typescript
interface SecurityConfig {
  jwtSecret: string;
  tokenExpirationHours: number;
  maxLoginAttempts: number;
  lockoutDurationMinutes: number;
  requireHttps: boolean;
  corsOrigins: string[];
}

export const securityConfig: SecurityConfig = {
  jwtSecret: process.env.JWT_SECRET || 'your-secret-key',
  tokenExpirationHours: parseInt(process.env.TOKEN_EXPIRATION_HOURS || '24'),
  maxLoginAttempts: parseInt(process.env.MAX_LOGIN_ATTEMPTS || '5'),
  lockoutDurationMinutes: parseInt(process.env.LOCKOUT_DURATION_MINUTES || '15'),
  requireHttps: process.env.REQUIRE_HTTPS === 'true',
  corsOrigins: process.env.CORS_ORIGINS?.split(',') || ['http://localhost:3000']
};
```

## Testing Security

### Security Test Cases

```typescript
import request from 'supertest';
import { app } from '../app';

describe('Security Tests', () => {
  describe('Token Validation', () => {
    it('should reject requests without authorization header', async () => {
      const response = await request(app)
        .post('/api/validate-token')
        .send({ token: 'test', user_id: 1 });
      
      expect(response.status).toBe(400);
      expect(response.body.error).toBe('Missing required headers');
    });
    
    it('should reject requests with invalid token format', async () => {
      const response = await request(app)
        .post('/api/validate-token')
        .set('Authorization', 'InvalidFormat token123')
        .set('X-User-ID', '1')
        .send({ token: 'test', user_id: 1 });
      
      expect(response.status).toBe(401);
      expect(response.body.error).toBe('Invalid authorization format');
    });
    
    it('should reject expired tokens', async () => {
      // Test with expired token
      const response = await request(app)
        .post('/api/validate-token')
        .set('Authorization', 'Bearer expired_token')
        .set('X-User-ID', '1')
        .send({ token: 'expired_token', user_id: 1 });
      
      expect(response.status).toBe(401);
      expect(response.body.error).toBe('Token expired');
    });
  });
  
  describe('Rate Limiting', () => {
    it('should limit requests per IP', async () => {
      const requests = Array(101).fill(null).map(() =>
        request(app)
          .post('/api/validate-token')
          .send({ token: 'test', user_id: 1 })
      );
      
      const responses = await Promise.all(requests);
      const tooManyRequests = responses.filter(r => r.status === 429);
      
      expect(tooManyRequests.length).toBeGreaterThan(0);
    });
  });
});
```

## Deployment Security Checklist

### Pre-deployment

- [ ] All environment variables are set
- [ ] HTTPS is enabled
- [ ] Database connections use SSL
- [ ] Logging is configured
- [ ] Rate limiting is enabled
- [ ] Security headers are configured

### Post-deployment

- [ ] Security tests pass
- [ ] Logs are being generated
- [ ] Monitoring is active
- [ ] Backup procedures are tested
- [ ] Incident response plan is ready

## Incident Response

### Security Breach Response

1. **Immediate Actions**
   - Revoke all affected tokens
   - Block suspicious IP addresses
   - Enable enhanced logging
   - Notify security team

2. **Investigation**
   - Review logs for suspicious activity
   - Identify affected users
   - Determine breach scope
   - Document findings

3. **Recovery**
   - Generate new tokens for affected users
   - Update security measures
   - Review and update procedures
   - Communicate with users

## Support and Contact

For security-related issues or questions about this implementation guide, please contact the security team immediately.

---

**Version**: 1.0  
**Last Updated**: 2025-01-20  
**Author**: Miku Security Team  
**Classification**: Internal Use Only
