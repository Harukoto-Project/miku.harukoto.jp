# Achievements API Documentation

このドキュメントでは、Miku Botのアチーブメントシステムに関するAPIエンドポイントについて説明します。

## 概要

アチーブメントAPIは、ユーザーのアチーブメント設定の管理と、アチーブメントの獲得状況の取得を提供します。すべてのエンドポイントは認証が必要です。

## ベースURL

```
http://localhost:3000
```

## 認証

すべてのエンドポイントでAPIキーによる認証が必要です。リクエストヘッダーに`Authorization`を設定してください：

```
Authorization: API TOKEN
```

## エンドポイント

### 1. ユーザーのアチーブメント設定を更新

**エンドポイント:** `PUT /user/:userId/settings/achievements`

**説明:** 指定されたユーザーのアチーブメント設定を更新します。

**パラメータ:**

-   `userId` (path): ユーザーID

**リクエストボディ:**

```json
{
  "achievementsEnabled": boolean,
  "notificationsEnabled": boolean
}
```

**レスポンス:**

-   **成功時 (200):**

```json
{
    "userId": "string",
    "achievementsEnabled": true,
    "notificationsEnabled": true
}
```

-   **エラー時 (400):**

```json
{
    "error": "Invalid parameters"
}
```

-   **エラー時 (500):**

```json
{
    "error": "Internal server error"
}
```

**使用例:**

```bash
curl -X PUT http://localhost:3000/user/123456789/settings/achievements \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"achievementsEnabled": true, "notificationsEnabled": false}'
```

### 2. ユーザーのアチーブメント設定を取得

**エンドポイント:** `GET /user/:userId/settings/achievements`

**説明:** 指定されたユーザーの現在のアチーブメント設定を取得します。設定が存在しない場合は、デフォルト設定を作成して返します。

**パラメータ:**

-   `userId` (path): ユーザーID

**レスポンス:**

-   **成功時 (200):**

```json
{
    "userId": "string",
    "achievementsEnabled": true,
    "notificationsEnabled": true
}
```

-   **エラー時 (500):**

```json
{
    "error": "Internal server error"
}
```

**使用例:**

```bash
curl -X GET http://localhost:3000/user/123456789/settings/achievements \
  -H "Authorization: Bearer YOUR_API_KEY"
```

### 3. ユーザーの獲得済みアチーブメント一覧を取得

**エンドポイント:** `GET /user/:userId/achievements/unlocked`

**説明:** 指定されたユーザーが獲得済みのアチーブメント一覧を取得します。

**パラメータ:**

-   `userId` (path): ユーザーID

**レスポンス:**

-   **成功時 (200):**

```json
[
    {
        "achievement": {
            "id": "string",
            "category": "string",
            "name": "string",
            "description": "string",
            "isHidden": false
        },
        "tier": {
            "tier": 1,
            "name": "string",
            "description": "string",
            "requirement": "string",
            "reward": "string"
        },
        "unlockedAt": "2024-01-01T00:00:00.000Z",
        "currentProgress": 100
    }
]
```

-   **エラー時 (500):**

```json
{
    "error": "Internal server error"
}
```

**使用例:**

```bash
curl -X GET http://localhost:3000/user/123456789/achievements/unlocked \
  -H "Authorization: Bearer YOUR_API_KEY"
```

### 4. ユーザーの未獲得アチーブメント一覧を取得

**エンドポイント:** `GET /user/:userId/achievements/locked`

**説明:** 指定されたユーザーがまだ獲得していないアチーブメント一覧を取得します。

**パラメータ:**

-   `userId` (path): ユーザーID

**レスポンス:**

-   **成功時 (200):**

```json
[
    {
        "achievement": {
            "id": "string",
            "category": "string",
            "name": "string",
            "description": "string",
            "isHidden": false
        },
        "nextTier": {
            "tier": 1,
            "name": "string",
            "description": "string",
            "requirement": "string",
            "reward": "string"
        },
        "progress": 0
    }
]
```

-   **エラー時 (500):**

```json
{
    "error": "Internal server error"
}
```

**使用例:**

```bash
curl -X GET http://localhost:3000/user/123456789/achievements/locked \
  -H "Authorization: Bearer YOUR_API_KEY"
```

### 5. ユーザーの全アチーブメント一覧を取得

**エンドポイント:** `GET /user/:userId/achievements`

**説明:** 指定されたユーザーの全アチーブメント一覧を取得します。獲得済みと未獲得の両方の情報を含みます。

**パラメータ:**

-   `userId` (path): ユーザーID

**レスポンス:**

-   **成功時 (200):**

```json
[
    {
        "achievement": {
            "id": "string",
            "category": "string",
            "name": "string",
            "description": "string",
            "isHidden": false
        },
        "status": "unlocked",
        "currentTier": 1,
        "tier": {
            "tier": 1,
            "name": "string",
            "description": "string",
            "requirement": "string",
            "reward": "string"
        },
        "unlockedAt": "2024-01-01T00:00:00.000Z",
        "currentProgress": 100
    },
    {
        "achievement": {
            "id": "string",
            "category": "string",
            "name": "string",
            "description": "string",
            "isHidden": false
        },
        "status": "locked",
        "nextTier": {
            "tier": 1,
            "name": "string",
            "description": "string",
            "requirement": "string",
            "reward": "string"
        },
        "progress": 0
    }
]
```

-   **エラー時 (500):**

```json
{
    "error": "Internal server error"
}
```

**使用例:**

```bash
curl -X GET http://localhost:3000/user/123456789/achievements \
  -H "Authorization: Bearer YOUR_API_KEY"
```

## データモデル

### Achievement

```typescript
interface Achievement {
    id: string;
    category: string;
    name: string;
    description: string;
    isHidden: boolean;
    tiers: AchievementTier[];
}
```

### AchievementTier

```typescript
interface AchievementTier {
    tier: number;
    name: string;
    description: string;
    requirement: string;
    reward: string;
}
```

### UserAchievement

```typescript
interface UserAchievement {
    userId: string;
    achievementId: string;
    currentTier: number;
    currentProgress: number;
    unlockedAt: Date[];
}
```

## エラーハンドリング

### HTTPステータスコード

-   **200**: 成功
-   **400**: 不正なリクエスト（パラメータエラー）
-   **401**: 認証エラー
-   **500**: サーバー内部エラー

### エラーレスポンス形式

```json
{
    "error": "エラーメッセージ"
}
```

## レート制限

アチーブメント関連のAPIには適切なレート制限が適用されています。過度なリクエストは制限される場合があります。

## 注意事項

1. すべてのエンドポイントで認証が必要です
2. `userId`は有効なDiscordユーザーIDである必要があります
3. アチーブメント設定の更新は即座に反映されます
4. 隠しアチーブメント（`isHidden: true`）は、獲得済みの場合のみ表示されます
5. アチーブメントの進行状況は、現在のティアに基づいて計算されます

## サポート

APIに関する質問や問題がございましたら、開発チームまでお問い合わせください。
