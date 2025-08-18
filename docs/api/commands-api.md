# Commands API ドキュメント

Discordボットのコマンド情報を取得するためのAPIエンドポイントです。フォルダ構造からカテゴリーを自動判定し、サブコマンドの入れ子構造も含む詳細なコマンド情報を提供します。

## 認証

すべてのAPIエンドポイントで認証が必要です。`Authorization`ヘッダーにAPIトークンを設定してください。

```http
Authorization: your-api-token
```

## エンドポイント

### コマンド一覧の取得

ボットの全コマンド情報を取得します。

**リクエスト**

```http
GET /commands
```

**レスポンス**

```json
{
    "success": true,
    "count": 25,
    "categories": [
        {
            "key": "info",
            "displayName": "情報系コマンド"
        },
        {
            "key": "moderation",
            "displayName": "管理系コマンド"
        },
        {
            "key": "music",
            "displayName": "音楽コマンド"
        }
    ],
    "commands": [
        {
            "id": "1234567890123456789",
            "name": "help",
            "description": "コマンドの一覧を表示します",
            "type": 1,
            "category": "info",
            "categoryDisplayName": "情報系コマンド",
            "defaultMemberPermissions": [],
            "dmPermission": true,
            "aliases": ["h", "commands"],
            "usage": "/help",
            "isOwnerCommand": false,
            "options": [],
            "createdAt": "2024-01-01T00:00:00.000Z"
        }
    ]
}
```

**エラーレスポンス**

-   `401 Unauthorized`: 認証トークンが無効
-   `503 Service Unavailable`: Botの準備が完了していない
-   `500 Internal Server Error`: サーバーエラー

---

## レスポンス詳細

### コマンドオブジェクト

| フィールド                 | 型              | 説明                                                |
| -------------------------- | --------------- | --------------------------------------------------- |
| `id`                       | string \| null  | DiscordコマンドのID                                 |
| `name`                     | string          | コマンド名                                          |
| `description`              | string          | コマンドの説明                                      |
| `type`                     | number          | コマンドタイプ（1: ChatInput, 2: User, 3: Message） |
| `category`                 | string          | フォルダ名ベースのカテゴリー                        |
| `categoryDisplayName`      | string          | カテゴリーの日本語表示名                            |
| `defaultMemberPermissions` | string[]        | 必要な権限のリスト                                  |
| `dmPermission`             | boolean         | DMでの使用可否                                      |
| `aliases`                  | string[]        | コマンドのエイリアス                                |
| `usage`                    | string          | 使用方法                                            |
| `isOwnerCommand`           | boolean         | オーナー専用コマンドかどうか                        |
| `options`                  | CommandOption[] | コマンドオプションの配列                            |
| `createdAt`                | string \| null  | コマンド作成日時                                    |

### コマンドオプション

| フィールド     | 型                | 説明                                     |
| -------------- | ----------------- | ---------------------------------------- |
| `name`         | string            | オプション名                             |
| `description`  | string            | オプションの説明                         |
| `type`         | number            | オプションタイプ                         |
| `typeName`     | string            | オプションタイプの文字列表現             |
| `required`     | boolean           | 必須かどうか                             |
| `choices`      | Choice[]          | 選択肢の配列                             |
| `minValue`     | number            | 最小値（数値型の場合）                   |
| `maxValue`     | number            | 最大値（数値型の場合）                   |
| `minLength`    | number            | 最小文字数（文字列型の場合）             |
| `maxLength`    | number            | 最大文字数（文字列型の場合）             |
| `autocomplete` | boolean           | オートコンプリート有効かどうか           |
| `channelTypes` | number[]          | チャンネルタイプ（チャンネル型の場合）   |
| `options`      | CommandOption[]   | サブオプション（入れ子構造）             |
| `arguments`    | CommandArgument[] | サブコマンドの引数（サブコマンドの場合） |

### コマンド引数

