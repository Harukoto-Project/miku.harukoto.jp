# ポートフォリオシステム 包括的詳細仕様書

## 目次

1. [システム概要](#1-システム概要)
2. [データベース設計](#2-データベース設計)
3. [API仕様](#3-api仕様)
4. [フロントエンド機能](#4-フロントエンド機能)
5. [ファイル管理システム](#5-ファイル管理システム)
6. [認証・セキュリティ](#6-認証セキュリティ)
7. [SEO・OGP対応](#7-seoogp対応)
8. [Laravel移行ガイド](#8-laravel移行ガイド)

---

## 1. システム概要

### 1.1 システムの目的

ユーザーが自身のプロフィール情報を管理し、カスタマイズ可能なポートフォリオページを公開できるWebアプリケーション。

### 1.2 主要機能

-   **プロフィール管理**: 基本情報、自己紹介、アバター画像
-   **カスタムテーマ**: 色設定、グラデーション対応
-   **リンク管理**: SNS・サービスへのリンクボタン
-   **カスタムURL**: 短縮URLによるプロフィール共有
-   **アバター管理**: 画像アップロード、ハッシュ管理
-   **SEO対応**: OGP画像生成、メタデータ最適化

### 1.3 技術スタック

-   **フロントエンド**: Next.js 14, React, TypeScript, Tailwind CSS
-   **バックエンド**: Next.js API Routes
-   **データベース**: MongoDB → MySQL（移行予定）
-   **認証**: NextAuth.js (Discord OAuth2)
-   **ストレージ**: Cloudflare R2 (S3互換)
-   **画像処理**: Next.js Image Optimization

---

## 2. データベース設計

### 2.1 テーブル構造（MySQL移行版）

#### 2.1.1 profiles テーブル

```sql
CREATE TABLE profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL UNIQUE COMMENT 'DiscordユーザーID',
    display_name VARCHAR(50) NULL COMMENT '表示名',
    bio TEXT NULL COMMENT '自己紹介（最大500文字）',
    pronouns VARCHAR(32) NULL COMMENT '代名詞',
    location VARCHAR(32) NULL COMMENT '所在地',
    avatar_url TEXT NULL COMMENT 'アバター画像URL',
    avatar_hash VARCHAR(255) NULL UNIQUE COMMENT 'アバターハッシュ',
    custom_url VARCHAR(30) NULL UNIQUE COMMENT 'カスタムURL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_avatar_hash (avatar_hash),
    INDEX idx_custom_url (custom_url),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.1.2 profile_links テーブル

```sql
CREATE TABLE profile_links (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL COMMENT 'プロフィールID',
    button_id VARCHAR(50) NOT NULL COMMENT 'ボタンタイプID',
    url TEXT NOT NULL COMMENT 'リンク先URL',
    display_name VARCHAR(50) NOT NULL COMMENT '表示名',
    custom_color VARCHAR(7) NULL COMMENT 'カスタム色（HEX）',
    sort_order INT DEFAULT 0 COMMENT '表示順序',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_profile_id (profile_id),
    INDEX idx_button_id (button_id),
    INDEX idx_sort_order (sort_order),
    UNIQUE KEY unique_profile_button (profile_id, button_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.1.3 profile_themes テーブル

```sql
CREATE TABLE profile_themes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL COMMENT 'プロフィールID',

    -- 背景色設定
    background_color_type ENUM('solid', 'gradient') DEFAULT 'solid',
    background_color_solid VARCHAR(7) NULL COMMENT '単色（HEX）',
    background_color_gradient_stops JSON NULL COMMENT 'グラデーション停止点',
    background_color_gradient_direction INT DEFAULT 135 COMMENT 'グラデーション角度',

    -- 表示名色設定
    display_name_color_type ENUM('solid', 'gradient') DEFAULT 'solid',
    display_name_color_solid VARCHAR(7) NULL,
    display_name_color_gradient_stops JSON NULL,
    display_name_color_gradient_direction INT DEFAULT 0,

    -- 代名詞色設定
    pronouns_color_type ENUM('solid', 'gradient') DEFAULT 'solid',
    pronouns_color_solid VARCHAR(7) NULL,
    pronouns_color_gradient_stops JSON NULL,
    pronouns_color_gradient_direction INT DEFAULT 0,

    -- 場所色設定
    location_color_type ENUM('solid', 'gradient') DEFAULT 'solid',
    location_color_solid VARCHAR(7) NULL,
    location_color_gradient_stops JSON NULL,
    location_color_gradient_direction INT DEFAULT 0,

    -- 自己紹介テキスト色設定
    bio_text_color_type ENUM('solid', 'gradient') DEFAULT 'solid',
    bio_text_color_solid VARCHAR(7) NULL,
    bio_text_color_gradient_stops JSON NULL,
    bio_text_color_gradient_direction INT DEFAULT 0,

    -- 自己紹介背景色設定
    bio_background_color_type ENUM('solid', 'gradient') DEFAULT 'solid',
    bio_background_color_solid VARCHAR(7) NULL,
    bio_background_color_gradient_stops JSON NULL,
    bio_background_color_gradient_direction INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_profile_theme (profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 データ制約・バリデーション

#### 2.2.1 文字数制限

-   **display_name**: 最大50文字
-   **bio**: 最大500文字
-   **pronouns**: 最大32文字
-   **location**: 最大32文字
-   **custom_url**: 3-30文字（英数字、ピリオド、アンダーバーのみ）

#### 2.2.2 リンク制限

-   **最大リンク数**: 10個
-   **URL形式**: http://またはhttps://で開始
-   **ボタンID重複**: 同一プロフィール内で重複不可

#### 2.2.3 色設定制限

-   **HEX色**: #RRGGBB形式
-   **グラデーション停止点**: 2-5個
-   **グラデーション角度**: 0-360度

---

## 3. API仕様

### 3.1 プロフィール管理API

#### 3.1.1 プロフィール取得

```http
GET /api/profile
Authorization: Bearer {token}

Response:
{
  "userId": "string",
  "avatarHash": "string",
  "avatarUrl": "string",
  "displayName": "string",
  "bio": "string",
  "pronouns": "string",
  "location": "string",
  "links": [...],
  "theme": {...},
  "customUrl": "string",
  "createdAt": "string",
  "updatedAt": "string"
}
```

#### 3.1.2 プロフィール更新

```http
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "displayName": "string",
  "bio": "string",
  "pronouns": "string",
  "location": "string",
  "links": [...],
  "theme": {...}
}

Response:
{
  "userId": "string",
  "avatarHash": "string",
  "avatarUrl": "string",
  "displayName": "string",
  "bio": "string",
  "pronouns": "string",
  "location": "string",
  "links": [...],
  "theme": {...},
  "updatedAt": "string"
}
```

#### 3.1.3 プロフィール作成

```http
POST /api/profile/create
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "displayName": "string",
  "bio": "string"
}

Response:
{
  "userId": "string",
  "avatarHash": "string",
  "avatarUrl": "string",
  "displayName": "string",
  "bio": "string",
  "createdAt": "string",
  "updatedAt": "string"
}
```

### 3.2 アバター管理API

#### 3.2.1 アバターアップロード準備

```http
POST /api/profile/avatar/upload-avatar
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "fileName": "string",
  "fileType": "string",
  "fileSize": "number"
}

Response:
{
  "signedUrl": "string",
  "fileName": "string",
  "publicUrl": "string",
  "avatarHash": "string",
  "oldAvatarUrl": "string"
}
```

#### 3.2.2 アバター自動適用

```http
PUT /api/profile/avatar/upload-avatar
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "avatarUrl": "string",
  "avatarHash": "string",
  "oldAvatarUrl": "string"
}

Response:
{
  "success": "boolean",
  "profile": {...}
}
```

#### 3.2.3 アバター削除

```http
DELETE /api/profile/avatar/remove-avatar
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "oldAvatarUrl": "string"
}

Response:
{
  "success": "boolean",
  "message": "string",
  "profile": {...}
}
```

### 3.3 カスタムURL管理API

#### 3.3.1 カスタムURL更新

```http
PATCH /api/profile/custom-url
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
  "customUrl": "string"
}

Response:
{
  "profile": {...}
}
```

#### 3.3.2 カスタムURL可用性チェック

```http
GET /api/profile/custom-url/check?url={url}

Response:
{
  "available": "boolean"
}
```

### 3.4 公開プロフィールAPI

#### 3.4.1 ユーザーIDによる取得

```http
GET /api/profile/{userId}

Response:
{
  "userId": "string",
  "displayName": "string",
  "bio": "string",
  "pronouns": "string",
  "location": "string",
  "avatarUrl": "string",
  "links": [...],
  "theme": {...},
  "createdAt": "string",
  "updatedAt": "string"
}
```

#### 3.4.2 アバターハッシュによる取得

```http
GET /api/profile/avatar/by-hash/{hash}

Response:
{
  "avatarHash": "string",
  "avatarUrl": "string",
  "displayName": "string"
}
```

### 3.5 OGP画像生成API

#### 3.5.1 プロフィールOGP画像

```http
GET /api/og/profile?displayName={name}&bio={bio}&pronouns={pronouns}&location={location}&avatar={url}

Response:
Image/PNG (1200x630)
```

---

## 4. フロントエンド機能

### 4.1 プロフィール編集コンポーネント

#### 4.1.1 ProfileEditor.tsx

**機能概要**: 自己紹介文の編集・管理

**主要機能**:

-   最大500文字の自己紹介入力
-   リアルタイム文字数表示
-   変更検知と未保存警告
-   リセット機能
-   スケルトンローディング

**状態管理**:

```typescript
interface ProfileEditorState {
    bio: string;
    currentProfile: Profile | null;
    isLoading: boolean;
    isLoadingProfile: boolean;
    hasChanges: boolean;
}
```

**バリデーション**:

-   文字数制限: 500文字以内
-   必須項目チェック
-   データ形式検証

#### 4.1.2 AvatarUploader.tsx

**機能概要**: アバター画像のアップロード・管理

**主要機能**:

-   ドラッグ&ドロップ対応
-   ファイル形式制限（JPEG, PNG, WebP, GIF）
-   ファイルサイズ制限（1MB）
-   プレビュー表示
-   削除機能

**アップロードフロー**:

1. ファイル選択・検証
2. アップロード準備（署名付きURL取得）
3. S3/R2へのファイルアップロード
4. プロフィール自動更新
5. 古いファイル削除

**状態管理**:

```typescript
interface AvatarUploaderState {
    preview: string;
    currentProfile: Profile | null;
    isUploading: boolean;
    isDragOver: boolean;
    isLoadingProfile: boolean;
}
```

#### 4.1.3 CustomUrlEditor.tsx

**機能概要**: カスタムURLの設定・管理

**主要機能**:

-   カスタムURL入力（3-30文字）
-   リアルタイム可用性チェック
-   プレビュー表示
-   バリデーション
-   クリア・リセット機能

**URL形式**:

-   英数字、ピリオド、アンダーバーのみ
-   3文字以上30文字以下
-   重複チェック

**状態管理**:

```typescript
interface CustomUrlEditorState {
    customUrl: string;
    currentProfile: Profile | null;
    isLoading: boolean;
    isLoadingProfile: boolean;
    isChecking: boolean;
    isAvailable: boolean | null;
    checkMessage: string;
    hasChanges: boolean;
}
```

### 4.2 ポートフォリオ表示ページ

#### 4.2.1 プロフィールページ（/profile/[userId]）

**機能概要**: ユーザーの公開プロフィール表示

**表示要素**:

-   アバター画像（128x128px、円形）
-   表示名（カスタマイズ可能な色）
-   代名詞（オプション）
-   場所（アイコン付き）
-   自己紹介（改行対応、背景色カスタマイズ）
-   リンクボタン（16種類のSNS・サービス）

**テーマシステム**:

```typescript
interface Theme {
    backgroundColor?: IColorConfig | string;
    displayNameColor?: IColorConfig | string;
    pronounsColor?: IColorConfig | string;
    locationColor?: IColorConfig | string;
    bioTextColor?: IColorConfig | string;
    bioBackgroundColor?: IColorConfig | string;
}

interface IColorConfig {
    type: 'solid' | 'gradient';
    solid?: string;
    gradient?: {
        stops: IGradientStop[];
        direction: number;
    };
}
```

**リンクボタン**:

-   **対応サービス**: 16種類（Twitter, GitHub, YouTube等）
-   **カスタムカラー**: 個別ボタンの色設定
-   **ホバーエフェクト**: スケール・シャドウアニメーション
-   **外部リンク**: 新規タブでの開封

#### 4.2.2 カスタムURLページ（/p/[url]）

**機能概要**: カスタムURLによるプロフィールリダイレクト

**処理フロー**:

1. カスタムURLの検証
2. プロフィール検索
3. 標準プロフィールページへのリダイレクト
4. メタデータ生成

**メタデータ**:

-   タイトル、説明文
-   OGP画像
-   Twitter Card
-   検索エンジン最適化

### 4.3 ダッシュボード統合

#### 4.3.1 エディターカード

**共通コンポーネント**:

-   EditorCard: 統一されたカードデザイン
-   スケルトンローディング
-   エラーハンドリング
-   レスポンシブ対応

#### 4.3.2 ナビゲーション

**ダッシュボード構造**:

-   サイドバーナビゲーション
-   ギルド別管理
-   ユーザープロフィール管理
-   設定項目の整理

---

## 5. ファイル管理システム

### 5.1 ストレージ構成

#### 5.1.1 Cloudflare R2設定

**環境変数**:

```env
CLOUDFLARE_R2_ENDPOINT=your-endpoint
CLOUDFLARE_R2_ACCESS_KEY_ID=your-access-key
CLOUDFLARE_R2_SECRET_ACCESS_KEY=your-secret-key
CLOUDFLARE_R2_BUCKET_NAME=your-bucket
CLOUDFLARE_R2_PUBLIC_URL=your-public-url
```

**ファイル構造**:

```
avatars/
├── {avatarHash}.jpg
├── {avatarHash}.png
├── {avatarHash}.webp
└── {avatarHash}.gif
```

#### 5.1.2 アップロード処理

**署名付きURL方式**:

1. ファイル情報の事前検証
2. 署名付きURLの生成（1時間有効）
3. クライアントからの直接アップロード
4. プロフィール自動更新
5. 古いファイルの削除

**ファイル制限**:

-   **サイズ**: 最大1MB
-   **形式**: JPEG, PNG, WebP, GIF
-   **命名**: ハッシュベースの一意名

### 5.2 ハッシュ生成システム

#### 5.2.1 ユーザーベースハッシュ

```typescript
function generateUserBasedHash(userId: string): string {
    const timestamp = Date.now().toString();
    const randomBytes = crypto.randomBytes(16).toString('hex');
    const combinedString = `${userId}-${timestamp}-${randomBytes}`;

    return crypto
        .createHash('sha256')
        .update(combinedString)
        .digest('hex')
        .substring(0, 32);
}
```

#### 5.2.2 重複チェック

**処理フロー**:

1. ユーザーベースハッシュ生成
2. データベースでの重複チェック
3. 重複時はランダムハッシュ生成
4. 最大リトライ回数（デフォルト10回）

---

## 6. 認証・セキュリティ

### 6.1 認証システム

#### 6.1.1 NextAuth.js設定

**Discord OAuth2**:

```typescript
DiscordProvider({
    clientId: process.env.DASH_CLIENT_ID,
    clientSecret: process.env.DASH_CLIENT_SECRET,
    authorization: {
        params: {
            scope: 'identify email guilds',
            redirect_uri: `${process.env.APP_URL}/api/auth/callback/discord`,
        },
    },
});
```

**スコープ**:

-   `identify`: ユーザー基本情報
-   `email`: メールアドレス
-   `guilds`: サーバー情報

#### 6.1.2 セッション管理

**JWT設定**:

-   アクセストークンの保持
-   ユーザーIDの管理
-   セッション有効期限

### 6.2 セキュリティ対策

#### 6.2.1 アクセス制御

**認証必須エンドポイント**:

-   `/api/profile` (GET, PUT)
-   `/api/profile/create`
-   `/api/profile/avatar/*`
-   `/api/profile/custom-url`

**公開エンドポイント**:

-   `/api/profile/{userId}`
-   `/api/profile/avatar/by-hash/{hash}`
-   `/api/profile/custom-url/check`
-   `/api/og/profile`

#### 6.2.2 入力値検証

**バリデーション**:

-   文字数制限
-   ファイル形式・サイズ
-   URL形式
-   色コード形式
-   グラデーション設定

**サニタイゼーション**:

-   XSS対策
-   SQLインジェクション対策
-   ファイルパストラバーサル対策

---

## 7. SEO・OGP対応

### 7.1 メタデータ生成

#### 7.1.1 動的メタデータ

**プロフィールページ**:

```typescript
export async function generateMetadata({ params }): Promise<Metadata> {
    const { userId } = await params;
    const profile = await getProfile(userId);

    return {
        title: `${profile.displayName} | Miku`,
        description: profile.bio,
        openGraph: {
            title: `${profile.displayName}のプロフィール`,
            description: profile.bio,
            images: [ogImageUrl],
        },
        twitter: {
            card: 'summary_large_image',
            images: [ogImageUrl],
        },
    };
}
```

#### 7.1.2 カスタムURLページ

**リダイレクト処理**:

1. カスタムURLの検証
2. プロフィール検索
3. 標準プロフィールページへのリダイレクト
4. メタデータの最適化

### 7.2 OGP画像生成

#### 7.2.1 画像生成API

**Edge Runtime**:

-   1200x630pxのOGP画像
-   動的コンテンツ生成
-   アバター画像の埋め込み
-   プロフィール情報の表示

**画像要素**:

-   アバター（円形、160x160px）
-   表示名（56px、青色）
-   代名詞（28px、紫色）
-   場所（24px、アイコン付き）
-   自己紹介（24px、白色）

#### 7.2.2 画像最適化

**Next.js Image**:

-   自動フォーマット変換
-   レスポンシブ画像
-   遅延読み込み
-   WebP対応

---

## 8. Laravel移行ガイド

### 8.1 データベース移行

#### 8.1.1 マイグレーションファイル

```php
// create_profiles_table.php
public function up()
{
    Schema::create('profiles', function (Blueprint $table) {
        $table->id();
        $table->string('user_id')->unique();
        $table->string('display_name', 50)->nullable();
        $table->text('bio')->nullable();
        $table->string('pronouns', 32)->nullable();
        $table->string('location', 32)->nullable();
        $table->text('avatar_url')->nullable();
        $table->string('avatar_hash')->unique()->nullable();
        $table->string('custom_url', 30)->unique()->nullable();
        $table->timestamps();

        $table->index('user_id');
        $table->index('avatar_hash');
        $table->index('custom_url');
    });
}

// create_profile_links_table.php
public function up()
{
    Schema::create('profile_links', function (Blueprint $table) {
        $table->id();
        $table->foreignId('profile_id')->constrained()->onDelete('cascade');
        $table->string('button_id', 50);
        $table->text('url');
        $table->string('display_name', 50);
        $table->string('custom_color', 7)->nullable();
        $table->integer('sort_order')->default(0);
        $table->timestamps();

        $table->index('profile_id');
        $table->unique(['profile_id', 'button_id']);
    });
}

// create_profile_themes_table.php
public function up()
{
    Schema::create('profile_themes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('profile_id')->constrained()->onDelete('cascade');

        // 背景色設定
        $table->enum('background_color_type', ['solid', 'gradient'])->default('solid');
        $table->string('background_color_solid', 7)->nullable();
        $table->json('background_color_gradient_stops')->nullable();
        $table->integer('background_color_gradient_direction')->default(135);

        // その他の色設定...

        $table->timestamps();

        $table->unique('profile_id');
    });
}
```

### 8.2 Eloquentモデル

#### 8.2.1 Profileモデル

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'pronouns',
        'location',
        'avatar_url',
        'avatar_hash',
        'custom_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function links(): HasMany
    {
        return $this->hasMany(ProfileLink::class);
    }

    public function theme(): HasOne
    {
        return $this->hasOne(ProfileTheme::class);
    }

    public function scopeByUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAvatarHash($query, $hash)
    {
        return $query->where('avatar_hash', $hash);
    }

    public function scopeByCustomUrl($query, $url)
    {
        return $query->where('custom_url', $url);
    }
}
```

#### 8.2.2 ProfileLinkモデル

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileLink extends Model
{
    protected $fillable = [
        'profile_id',
        'button_id',
        'url',
        'display_name',
        'custom_color',
        'sort_order',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
```

#### 8.2.3 ProfileThemeモデル

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileTheme extends Model
{
    protected $fillable = [
        'profile_id',
        'background_color_type',
        'background_color_solid',
        'background_color_gradient_stops',
        'background_color_gradient_direction',
        // その他の色設定...
    ];

    protected $casts = [
        'background_color_gradient_stops' => 'array',
        'background_color_gradient_direction' => 'integer',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
```

### 8.3 コントローラー

#### 8.3.1 ProfileController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $this->profileService->getProfile($user->id);

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'display_name' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
            'pronouns' => 'nullable|string|max:32',
            'location' => 'nullable|string|max:32',
            'links' => 'nullable|array|max:10',
            'links.*.button_id' => 'required|string|max:50',
            'links.*.url' => 'required|url|max:500',
            'links.*.display_name' => 'required|string|max:50',
            'links.*.custom_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme' => 'nullable|array',
        ]);

        $user = $request->user();
        $profile = $this->profileService->updateProfile(
            $user->id,
            $request->all(),
        );

        return response()->json($profile);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'display_name' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $profile = $this->profileService->createProfile(
            $user->id,
            $request->all(),
        );

        return response()->json($profile, 201);
    }
}
```

#### 8.3.2 PublicProfileController

```php
<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicProfileController extends Controller
{
    public function show(string $userId): JsonResponse
    {
        $profile = Profile::with(['links', 'theme'])
            ->byUserId($userId)
            ->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    public function showByCustomUrl(string $url): JsonResponse
    {
        $profile = Profile::with(['links', 'theme'])
            ->byCustomUrl($url)
            ->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }
}
```

### 8.4 サービス層

#### 8.4.1 ProfileService

```php
<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\ProfileLink;
use App\Models\ProfileTheme;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function getProfile(string $userId): ?Profile
    {
        return Profile::with(['links', 'theme'])
            ->byUserId($userId)
            ->first();
    }

    public function createProfile(string $userId, array $data): Profile
    {
        $existingProfile = Profile::byUserId($userId)->first();

        if ($existingProfile) {
            throw new \Exception('Profile already exists for this user');
        }

        return DB::transaction(function () use ($userId, $data) {
            $profile = Profile::create([
                'user_id' => $userId,
                'display_name' => $data['display_name'] ?? null,
                'bio' => $data['bio'] ?? null,
            ]);

            if (isset($data['links'])) {
                $this->updateLinks($profile, $data['links']);
            }

            if (isset($data['theme'])) {
                $this->updateTheme($profile, $data['theme']);
            }

            return $profile->fresh(['links', 'theme']);
        });
    }

    public function updateProfile(string $userId, array $data): Profile
    {
        $profile = Profile::byUserId($userId)->first();

        if (!$profile) {
            throw new \Exception('Profile not found');
        }

        return DB::transaction(function () use ($profile, $data) {
            $profile->update([
                'display_name' =>
                    $data['display_name'] ?? $profile->display_name,
                'bio' => $data['bio'] ?? $profile->bio,
                'pronouns' => $data['pronouns'] ?? $profile->pronouns,
                'location' => $data['location'] ?? $profile->location,
            ]);

            if (isset($data['links'])) {
                $this->updateLinks($profile, $data['links']);
            }

            if (isset($data['theme'])) {
                $this->updateTheme($profile, $data['theme']);
            }

            return $profile->fresh(['links', 'theme']);
        });
    }

    private function updateLinks(Profile $profile, array $links): void
    {
        $profile->links()->delete();

        foreach ($links as $linkData) {
            $profile->links()->create([
                'button_id' => $linkData['button_id'],
                'url' => $linkData['url'],
                'display_name' => $linkData['display_name'],
                'custom_color' => $linkData['custom_color'] ?? null,
                'sort_order' => $linkData['sort_order'] ?? 0,
            ]);
        }
    }

    private function updateTheme(Profile $profile, array $themeData): void
    {
        $profile
            ->theme()
            ->updateOrCreate(['profile_id' => $profile->id], $themeData);
    }

    public function generateUniqueAvatarHash(
        string $userId,
        int $maxRetries = 10,
    ): string {
        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $hash = $this->generateUserBasedHash($userId);

            $existingProfile = Profile::byAvatarHash($hash)->first();

            if (!$existingProfile) {
                return $hash;
            }
        }

        $fallbackHash = Str::random(32);
        $existingFallback = Profile::byAvatarHash($fallbackHash)->first();

        if ($existingFallback) {
            throw new \Exception(
                'Failed to generate unique avatar hash after maximum retries',
            );
        }

        return $fallbackHash;
    }

    private function generateUserBasedHash(string $userId): string
    {
        $timestamp = time();
        $randomBytes = Str::random(16);
        $combinedString = "{$userId}-{$timestamp}-{$randomBytes}";

        return substr(hash('sha256', $combinedString), 0, 32);
    }
}
```

### 8.5 ルーティング

#### 8.5.1 Webルート

```php
// routes/web.php

// プロフィール表示
Route::get('/profile/{userId}', [PublicProfileController::class, 'show'])->name(
    'profile.show',
);

// カスタムURL
Route::get('/p/{url}', [
    PublicProfileController::class,
    'showByCustomUrl',
])->name('profile.custom');
```

#### 8.5.2 APIルート

```php
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    // プロフィール管理
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/create', [ProfileController::class, 'store']);

    // アバター管理
    Route::post('/profile/avatar/upload', [
        AvatarController::class,
        'prepareUpload',
    ]);
    Route::put('/profile/avatar/apply', [
        AvatarController::class,
        'applyAvatar',
    ]);
    Route::delete('/profile/avatar/remove', [
        AvatarController::class,
        'removeAvatar',
    ]);

    // カスタムURL
    Route::patch('/profile/custom-url', [CustomUrlController::class, 'update']);
});

// 公開API
Route::get('/profile/{userId}', [PublicProfileController::class, 'show']);
Route::get('/profile/custom-url/check', [CustomUrlController::class, 'check']);
Route::get('/og/profile', [OgImageController::class, 'generate']);
```

### 8.6 認証・ミドルウェア

#### 8.6.1 Sanctum設定

```php
// config/sanctum.php
return [
    'stateful' => explode(
        ',',
        env(
            'SANCTUM_STATEFUL_DOMAINS',
            sprintf(
                '%s%s',
                'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
                env('APP_URL')
                    ? ',' . parse_url(env('APP_URL'), PHP_URL_HOST)
                    : '',
            ),
        ),
    ),

    'guard' => ['web'],

    'expiration' => null,

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

#### 8.6.2 認証ミドルウェア

```php
// app/Http/Middleware/Authenticate.php
protected function redirectTo(Request $request): ?string
{
    if (! $request->expectsJson()) {
        return route('login');
    }

    return null;
}
```

### 8.7 環境設定

#### 8.7.1 .env設定

```env
# データベース
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio_db
DB_USERNAME=portfolio_user
DB_PASSWORD=secure_password

# ファイルストレージ
FILESYSTEM_DISK=r2
R2_ENDPOINT=your-endpoint
R2_ACCESS_KEY_ID=your-access-key
R2_SECRET_ACCESS_KEY=your-secret-key
R2_BUCKET_NAME=your-bucket
R2_PUBLIC_URL=your-public-url

# 認証
DISCORD_CLIENT_ID=your-discord-client-id
DISCORD_CLIENT_SECRET=your-discord-client-secret
DISCORD_REDIRECT_URI=http://localhost:8000/auth/discord/callback

# アプリケーション
APP_URL=http://localhost:8000
APP_NAME="Portfolio System"
```

#### 8.7.2 ファイルシステム設定

```php
// config/filesystems.php
'r2' => [
    'driver' => 's3',
    'key' => env('R2_ACCESS_KEY_ID'),
    'secret' => env('R2_SECRET_ACCESS_KEY'),
    'region' => 'auto',
    'bucket' => env('R2_BUCKET_NAME'),
    'endpoint' => env('R2_ENDPOINT'),
    'url' => env('R2_PUBLIC_URL'),
    'use_path_style_endpoint' => false,
],
```

---

## 9. 移行チェックリスト

### 9.1 データベース移行

-   [ ] MongoDBスキーマのMySQLテーブル設計
-   [ ] データ移行スクリプトの作成
-   [ ] インデックス・制約の設定
-   [ ] データ整合性の検証

### 9.2 バックエンド移行

-   [ ] Next.js API Routes → Laravel Controllers
-   [ ] Mongoose → Eloquent ORM
-   [ ] NextAuth.js → Laravel Sanctum
-   [ ] バリデーション・エラーハンドリング

### 9.3 フロントエンド対応

-   [ ] APIエンドポイントの更新
-   [ ] 認証フローの調整
-   [ ] エラーハンドリングの更新
-   [ ] テストケースの更新

### 9.4 インフラ・デプロイ

-   [ ] 環境変数の設定
-   [ ] ファイルストレージの設定
-   [ ] データベース接続の確認
-   [ ] パフォーマンステスト
