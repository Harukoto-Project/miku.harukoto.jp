# Welcomer API ドキュメント

Welcomerの設定を管理するためのAPIエンドポイントです。Discordサーバー（ギルド）の参加時・退出時のメッセージ設定を取得・更新できます。

## 認証

すべてのAPIエンドポイントで認証が必要です。`Authorization`ヘッダーにAPIトークンを設定してください。

```http
Authorization: your-api-token
```

## エンドポイント

### 1. Welcomer設定の取得

指定されたギルドのWelcomer設定を取得します。

**リクエスト**

```http
GET /guild/{guildId}/settings/welcomer
```

**パラメータ**

-   `guildId` (string, required): DiscordギルドのID

**レスポンス**

```json
{
    "_id": "mongodb-object-id",
    "guildId": "123456789012345678",
    "onJoin": {
        "channelId": "987654321098765432",
        "message": "ようこそ！",
        "isEnabled": true
    },
    "onLeave": {
        "channelId": "987654321098765432",
        "message": "また来てね！",
        "isEnabled": true
    }
}
```

**エラーレスポンス**

-   `401 Unauthorized`: 認証トークンが無効
-   `404 Not Found`: Welcomer設定が見つからない
-   `500 Internal Server Error`: サーバーエラー

---

### 2. 参加時のWelcomer設定を更新

参加時のメッセージ設定を更新します。

**リクエスト**

```http
PUT /guild/{guildId}/settings/welcomer/join
```

**パラメータ**

-   `guildId` (string, required): DiscordギルドのID

**リクエストボディ**

```json
{
    "channelId": "string",
    "message": "string",
    "isEnabled": "boolean"
}
```

**フィールド詳細**

-   `channelId` (required): メッセージを送信するチャンネルのID
-   `message` (required): 送信するメッセージ内容
-   `isEnabled` (required): 機能の有効/無効状態

**レスポンス**

```json
{
    "message": "参加時のWelcomer設定を更新しました",
    "data": {
        "_id": "mongodb-object-id",
        "guildId": "123456789012345678",
        "onJoin": {
            "channelId": "987654321098765432",
            "message": "ようこそ！",
            "isEnabled": true
        },
        "onLeave": {
            "channelId": "987654321098765432",
            "message": "また来てね！",
            "isEnabled": true
        }
    }
}
```

**エラーレスポンス**

-   `400 Bad Request`: リクエストボディが不正
-   `401 Unauthorized`: 認証トークンが無効
-   `500 Internal Server Error`: サーバーエラー

---

### 3. 退出時のWelcomer設定を更新

退出時のメッセージ設定を更新します。

**リクエスト**

```http
PUT /guild/{guildId}/settings/welcomer/leave
```

**パラメータ**

-   `guildId` (string, required): DiscordギルドのID

**リクエストボディ**

```json
{
    "channelId": "string",
    "message": "string",
    "isEnabled": "boolean"
}
```

**フィールド詳細**

-   `channelId` (required): メッセージを送信するチャンネルのID
-   `message` (required): 送信するメッセージ内容
-   `isEnabled` (required): 機能の有効/無効状態

**レスポンス**

```json
{
    "message": "退出時のWelcomer設定を更新しました",
    "data": {
        "_id": "mongodb-object-id",
        "guildId": "123456789012345678",
        "onJoin": {
            "channelId": "987654321098765432",
            "message": "ようこそ！",
            "isEnabled": true
        },
        "onLeave": {
            "channelId": "987654321098765432",
            "message": "また来てね！",
            "isEnabled": true
        }
    }
}
```

**エラーレスポンス**

-   `400 Bad Request`: リクエストボディが不正
-   `401 Unauthorized`: 認証トークンが無効
-   `500 Internal Server Error`: サーバーエラー

---

## 使用例

### cURLでの使用例

#### 設定の取得

```bash
curl -X GET "http://localhost:3000/guild/123456789012345678/settings/welcomer" \
  -H "Authorization: your-api-token"
```

#### 参加時の設定を更新

```bash
curl -X PUT "http://localhost:3000/guild/123456789012345678/settings/welcomer/join" \
  -H "Authorization: your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "channelId": "987654321098765432",
    "message": "ようこそ！",
    "isEnabled": true
  }'
```

#### 退出時の設定を更新

```bash
curl -X PUT "http://localhost:3000/guild/123456789012345678/settings/welcomer/leave" \
  -H "Authorization: your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "channelId": "987654321098765432",
    "message": "また来てね！",
    "isEnabled": true
  }'
```

### JavaScriptでの使用例

```javascript
const API_BASE = 'http://localhost:3000';
const API_TOKEN = 'your-api-token';
const GUILD_ID = '123456789012345678';

// 設定の取得
async function getWelcomerSettings() {
    const response = await fetch(
        `${API_BASE}/guild/${GUILD_ID}/settings/welcomer`,
        {
            headers: {
                Authorization: API_TOKEN,
            },
        },
    );
    return await response.json();
}

// 参加時の設定を更新
async function updateJoinWelcomer(channelId, message, isEnabled) {
    const response = await fetch(
        `${API_BASE}/guild/${GUILD_ID}/settings/welcomer/join`,
        {
            method: 'PUT',
            headers: {
                Authorization: API_TOKEN,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                channelId,
                message,
                isEnabled,
            }),
        },
    );
    return await response.json();
}

// 退出時の設定を更新
async function updateLeaveWelcomer(channelId, message, isEnabled) {
    const response = await fetch(
        `${API_BASE}/guild/${GUILD_ID}/settings/welcomer/leave`,
        {
            method: 'PUT',
            headers: {
                Authorization: API_TOKEN,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                channelId,
                message,
                isEnabled,
            }),
        },
    );
    return await response.json();
}
```

---

## データベーススキーマ

Welcomer設定は以下のMongoDBスキーマで保存されます：

```typescript
interface WelcomerDocument {
    guildId: string; // ギルドID（一意）
    onJoin?: {
        // 参加時の設定
        channelId: string; // チャンネルID
        message: string; // メッセージ内容
        isEnabled: boolean; // 有効/無効
    } | null;
    onLeave?: {
        // 退出時の設定
        channelId: string; // チャンネルID
        message: string; // メッセージ内容
        isEnabled: boolean; // 有効/無効
    };
}
```

---

## 注意事項

1. **認証**: すべてのエンドポイントで有効なAPIトークンが必要です
2. **権限**: ギルドの管理者権限を持つユーザーのみが設定を変更できます
3. **チャンネルID**: 有効なDiscordチャンネルIDを指定してください
4. **メッセージ**: メッセージは空文字列にすることはできません
5. **Upsert**: 設定が存在しない場合は新規作成されます

---

## ステータスコード

| コード | 説明                   |
| ------ | ---------------------- |
| 200    | 成功                   |
| 400    | リクエストが不正       |
| 401    | 認証が必要             |
| 404    | リソースが見つからない |
| 500    | サーバーエラー         |
