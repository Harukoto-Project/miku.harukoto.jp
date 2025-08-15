'use client';

import { useState, useCallback, useEffect } from 'react';
import { EditorCard } from '@/components/ui/dashboard/EditorCard';
import { Skeleton } from '@/components/ui/skeleton';
import { Toaster, toaster } from '@/components/ui/Toaster';
import { useSession } from 'next-auth/react';
import * as Icons from 'react-icons/fa';

interface Profile {
    userId: string;
    customUrl?: string;
}

export function CustomUrlEditor() {
    const { data: session } = useSession();
    const [customUrl, setCustomUrl] = useState('');
    const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isLoadingProfile, setIsLoadingProfile] = useState(true);
    const [isChecking, setIsChecking] = useState(false);
    const [isAvailable, setIsAvailable] = useState<boolean | null>(null);
    const [checkMessage, setCheckMessage] = useState('');
    const [hasChanges, setHasChanges] = useState(false);

    const CUSTOM_URL_MAX_LENGTH = 30;
    const APP_URL =
        process.env.NEXT_PUBLIC_APP_URL || 'https://miku.harukoto.jp';

    useEffect(() => {
        const fetchCurrentProfile = async () => {
            if (!session?.user) {
                setIsLoadingProfile(false);
                return;
            }

            try {
                const response = await fetch('/api/profile', {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache',
                    },
                });

                if (response.ok) {
                    const profile = await response.json();
                    setCurrentProfile(profile);
                    setCustomUrl(profile.customUrl || '');
                    console.log('Fetched profile:', profile);
                } else if (response.status !== 404) {
                    console.error(
                        'Failed to fetch profile:',
                        response.status,
                        response.statusText,
                    );
                }
            } catch (error) {
                console.error('Error fetching profile:', error);
            } finally {
                setIsLoadingProfile(false);
            }
        };

        if (session?.user) {
            fetchCurrentProfile();
        } else {
            setIsLoadingProfile(false);
        }
    }, [session?.user, session?.user.id]);

    useEffect(() => {
        const originalUrl = currentProfile?.customUrl || '';
        const hasChangesValue = customUrl.trim() !== originalUrl;
        setHasChanges(hasChangesValue);
        console.log('Change detection:', {
            customUrl,
            originalUrl,
            hasChanges: hasChangesValue,
        });
    }, [customUrl, currentProfile]);

    const checkAvailability = useCallback(
        async (url: string) => {
            if (!url || url.length < 3) {
                setIsAvailable(null);
                setCheckMessage('');
                return;
            }

            if (!/^[a-zA-Z0-9._]{3,30}$/.test(url)) {
                setIsAvailable(false);
                setCheckMessage(
                    '英数字、ピリオド、アンダーバーのみで3文字以上30文字以下で入力してください',
                );
                return;
            }

            if (url === (currentProfile?.customUrl || '')) {
                setIsAvailable(null);
                setCheckMessage('');
                return;
            }

            setIsChecking(true);
            try {
                const response = await fetch(
                    `/api/profile/custom-url/check?url=${encodeURIComponent(url)}`,
                );
                if (response.ok) {
                    const data = await response.json();
                    setIsAvailable(data.available);
                    setCheckMessage(
                        data.available
                            ? '✓ 利用可能です'
                            : '✗ このURLは既に使用されています',
                    );
                } else {
                    setIsAvailable(false);
                    setCheckMessage('確認中にエラーが発生しました');
                }
            } catch (error) {
                console.error('Availability check error:', error);
                setIsAvailable(false);
                setCheckMessage('確認中にエラーが発生しました');
            } finally {
                setIsChecking(false);
            }
        },
        [currentProfile?.customUrl],
    );

    useEffect(() => {
        const timer = setTimeout(() => {
            if (customUrl !== (currentProfile?.customUrl || '')) {
                checkAvailability(customUrl);
            } else {
                setIsAvailable(null);
                setCheckMessage('');
            }
        }, 500);

        return () => clearTimeout(timer);
    }, [customUrl, currentProfile, checkAvailability]);

    const handleCustomUrlChange = useCallback(
        (event: React.ChangeEvent<HTMLInputElement>) => {
            const value = event.target.value
                .toLowerCase()
                .replace(/[^a-z0-9._]/g, '');
            if (value.length <= CUSTOM_URL_MAX_LENGTH) {
                setCustomUrl(value);
            }
        },
        [],
    );

    const handleSave = useCallback(async () => {
        if (!session?.user || isLoading) return;

        const trimmedUrl = customUrl.trim();

        try {
            setIsLoading(true);

            const response = await fetch('/api/profile/custom-url', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    customUrl: trimmedUrl || null,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(
                    errorData.message || 'カスタムURLの更新に失敗しました',
                );
            }

            const result = await response.json();
            console.log('Save result:', result);

            setCurrentProfile(result.profile);
            setCustomUrl(result.profile.customUrl || '');
            setHasChanges(false);
            setIsAvailable(null);
            setCheckMessage('');

            toaster.create({
                title: '保存完了',
                description: 'カスタムURLが正常に更新されました',
                type: 'success',
            });

            setTimeout(async () => {
                try {
                    const refreshResponse = await fetch('/api/profile', {
                        method: 'GET',
                        headers: {
                            'Cache-Control': 'no-cache',
                        },
                    });
                    if (refreshResponse.ok) {
                        const refreshedProfile = await response.json();
                        setCurrentProfile(refreshedProfile);
                        setCustomUrl(refreshedProfile.customUrl || '');
                    }
                } catch (error) {
                    console.error('Profile refresh error:', error);
                }
            }, 1000);
        } catch (error) {
            console.error('Save error:', error);
            toaster.create({
                title: '保存エラー',
                description:
                    error instanceof Error
                        ? error.message
                        : 'カスタムURLの保存に失敗しました',
                type: 'error',
            });
        } finally {
            setIsLoading(false);
        }
    }, [customUrl, session, isLoading]);

    const handleReset = useCallback(() => {
        const originalUrl = currentProfile?.customUrl || '';
        setCustomUrl(originalUrl);
        setHasChanges(false);
        setIsAvailable(null);
        setCheckMessage('');
    }, [currentProfile]);

    const handleClear = useCallback(() => {
        setCustomUrl('');
    }, []);

    if (!session?.user) {
        return (
            <EditorCard>
                <div className="flex w-full flex-col items-center gap-4 py-8">
                    <div className="rounded-full bg-gray-100 p-4 dark:bg-gray-800">
                        <Icons.FaLink className="h-8 w-8 text-gray-400" />
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
                        {/* Loading skeleton */}
                        <div className="flex items-center justify-between">
                            <Skeleton height="7" width="32" />
                        </div>
                        <div className="space-y-4">
                            <div>
                                <Skeleton height="4" width="20" />
                                <div className="mt-2 flex items-center gap-2">
                                    <Skeleton height="4" width="40" />
                                    <Skeleton height="12" width="full" />
                                </div>
                                <div className="mt-2 flex justify-between">
                                    <Skeleton height="3" width="32" />
                                    <Skeleton height="3" width="12" />
                                </div>
                            </div>
                        </div>
                        <div className="space-y-3">
                            <Skeleton height="5" width="24" />
                            <Skeleton height="12" width="full" />
                        </div>
                        <div className="flex gap-3">
                            <Skeleton height="12" width="20" />
                            <Skeleton height="12" width="20" />
                            <Skeleton height="12" width="full" />
                        </div>
                    </div>
                ) : (
                    <div className="flex w-full flex-col gap-6">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                カスタムURL設定
                            </h3>
                            {hasChanges && (
                                <div className="flex items-center gap-1 text-sm text-orange-600 dark:text-orange-400">
                                    <Icons.FaExclamationTriangle className="h-4 w-4" />
                                    未保存の変更
                                </div>
                            )}
                        </div>

                        {/* カスタムURL入力 */}
                        <div className="space-y-4">
                            <div>
                                <label
                                    htmlFor="customUrl"
                                    className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    カスタムURL
                                </label>
                                <div className="flex items-center gap-2">
                                    <span className="whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {APP_URL}/p/
                                    </span>
                                    <div className="relative flex-1">
                                        <input
                                            id="customUrl"
                                            type="text"
                                            value={customUrl}
                                            onChange={handleCustomUrlChange}
                                            placeholder="your-custom-url"
                                            className={`w-full rounded-lg border px-4 py-3 pr-12 text-sm transition-all focus:outline-none focus:ring-2 dark:bg-gray-800 dark:text-gray-100 ${
                                                hasChanges
                                                    ? 'border-orange-300 focus:border-orange-500 focus:ring-orange-500/20'
                                                    : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/20 dark:border-gray-600'
                                            }`}
                                            disabled={isLoading}
                                        />
                                        <span className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                                            {customUrl.length}/
                                            {CUSTOM_URL_MAX_LENGTH}
                                        </span>
                                    </div>
                                </div>
                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    英数字、ピリオド、アンダーバーのみで3文字以上30文字以下で設定してください
                                </p>

                                {/* 可用性チェック結果 */}
                                {(isChecking || checkMessage) && (
                                    <div className="mt-2">
                                        {isChecking ? (
                                            <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                                <div className="h-4 w-4 animate-spin rounded-full border-2 border-gray-400 border-t-transparent" />
                                                確認中...
                                            </div>
                                        ) : (
                                            <div
                                                className={`text-sm ${
                                                    isAvailable === true
                                                        ? 'text-green-600 dark:text-green-400'
                                                        : isAvailable === false
                                                          ? 'text-red-600 dark:text-red-400'
                                                          : 'text-gray-600 dark:text-gray-400'
                                                }`}
                                            >
                                                {checkMessage}
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* プレビュー */}
                        {customUrl && (
                            <div className="rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
                                <h5 className="mb-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                    プレビュー
                                </h5>
                                <div className="break-all text-sm text-blue-600 dark:text-blue-400">
                                    {APP_URL}/p/{customUrl}
                                </div>
                            </div>
                        )}

                        {/* 情報メッセージ */}
                        <div className="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <h5 className="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                                カスタムURLについて
                            </h5>
                            <ul className="space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                <li>
                                    •
                                    他のユーザーがあなたのプロフィールにアクセスしやすくなります
                                </li>
                                <li>
                                    •
                                    一度設定したURLは他のユーザーが使用できなくなります
                                </li>
                                <li>• 空にすると標準のURL形式になります</li>
                                <li>• 後からいつでも変更できます</li>
                            </ul>
                        </div>

                        {/* アクションボタン */}
                        <div className="flex gap-3">
                            <button
                                onClick={handleReset}
                                disabled={!hasChanges || isLoading}
                                className={`flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium transition-all dark:border-gray-600 ${
                                    !hasChanges || isLoading
                                        ? 'cursor-not-allowed opacity-50'
                                        : 'bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'
                                }`}
                            >
                                <Icons.FaUndo className="h-4 w-4" />
                                リセット
                            </button>

                            {customUrl && (
                                <button
                                    onClick={handleClear}
                                    disabled={isLoading}
                                    className={`flex items-center justify-center gap-2 rounded-lg border border-red-300 px-4 py-3 text-sm font-medium transition-all dark:border-red-600 ${
                                        isLoading
                                            ? 'cursor-not-allowed opacity-50'
                                            : 'bg-white text-red-700 hover:border-red-400 hover:bg-red-50 dark:bg-gray-800 dark:text-red-300 dark:hover:bg-red-900/20'
                                    }`}
                                >
                                    <Icons.FaTimes className="h-4 w-4" />
                                    クリア
                                </button>
                            )}

                            <button
                                onClick={handleSave}
                                disabled={
                                    !hasChanges ||
                                    isLoading ||
                                    !!(customUrl && isAvailable !== true)
                                }
                                className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-medium text-white transition-all ${
                                    !hasChanges ||
                                    isLoading ||
                                    (customUrl && isAvailable !== true)
                                        ? 'cursor-not-allowed bg-blue-400'
                                        : 'bg-blue-600 shadow-lg hover:bg-blue-700 hover:shadow-xl'
                                }`}
                            >
                                {isLoading ? (
                                    <div className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                ) : (
                                    <>
                                        <Icons.FaSave className="h-4 w-4" />
                                        保存
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                )}
            </EditorCard>
        </>
    );
}