| フィールド     | 型       | 説明                                   |
| -------------- | -------- | -------------------------------------- |
| `name`         | string   | 引数名                                 |
| `description`  | string   | 引数の説明                             |
| `type`         | number   | 引数の型                               |
| `typeName`     | string   | 引数の型名                             |
| `required`     | boolean  | 必須かどうか                           |
| `choices`      | Choice[] | 選択肢の配列                           |
| `minValue`     | number   | 最小値（数値型の場合）                 |
| `maxValue`     | number   | 最大値（数値型の場合）                 |
| `minLength`    | number   | 最小文字数（文字列型の場合）           |
| `maxLength`    | number   | 最大文字数（文字列型の場合）           |
| `autocomplete` | boolean  | オートコンプリート有効かどうか         |
| `channelTypes` | number[] | チャンネルタイプ（チャンネル型の場合） |

### オプションタイプ

| 数値 | 名前              | 説明                         |
| ---- | ----------------- | ---------------------------- |
| 1    | SUB_COMMAND       | サブコマンド                 |
| 2    | SUB_COMMAND_GROUP | サブコマンドグループ         |
| 3    | STRING            | 文字列                       |
| 4    | INTEGER           | 整数                         |
| 5    | BOOLEAN           | 真偽値                       |
| 6    | USER              | ユーザー                     |
| 7    | CHANNEL           | チャンネル                   |
| 8    | ROLE              | ロール                       |
| 9    | MENTIONABLE       | メンション可能なオブジェクト |
| 10   | NUMBER            | 数値（小数対応）             |
| 11   | ATTACHMENT        | ファイル添付                 |

---

## サブコマンドの入れ子構造

### サブコマンドグループ

サブコマンドグループは複数のサブコマンドをグループ化します。

```json
{
    "name": "config",
    "description": "設定を管理します",
    "type": 2,
    "typeName": "SUB_COMMAND_GROUP",
    "required": false,
    "arguments": [
        {
            "name": "set",
            "description": "設定を変更します",
            "type": 1,
            "typeName": "SUB_COMMAND",
            "required": false,
            "arguments": [
                {
                    "name": "key",
                    "description": "設定キー",
                    "type": 3,
                    "typeName": "STRING",
                    "required": true
                },
                {
                    "name": "value",
                    "description": "設定値",
                    "type": 3,
                    "typeName": "STRING",
                    "required": true
                }
            ],
            "options": [
                {
                    "name": "key",
                    "description": "設定キー",
                    "type": 3,
                    "typeName": "STRING",
                    "required": true
                },
                {
                    "name": "value",
                    "description": "設定値",
                    "type": 3,
                    "typeName": "STRING",
                    "required": true
                }
            ]
        }
    ]
}
```

### サブコマンド

サブコマンドは独立したコマンドとして機能します。

```json
{
    "name": "play",
    "description": "音楽を再生します",
    "type": 1,
    "typeName": "SUB_COMMAND",
    "required": false,
    "arguments": [
        {
            "name": "query",
            "description": "検索クエリまたはURL",
            "type": 3,
            "typeName": "STRING",
            "required": true,
            "autocomplete": true
        },
        {
            "name": "volume",
            "description": "音量（0-100）",
            "type": 4,
            "typeName": "INTEGER",
            "required": false,
            "minValue": 0,
            "maxValue": 100
        }
    ],
    "options": [
        {
            "name": "query",
            "description": "検索クエリまたはURL",
            "type": 3,
            "typeName": "STRING",
            "required": true,
            "autocomplete": true
        },
        {
            "name": "volume",
            "description": "音量（0-100）",
            "type": 4,
            "typeName": "INTEGER",
            "required": false,
            "minValue": 0,
            "maxValue": 100
        }
    ]
}
```

---

## カテゴリー一覧

