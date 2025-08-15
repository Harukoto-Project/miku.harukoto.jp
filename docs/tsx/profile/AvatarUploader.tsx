'use client';

import { useState, useRef, useCallback, useEffect } from 'react';
import { EditorCard } from '@/components/ui/dashboard/EditorCard';
import { Skeleton, SkeletonCircle } from '@/components/ui/skeleton';
import { Toaster, toaster } from '@/components/ui/Toaster';
import Image from 'next/image';
import { useSession } from 'next-auth/react';
import * as Icons from 'react-icons/fa';

interface Profile {
    userId: string;
    avatarHash?: string;
    avatarUrl?: string;
    displayName?: string;
    bio?: string;
}

export function AvatarUploader() {
    const { data: session } = useSession();
    const [preview, setPreview] = useState<string>('');
    const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);
    const [isUploading, setIsUploading] = useState(false);
    const [isDragOver, setIsDragOver] = useState(false);
    const [isLoadingProfile, setIsLoadingProfile] = useState(true);

    const fileInputRef = useRef<HTMLInputElement>(null);
    const MAX_FILE_SIZE = 1024 * 1024;

    useEffect(() => {
        const fetchCurrentProfile = async () => {
            if (!session?.user) {
                setIsLoadingProfile(false);
                return;
            }

            try {
                const response = await fetch('/api/profile');
                if (response.ok) {
                    const profile = await response.json();
                    setCurrentProfile(profile);
                    setPreview(profile.avatarUrl || '');
                } else if (response.status !== 404) {
                    console.error('Failed to fetch profile');
                }
            } catch (error) {
                console.error('Error fetching profile:', error);
            } finally {
                setIsLoadingProfile(false);
            }
        };

        fetchCurrentProfile();
    }, [session]);

    const validateFile = useCallback(
        (file: File): string | null => {
            if (file.size > MAX_FILE_SIZE) {
                return 'ファイルサイズが1MBを超えています';
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
            ];
            if (!allowedTypes.includes(file.type)) {
                return 'JPEG、PNG、WebP、GIF形式の画像のみサポートしています';
            }

            return null;
        },
        [MAX_FILE_SIZE],
    );

    const uploadAndApplyFile = useCallback(async (file: File) => {
        try {
            setIsUploading(true);

            // ステップ1: アップロード準備
            const prepareResponse = await fetch(
                '/api/profile/avatar/upload-avatar',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        fileName: file.name,
                        fileType: file.type,
                        fileSize: file.size,
                    }),
                },
            );

            if (!prepareResponse.ok) {
                const errorData = await prepareResponse.json();
                throw new Error(
                    errorData.error || 'アップロードの準備に失敗しました',
                );
            }

            const prepareData = await prepareResponse.json();

            // ステップ2: ファイルをアップロード
            const uploadResponse = await fetch(prepareData.signedUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': file.type,
                },
                body: file,
            });

            if (!uploadResponse.ok) {
                throw new Error('ファイルのアップロードに失敗しました');
            }

            // ステップ3: プロフィールに自動適用
            const applyResponse = await fetch(
                '/api/profile/avatar/upload-avatar',
                {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        avatarUrl: prepareData.publicUrl,
                        avatarHash: prepareData.avatarHash,
                        oldAvatarUrl: prepareData.oldAvatarUrl,
                    }),
                },
            );

            if (!applyResponse.ok) {
                const errorData = await applyResponse.json();
                throw new Error(
                    errorData.error || 'アバターの適用に失敗しました',
                );
            }

            const applyData = await applyResponse.json();

            // 状態を更新
            setPreview(prepareData.publicUrl);
            setCurrentProfile(applyData.profile);

            // ファイル入力をリセット
            if (fileInputRef.current) {
                fileInputRef.current.value = '';
            }

            toaster.create({
                title: 'アップロード完了',
                description:
                    'アバターが正常にアップロードされ、プロフィールに適用されました。',
                type: 'success',
            });
        } catch (error) {
            console.error('Upload error:', error);
            toaster.create({
                title: 'アップロードエラー',
                description:
                    error instanceof Error
                        ? error.message
                        : 'アバターのアップロードに失敗しました',
                type: 'error',
            });
        } finally {
            setIsUploading(false);
        }
    }, []);

    const handleFileSelect = useCallback(
        (file: File) => {
            const error = validateFile(file);
            if (error) {
                toaster.create({
                    title: 'ファイルエラー',
                    description: error,
                    type: 'error',
                });
                return;
            }

            uploadAndApplyFile(file);
        },
        [validateFile, uploadAndApplyFile],
    );

    const handleFileChange = useCallback(
        (event: React.ChangeEvent<HTMLInputElement>) => {
            const file = event.target.files?.[0];
            if (file) {
                handleFileSelect(file);
            }
        },
        [handleFileSelect],
    );

    const handleDrop = useCallback(
        (event: React.DragEvent) => {
            event.preventDefault();
            setIsDragOver(false);

            const file = event.dataTransfer.files?.[0];
            if (file) {
                handleFileSelect(file);
            }
        },
        [handleFileSelect],
    );

    const handleDragOver = useCallback((event: React.DragEvent) => {
        event.preventDefault();
        setIsDragOver(true);
    }, []);

    const handleDragLeave = useCallback(() => {
        setIsDragOver(false);
    }, []);

    const handleRemoveAvatar = useCallback(async () => {
        if (!currentProfile?.avatarUrl) return;

        try {
            setIsUploading(true);

            const response = await fetch('/api/profile/avatar/remove-avatar', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    oldAvatarUrl: currentProfile.avatarUrl,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(
                    errorData.error || 'アバターの削除に失敗しました',
                );
            }

            const result = await response.json();
            setCurrentProfile(result.profile);
            setPreview('');

            toaster.create({
                title: '削除完了',
                description: 'アバターが正常に削除されました',
                type: 'success',
            });
        } catch (error) {
            console.error('Remove avatar error:', error);
            toaster.create({
                title: '削除エラー',
                description:
                    error instanceof Error
                        ? error.message
                        : 'アバターの削除に失敗しました',
                type: 'error',
            });
        } finally {
            setIsUploading(false);
        }
    }, [currentProfile]);

    if (!session?.user) {
        return (
            <EditorCard>
                <div className="flex w-full flex-col items-center gap-4 py-8">
                    <div className="rounded-full bg-gray-100 p-4 dark:bg-gray-800">
                        <Icons.FaUser className="h-8 w-8 text-gray-400" />
                    </div>
                    <p className="text-gray-600 dark:text-gray-400">
                        ログインが必要です
                    </p>
                </div>
            </EditorCard>
        );
    }

    return (
        <>
            <Toaster />
            <EditorCard>
                {isLoadingProfile ? (
                    <div className="flex w-full flex-col gap-6">
                        {/* タイトル部分のスケルトン */}
                        <div className="flex items-center justify-between">
                            <Skeleton height="7" width="32" />
                        </div>

                        {/* アバター表示部分のスケルトン */}
                        <div className="flex flex-col items-center gap-4">
                            <SkeletonCircle size="32" />
                            <div className="space-y-2 text-center">
                                <Skeleton height="5" width="20" />
                                <Skeleton height="4" width="16" />
                            </div>
                        </div>

                        {/* アップロード領域のスケルトン */}
                        <div className="space-y-4">
                            <Skeleton height="32" width="full" />
                            <div className="flex gap-3">
                                <Skeleton height="10" width="20" />
                                <Skeleton height="10" width="20" />
                            </div>
                        </div>

                        {/* 情報メッセージのスケルトン */}
                        <div className="space-y-3">
                            <Skeleton height="5" width="24" />
                            <div className="space-y-2">
                                <Skeleton height="4" width="full" />
                                <Skeleton height="4" width="3/4" />
                                <Skeleton height="4" width="5/6" />
                            </div>
                        </div>
                    </div>
                ) : (
                    <div className="flex w-full flex-col gap-6">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                アバター設定
                            </h3>
                        </div>

                        {/* 現在のアバター表示 */}
                        <div className="flex flex-col items-center gap-4">
                            <div className="relative">
                                {preview ? (
                                    <div className="group relative h-32 w-32">
                                        <Image
                                            src={preview}
                                            alt="Current Avatar"
                                            width={128}
                                            height={128}
                                            className="h-32 w-32 overflow-hidden rounded-full border-4 border-gray-200 object-cover shadow-lg dark:border-gray-600"
                                        />
                                        {currentProfile?.avatarUrl && (
                                            <button
                                                onClick={handleRemoveAvatar}
                                                disabled={isUploading}
                                                className="absolute -right-2 -top-2 rounded-full bg-red-500 p-2 text-white shadow-lg transition-all hover:bg-red-600 disabled:opacity-50"
                                                title="アバターを削除"
                                            >
                                                <Icons.FaTimes className="h-3 w-3" />
                                            </button>
                                        )}
                                    </div>
                                ) : (
                                    <div className="flex h-32 w-32 items-center justify-center rounded-full border-4 border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                                        <Icons.FaUser className="h-8 w-8 text-gray-400" />
                                    </div>
                                )}
                            </div>

                            <div className="text-center">
                                <p className="text-sm font-medium text-gray-900 dark:text-white">
                                    {currentProfile?.displayName ||
                                        'ユーザー名未設定'}
                                </p>
                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                    {preview
                                        ? 'アバター設定済み'
                                        : 'アバター未設定'}
                                </p>
                            </div>
                        </div>

                        {/* アップロード領域 */}
                        <div
                            className={`relative rounded-lg border-2 border-dashed p-8 text-center transition-colors ${
                                isDragOver
                                    ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20'
                                    : 'border-gray-300 bg-gray-50 hover:border-gray-400 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-gray-500'
                            } ${isUploading ? 'pointer-events-none opacity-50' : ''}`}
                            onDrop={handleDrop}
                            onDragOver={handleDragOver}
                            onDragLeave={handleDragLeave}
                        >
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                onChange={handleFileChange}
                                className="absolute inset-0 cursor-pointer opacity-0"
                                disabled={isUploading}
                            />

                            <div className="space-y-4">
                                <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                                    {isUploading ? (
                                        <div className="h-6 w-6 animate-spin rounded-full border-2 border-blue-600 border-t-transparent" />
                                    ) : (
                                        <Icons.FaCloudUploadAlt className="h-6 w-6 text-blue-600" />
                                    )}
                                </div>

                                <div>
                                    <p className="text-lg font-medium text-gray-900 dark:text-white">
                                        {isUploading
                                            ? 'アップロード中...'
                                            : 'ファイルをドロップまたはクリック'}
                                    </p>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        JPEG, PNG, WebP, GIF形式をサポート
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* アクションボタン */}
                        <div className="flex gap-3">
                            <button
                                onClick={() => fileInputRef.current?.click()}
                                disabled={isUploading}
                                className="flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white transition-all hover:bg-blue-700 disabled:opacity-50"
                            >
                                <Icons.FaUpload className="h-4 w-4" />
                                ファイルを選択
                            </button>

                            {preview && (
                                <button
                                    onClick={handleRemoveAvatar}
                                    disabled={isUploading}
                                    className="flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-3 text-sm font-medium text-red-600 transition-all hover:bg-red-50 disabled:opacity-50 dark:border-red-600 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20"
                                >
                                    <Icons.FaTrash className="h-4 w-4" />
                                    削除
                                </button>
                            )}
                        </div>

                        {/* 情報メッセージ */}
                        <div className="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <h5 className="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                                アバターについて
                            </h5>
                            <ul className="space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                <li>• 最大ファイルサイズ: 1MB</li>
                                <li>• 対応形式: JPEG, PNG, WebP, GIF</li>
                                <li>• 正方形の画像が推奨されます</li>
                                <li>
                                    •
                                    アップロード後は自動的にプロフィールに適用されます
                                </li>
                            </ul>
                        </div>
                    </div>
                )}
            </EditorCard>
        </>
    );
}
