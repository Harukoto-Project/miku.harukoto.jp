'use client';

import { useState, useCallback, useEffect } from 'react';
import { EditorCard } from '@/components/ui/dashboard/EditorCard';
import { Skeleton } from '@/components/ui/skeleton';
import { Toaster, toaster } from '@/components/ui/Toaster';
import { useSession } from 'next-auth/react';
import * as Icons from 'react-icons/fa';

interface Profile {
    userId: string;
    avatarHash?: string;
    avatarUrl?: string;
    displayName?: string;
    bio?: string;
    pronouns?: string;
    location?: string;
}

export function DisplayNameEditor() {
    const { data: session } = useSession();
    const [displayName, setDisplayName] = useState('');
    const [pronouns, setPronouns] = useState('');
    const [location, setLocation] = useState('');
    const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isLoadingProfile, setIsLoadingProfile] = useState(true);
    const [hasChanges, setHasChanges] = useState(false);

    const DISPLAY_NAME_MAX_LENGTH = 50;
    const PRONOUNS_MAX_LENGTH = 32;
    const LOCATION_MAX_LENGTH = 32;

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
                    setDisplayName(profile.displayName || '');
                    setPronouns(profile.pronouns || '');
                    setLocation(profile.location || '');
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

    useEffect(() => {
        const originalName = currentProfile?.displayName || '';
        const originalPronouns = currentProfile?.pronouns || '';
        const originalLocation = currentProfile?.location || '';

        setHasChanges(
            displayName.trim() !== originalName ||
                pronouns.trim() !== originalPronouns ||
                location.trim() !== originalLocation,
        );
    }, [displayName, pronouns, location, currentProfile]);

    const handleDisplayNameChange = useCallback(
        (event: React.ChangeEvent<HTMLInputElement>) => {
            const value = event.target.value;
            if (value.length <= DISPLAY_NAME_MAX_LENGTH) {
                setDisplayName(value);
            }
        },
        [],
    );

    const handlePronounsChange = useCallback(
        (event: React.ChangeEvent<HTMLInputElement>) => {
            const value = event.target.value;
            if (value.length <= PRONOUNS_MAX_LENGTH) {
                setPronouns(value);
            }
        },
        [],
    );

    const handleLocationChange = useCallback(
        (event: React.ChangeEvent<HTMLInputElement>) => {
            const value = event.target.value;
            if (value.length <= LOCATION_MAX_LENGTH) {
                setLocation(value);
            }
        },
        [],
    );

    const handleSave = useCallback(async () => {
        if (!session?.user || isLoading) return;

        const trimmedName = displayName.trim();
        const trimmedPronouns = pronouns.trim();
        const trimmedLocation = location.trim();

        try {
            setIsLoading(true);

            const response = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    displayName: trimmedName,
                    pronouns: trimmedPronouns,
                    location: trimmedLocation,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(
                    errorData.error || 'プロファイルの更新に失敗しました',
                );
            }

            const result = await response.json();
            setCurrentProfile(result);
            setHasChanges(false);

            toaster.create({
                title: '保存完了',
                description: 'プロファイルが正常に更新されました',
                type: 'success',
            });
        } catch (error) {
            console.error('Save error:', error);
            toaster.create({
                title: '保存エラー',
                description:
                    error instanceof Error
                        ? error.message
                        : 'プロファイルの保存に失敗しました',
                type: 'error',
            });
        } finally {
            setIsLoading(false);
        }
    }, [displayName, pronouns, location, session, isLoading]);

    const handleReset = useCallback(() => {
        const originalName = currentProfile?.displayName || '';
        const originalPronouns = currentProfile?.pronouns || '';
        const originalLocation = currentProfile?.location || '';

        setDisplayName(originalName);
        setPronouns(originalPronouns);
        setLocation(originalLocation);
        setHasChanges(false);
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

                        {/* 入力フォーム部分のスケルトン */}
                        <div className="space-y-4">
                            {/* 表示名 */}
                            <div>
                                <Skeleton height="4" width="16" />
                                <Skeleton
                                    height="12"
                                    width="full"
                                    className="mt-2"
                                />
                                <div className="mt-2 flex justify-between">
                                    <Skeleton height="3" width="20" />
                                    <Skeleton height="3" width="12" />
                                </div>
                            </div>

                            {/* 代名詞 */}
                            <div>
                                <Skeleton height="4" width="12" />
                                <Skeleton
                                    height="12"
                                    width="full"
                                    className="mt-2"
                                />
                                <div className="mt-2 flex justify-between">
                                    <Skeleton height="3" width="24" />
                                    <Skeleton height="3" width="10" />
                                </div>
                            </div>

                            {/* 場所 */}
                            <div>
                                <Skeleton height="4" width="8" />
                                <Skeleton
                                    height="12"
                                    width="full"
                                    className="mt-2"
                                />
                                <div className="mt-2 flex justify-between">
                                    <Skeleton height="3" width="16" />
                                    <Skeleton height="3" width="10" />
                                </div>
                            </div>
                        </div>

                        {/* 情報メッセージのスケルトン */}
                        <div className="space-y-3">
                            <Skeleton height="5" width="28" />
                            <div className="space-y-2">
                                <Skeleton height="4" width="full" />
                                <Skeleton height="4" width="3/4" />
                                <Skeleton height="4" width="5/6" />
                            </div>
                        </div>

                        {/* ボタン部分のスケルトン */}
                        <div className="flex gap-3">
                            <Skeleton height="12" width="20" />
                            <Skeleton height="12" width="full" />
                        </div>
                    </div>
                ) : (
                    <div className="flex w-full flex-col gap-6">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                プロファイル設定
                            </h3>
                            {hasChanges && (
                                <div className="flex items-center gap-1 text-sm text-orange-600 dark:text-orange-400">
                                    <Icons.FaExclamationTriangle className="h-4 w-4" />
                                    未保存の変更
                                </div>
                            )}
                        </div>

                        {/* 入力フォーム */}
                        <div className="space-y-4">
                            {/* 表示名 */}
                            <div>
                                <label
                                    htmlFor="displayName"
                                    className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    表示名
                                </label>
                                <div className="relative">
                                    <input
                                        id="displayName"
                                        type="text"
                                        value={displayName}
                                        onChange={handleDisplayNameChange}
                                        placeholder="表示名を入力してください"
                                        className={`w-full rounded-lg border px-4 py-3 pr-12 text-sm transition-all focus:outline-none focus:ring-2 dark:bg-gray-800 dark:text-gray-100 ${
                                            hasChanges
                                                ? 'border-orange-300 focus:border-orange-500 focus:ring-orange-500/20'
                                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/20 dark:border-gray-600'
                                        }`}
                                        disabled={isLoading}
                                    />
                                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                                        {displayName.length}/
                                        {DISPLAY_NAME_MAX_LENGTH}
                                    </span>
                                </div>
                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    プロフィールページに表示される名前です
                                </p>
                            </div>

                            {/* 代名詞 */}
                            <div>
                                <label
                                    htmlFor="pronouns"
                                    className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    代名詞・職業など
                                </label>
                                <div className="relative">
                                    <input
                                        id="pronouns"
                                        type="text"
                                        value={pronouns}
                                        onChange={handlePronounsChange}
                                        placeholder="例: he/him, Developer, Student"
                                        className={`w-full rounded-lg border px-4 py-3 pr-12 text-sm transition-all focus:outline-none focus:ring-2 dark:bg-gray-800 dark:text-gray-100 ${
                                            hasChanges
                                                ? 'border-orange-300 focus:border-orange-500 focus:ring-orange-500/20'
                                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/20 dark:border-gray-600'
                                        }`}
                                        disabled={isLoading}
                                    />
                                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                                        {pronouns.length}/{PRONOUNS_MAX_LENGTH}
                                    </span>
                                </div>
                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    代名詞や職業、役職などを入力してください（任意）
                                </p>
                            </div>

                            {/* 場所 */}
                            <div>
                                <label
                                    htmlFor="location"
                                    className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    場所
                                </label>
                                <div className="relative">
                                    <input
                                        id="location"
                                        type="text"
                                        value={location}
                                        onChange={handleLocationChange}
                                        placeholder="例: Tokyo, Japan"
                                        className={`w-full rounded-lg border px-4 py-3 pr-12 text-sm transition-all focus:outline-none focus:ring-2 dark:bg-gray-800 dark:text-gray-100 ${
                                            hasChanges
                                                ? 'border-orange-300 focus:border-orange-500 focus:ring-orange-500/20'
                                                : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/20 dark:border-gray-600'
                                        }`}
                                        disabled={isLoading}
                                    />
                                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                                        {location.length}/{LOCATION_MAX_LENGTH}
                                    </span>
                                </div>
                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    居住地や活動拠点を入力してください（任意）
                                </p>
                            </div>
                        </div>

                        {/* 情報メッセージ */}
                        <div className="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <h5 className="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                                プロファイル情報について
                            </h5>
                            <ul className="space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                <li>• 表示名以外は任意入力です</li>
                                <li>
                                    •
                                    すべての情報がプロフィールページに公開されます
                                </li>
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

                            <button
                                onClick={handleSave}
                                disabled={!hasChanges || isLoading}
                                className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-medium text-white transition-all ${
                                    !hasChanges || isLoading
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