| カテゴリーキー | 日本語表示名         | 説明                         |
| -------------- | -------------------- | ---------------------------- |
| `auth`         | 認証系コマンド       | 認証・ログイン関連           |
| `context`      | コンテキストコマンド | 右クリックメニューコマンド   |
| `custom_role`  | カスタムロール       | カスタムロール管理           |
| `dev`          | 開発者向けコマンド   | 開発・デバッグ用             |
| `developer`    | 開発者向けコマンド   | 開発・デバッグ用             |
| `info`         | 情報系コマンド       | 情報表示・ヘルプ             |
| `moderation`   | 管理系コマンド       | サーバー管理・モデレーション |
| `music`        | 音楽コマンド         | 音楽再生・管理               |
| `premium`      | プレミアムコマンド   | プレミアム機能               |
| `reader`       | 読み上げコマンド     | テキスト読み上げ             |
| `util`         | 便利系コマンド       | ユーティリティ・ツール       |

---

## 使用例

### cURLでの使用例

```bash
curl -X GET "http://localhost:3000/commands" \
  -H "Authorization: your-api-token"
```

### JavaScriptでの使用例

```javascript
const API_BASE = 'http://localhost:3000';
const API_TOKEN = 'your-api-token';

async function getCommands() {
    const response = await fetch(`${API_BASE}/commands`, {
        headers: {
            Authorization: API_TOKEN,
        },
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
}

// コマンド一覧を取得
getCommands()
    .then((data) => {
        console.log(`コマンド数: ${data.count}`);
        console.log('カテゴリー:', data.categories);

        // 特定のカテゴリーのコマンドを表示
        const infoCommands = data.commands.filter(
            (cmd) => cmd.category === 'info',
        );
        console.log('情報系コマンド:', infoCommands);
    })
    .catch((error) => {
        console.error('エラー:', error);
    });
```

### カテゴリー別コマンドの取得

```javascript
function getCommandsByCategory(commands, category) {
    return commands.filter((cmd) => cmd.category === category);
}

function getCommandsWithSubcommands(commands) {
    return commands.filter((cmd) =>
        cmd.options.some((opt) => opt.type === 1 || opt.type === 2),
    );
}

// サブコマンドを持つコマンドを取得
const subcommandCommands = getCommandsWithSubcommands(data.commands);
console.log('サブコマンドを持つコマンド:', subcommandCommands);

// サブコマンドの引数を取得
function getSubcommandArguments(command) {
    const subcommands = command.options.filter(
        (opt) => opt.type === 1 || opt.type === 2,
    );

    return subcommands.map((subcmd) => ({
        name: subcmd.name,
        description: subcmd.description,
        arguments: subcmd.arguments || [],
    }));
}

// 特定のコマンドのサブコマンド引数を表示
const musicCommands = getCommandsByCategory(data.commands, 'music');
musicCommands.forEach((cmd) => {
    console.log(`コマンド: ${cmd.name}`);
    const subcmdArgs = getSubcommandArguments(cmd);
    subcmdArgs.forEach((subcmd) => {
        console.log(`  サブコマンド: ${subcmd.name}`);
        subcmd.arguments.forEach((arg) => {
            console.log(
                `    引数: ${arg.name} (${arg.typeName}) - ${arg.required ? '必須' : '任意'}`,
            );
        });
    });
});
```

---

## データソース

このAPIは以下の2つのデータソースを統合してコマンド情報を提供します：

1. **フォルダ構造**（`@/utils/getCommands`）

    - カテゴリー（フォルダ名）
    - エイリアス
    - 使用方法
    - オーナーコマンド判定

2. **Discord.jsアプリケーションコマンド**
    - コマンドID
    - 説明
    - 権限設定
    - オプション詳細
    - 作成日時

---

## 注意事項

1. **認証**: すべてのエンドポイントで有効なAPIトークンが必要です
2. **Bot状態**: Botが準備完了状態でない場合は503エラーが返されます
3. **カテゴリー**: カテゴリーは実際のフォルダ構造に基づいて自動判定されます
4. **サブコマンド**: 複雑な入れ子構造も正確に表現されます
5. **権限**: コマンドの権限情報はDiscord.jsから取得されます

---

## ステータスコード

| コード | 説明                          |
| ------ | ----------------------------- |
| 200    | 成功                          |
| 401    | 認証が必要                    |
| 503    | サービス利用不可（Bot準備中） |
| 500    | サーバーエラー                |
