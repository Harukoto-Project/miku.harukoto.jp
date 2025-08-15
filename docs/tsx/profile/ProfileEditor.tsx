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
}

export function ProfileEditor() {
    const { data: session } = useSession();
    const [bio, setBio] = useState('');
    const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isLoadingProfile, setIsLoadingProfile] = useState(true);
    const [hasChanges, setHasChanges] = useState(false);

    const MAX_LENGTH = 500;

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
                    setBio(profile.bio || '');
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
        const originalBio = currentProfile?.bio || '';
        setHasChanges(bio.trim() !== originalBio);
    }, [bio, currentProfile]);

    const handleTextareaChange = useCallback(
        (event: React.ChangeEvent<HTMLTextAreaElement>) => {
            const value = event.target.value;
            if (value.length <= MAX_LENGTH) {
                setBio(value);
            }
        },
        [],
    );

    const handleSave = useCallback(async () => {
        if (!session?.user || isLoading) return;

        const trimmedBio = bio.trim();

        try {
            setIsLoading(true);

            const response = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    bio: trimmedBio,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(
                    errorData.error || '自己紹介の更新に失敗しました',
                );
            }

            const result = await response.json();
            setCurrentProfile(result);
            setHasChanges(false);

            toaster.create({
                title: '保存完了',
                description: '自己紹介が正常に更新されました',
                type: 'success',
            });
        } catch (error) {
            console.error('Save error:', error);
            toaster.create({
                title: '保存エラー',
                description:
                    error instanceof Error
                        ? error.message
                        : '自己紹介の保存に失敗しました',
                type: 'error',
            });
        } finally {
            setIsLoading(false);
        }
    }, [bio, session, isLoading]);

    const handleReset = useCallback(() => {
        const originalBio = currentProfile?.bio || '';
        setBio(originalBio);
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
                            <Skeleton height="7" width="28" />
                        </div>

                        {/* 現在の設定表示部分のスケルトン */}
                        <div className="space-y-3">
                            <Skeleton height="5" width="24" />
                            <div className="space-y-2">
                                <Skeleton height="4" width="full" />
                                <Skeleton height="4" width="3/4" />
                                <Skeleton height="4" width="5/6" />
                                <Skeleton height="4" width="2/3" />
                            </div>
                        </div>

                        {/* 入力フォーム部分のスケルトン */}
                        <div className="space-y-4">
                            <div className="flex justify-between">
                                <Skeleton height="4" width="20" />
                                <Skeleton height="4" width="12" />
                            </div>
                            <Skeleton height="32" width="full" />
                        </div>

                        {/* 情報メッセージのスケルトン */}
                        <div className="space-y-3">
                            <Skeleton height="5" width="32" />
                            <div className="space-y-2">
                                <Skeleton height="4" width="full" />
                                <Skeleton height="4" width="4/5" />
                                <Skeleton height="4" width="3/4" />
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
                                自己紹介設定
                            </h3>
                            {hasChanges && (
                                <div className="flex items-center gap-1 text-sm text-orange-600 dark:text-orange-400">
                                    <Icons.FaExclamationTriangle className="h-4 w-4" />
                                    未保存の変更
                                </div>
                            )}
                        </div>

                        {/* 現在の設定表示 */}
                        <div className="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                            <h4 className="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                現在の自己紹介
                            </h4>
                            <div className="rounded-md bg-white p-3 dark:bg-gray-700">
                                {currentProfile?.bio ? (
                                    <p className="whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">
                                        {currentProfile.bio}
                                    </p>
                                ) : (
                                    <p className="text-sm italic text-gray-500 dark:text-gray-400">
                                        未設定
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* 入力フォーム */}
                        <div className="space-y-4">
                            <div>
                                <div className="mb-2 flex items-center justify-between">
                                    <label
                                        htmlFor="bio"
                                        className="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        新しい自己紹介
                                    </label>
                                    <span
                                        className={`text-xs ${
                                            bio.length > MAX_LENGTH * 0.8
                                                ? 'text-amber-600 dark:text-amber-400'
                                                : 'text-gray-500'
                                        }`}
                                    >
                                        {bio.length}/{MAX_LENGTH}
                                    </span>
                                </div>
                                <textarea
                                    id="bio"
                                    value={bio}
                                    onChange={handleTextareaChange}
                                    placeholder="自己紹介を入力してください..."
                                    rows={6}
                                    className={`w-full resize-none rounded-lg border px-4 py-3 text-sm transition-all focus:outline-none focus:ring-2 dark:bg-gray-800 dark:text-gray-100 ${
                                        hasChanges
                                            ? 'border-orange-300 focus:border-orange-500 focus:ring-orange-500/20'
                                            : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/20 dark:border-gray-600'
                                    }`}
                                    disabled={isLoading}
                                />
                            </div>

                            {/* 文字数警告 */}
                            {bio.length > MAX_LENGTH * 0.8 && (
                                <div className="rounded-md bg-amber-50 p-3 dark:bg-amber-900/20">
                                    <div className="flex">
                                        <Icons.FaExclamationTriangle className="h-5 w-5 text-amber-400" />
                                        <div className="ml-3">
                                            <p className="text-sm text-amber-800 dark:text-amber-200">
                                                文字数が上限に近づいています
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* 情報メッセージ */}
                        <div className="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <h5 className="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                                自己紹介について
                            </h5>
                            <ul className="space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                <li>• 最大{MAX_LENGTH}文字まで入力できます</li>
                                <li>• 改行も含まれて表示されます</li>
                                <li>• プロフィールページに公開されます</li>
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
