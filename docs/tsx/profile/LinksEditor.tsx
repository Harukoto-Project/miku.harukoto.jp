'use client';

import { useState, useCallback, useEffect } from 'react';
import { EditorCard } from '@/components/ui/dashboard/EditorCard';
import { Skeleton } from '@/components/ui/skeleton';
import { Toaster, toaster } from '@/components/ui/Toaster';
import { useSession } from 'next-auth/react';
import * as Icons from 'react-icons/fa';
import { IconType } from 'react-icons';

interface Link {
    buttonId: string;
    url: string;
    displayName: string;
}

interface Profile {
    userId: string;
    avatarHash?: string;
    avatarUrl?: string;
    displayName?: string;
    bio?: string;
    pronouns?: string;
    location?: string;
    links?: Link[];
}

interface ButtonType {
    id: string;
    label: string;
    icon: IconType;
    color: string;
}

// 利用可能なボタンの種類
const AVAILABLE_BUTTONS: ButtonType[] = [
    {
        id: 'website',
        label: 'ウェブサイト',
        icon: Icons.FaGlobe,
        color: 'bg-blue-600',
    },
    {
        id: 'twitter',
        label: 'Twitter',
        icon: Icons.FaTwitter,
        color: 'bg-sky-500',
    },
    {
        id: 'github',
        label: 'GitHub',
        icon: Icons.FaGithub,
        color: 'bg-gray-800',
    },
    {
        id: 'youtube',
        label: 'YouTube',
        icon: Icons.FaYoutube,
        color: 'bg-red-600',
    },
    {
        id: 'instagram',
        label: 'Instagram',
        icon: Icons.FaInstagram,
        color: 'bg-pink-600',
    },
    {
        id: 'discord',
        label: 'Discord',
        icon: Icons.FaDiscord,
        color: 'bg-indigo-600',
    },
    {
        id: 'twitch',
        label: 'Twitch',
        icon: Icons.FaTwitch,
        color: 'bg-purple-600',
    },
    {
        id: 'linkedin',
        label: 'LinkedIn',
        icon: Icons.FaLinkedin,
        color: 'bg-blue-700',
    },
    {
        id: 'facebook',
        label: 'Facebook',
        icon: Icons.FaFacebook,
        color: 'bg-blue-800',
    },
    { id: 'tiktok', label: 'TikTok', icon: Icons.FaTiktok, color: 'bg-black' },
    { id: 'steam', label: 'Steam', icon: Icons.FaSteam, color: 'bg-gray-700' },
    {
        id: 'spotify',
        label: 'Spotify',
        icon: Icons.FaSpotify,
        color: 'bg-green-600',
    },
    {
        id: 'paypal',
        label: 'PayPal',
        icon: Icons.FaPaypal,
        color: 'bg-blue-600',
    },
    {
        id: 'amazon',
        label: 'Amazon',
        icon: Icons.FaAmazon,
        color: 'bg-orange-500',
    },
    {
        id: 'email',
        label: 'メール',
        icon: Icons.FaEnvelope,
        color: 'bg-gray-600',
    },
];

const MAX_LINKS = 10;
const MAX_URL_LENGTH = 500;
const MAX_DISPLAY_NAME_LENGTH = 50;

export function LinksEditor() {
    const { data: session } = useSession();
    const [links, setLinks] = useState<Link[]>([]);
    const [currentProfile, setCurrentProfile] = useState<Profile | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isLoadingProfile, setIsLoadingProfile] = useState(true);
    const [hasChanges, setHasChanges] = useState(false);

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
                    setLinks(profile.links || []);
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
        const originalLinks = currentProfile?.links || [];
        const hasLinkChanges =
            JSON.stringify(links) !== JSON.stringify(originalLinks);
        setHasChanges(hasLinkChanges);
    }, [links, currentProfile]);

    const addLink = useCallback(() => {
        if (links.length >= MAX_LINKS) {
            toaster.create({
                title: '制限に達しました',
                description: `リンクは最大${MAX_LINKS}個まで追加できます`,
                type: 'warning',
            });
            return;
        }

        const newLink: Link = {
            buttonId: AVAILABLE_BUTTONS[0].id,
            url: '',
            displayName: '',
        };
        setLinks([...links, newLink]);
    }, [links]);

    const removeLink = useCallback(
        (index: number) => {
            setLinks(links.filter((_, i) => i !== index));
        },
        [links],
    );

    const updateLink = useCallback(
        (index: number, field: keyof Link, value: string) => {
            const updatedLinks = links.map((link, i) =>
                i === index ? { ...link, [field]: value } : link,
            );
            setLinks(updatedLinks);
        },
        [links],
    );

    const handleSave = useCallback(async () => {
        if (!session?.user || isLoading) return;

        // バリデーション
        const invalidLinks = links.filter(
            (link) =>
                !link.buttonId || !link.url.trim() || !link.displayName.trim(),
        );

        if (invalidLinks.length > 0) {
            toaster.create({
                title: '入力エラー',
                description: 'すべてのフィールドを正しく入力してください',
                type: 'error',
            });
            return;
        }

        // URL形式のバリデーション
        const urlPattern = /^https?:\/\/.+/;
        const invalidUrls = links.filter((link) => !urlPattern.test(link.url));

        if (invalidUrls.length > 0) {
            toaster.create({
                title: 'URL形式エラー',
                description: 'URLはhttp://またはhttps://で始まる必要があります',
                type: 'error',
            });
            return;
        }

        try {
            setIsLoading(true);

            const response = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    links: links,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(
                    errorData.error || 'リンクの更新に失敗しました',
                );
            }

            const result = await response.json();
            setCurrentProfile(result);
            setHasChanges(false);

            toaster.create({
                title: '保存完了',
                description: 'リンクが正常に更新されました',
                type: 'success',
            });
        } catch (error) {
            console.error('Save error:', error);
            toaster.create({
                title: '保存エラー',
                description:
                    error instanceof Error
                        ? error.message
                        : 'リンクの保存に失敗しました',
                type: 'error',
            });
        } finally {
            setIsLoading(false);
        }
    }, [links, session, isLoading]);

    const handleReset = useCallback(() => {
        const originalLinks = currentProfile?.links || [];
        setLinks(originalLinks);
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
                            <Skeleton height="7" width="24" />
                            <Skeleton height="6" width="20" />
                        </div>

                        {/* 現在のリンク表示部分のスケルトン */}
                        <div className="space-y-3">
                            <Skeleton height="5" width="20" />
                            <div className="space-y-2">
                                {Array.from({ length: 3 }).map((_, index) => (
                                    <div
                                        key={index}
                                        className="flex items-center gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                    >
                                        <Skeleton height="8" width="8" />
                                        <div className="flex-1 space-y-2">
                                            <Skeleton height="4" width="24" />
                                            <Skeleton height="3" width="32" />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* リンク編集部分のスケルトン */}
                        <div className="space-y-4">
                            <Skeleton height="5" width="16" />
                            {Array.from({ length: 2 }).map((_, index) => (
                                <div
                                    key={index}
                                    className="space-y-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                >
                                    <div className="flex items-center justify-between">
                                        <Skeleton height="5" width="16" />
                                        <Skeleton height="6" width="8" />
                                    </div>
                                    <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                                        <Skeleton height="12" width="full" />
                                        <Skeleton height="12" width="full" />
                                        <Skeleton height="12" width="full" />
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* ボタン部分のスケルトン */}
                        <div className="flex gap-3">
                            <Skeleton height="12" width="24" />
                            <Skeleton height="12" width="20" />
                            <Skeleton height="12" width="full" />
                        </div>
                    </div>
                ) : (
                    <div className="flex w-full flex-col gap-6">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                リンク設定
                            </h3>
                            {hasChanges && (
                                <div className="flex items-center gap-1 text-sm text-orange-600 dark:text-orange-400">
                                    <Icons.FaExclamationTriangle className="h-4 w-4" />
                                    未保存の変更
                                </div>
                            )}
                        </div>

                        {/* 現在のリンク表示 */}
                        <div className="space-y-3">
                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                現在のリンク (
                                {currentProfile?.links?.length || 0}/{MAX_LINKS}
                                )
                            </h4>
                            {currentProfile?.links &&
                            currentProfile.links.length > 0 ? (
                                <div className="space-y-2">
                                    {currentProfile.links.map((link, index) => {
                                        const buttonType =
                                            AVAILABLE_BUTTONS.find(
                                                (btn) =>
                                                    btn.id === link.buttonId,
                                            );
                                        const IconComponent =
                                            buttonType?.icon || Icons.FaGlobe;

                                        return (
                                            <div
                                                key={index}
                                                className="flex items-center gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                            >
                                                <div
                                                    className={`rounded-full p-2 text-white ${buttonType?.color || 'bg-gray-600'}`}
                                                >
                                                    <IconComponent className="h-4 w-4" />
                                                </div>
                                                <div className="flex-1">
                                                    <p className="font-medium text-gray-900 dark:text-white">
                                                        {link.displayName}
                                                    </p>
                                                    <p className="truncate text-sm text-gray-500 dark:text-gray-400">
                                                        {link.url}
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            ) : (
                                <div className="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-600">
                                    <Icons.FaLink className="mx-auto h-8 w-8 text-gray-400" />
                                    <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        まだリンクが設定されていません
                                    </p>
                                </div>
                            )}
                        </div>

                        {/* リンク編集 */}
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    リンクの編集
                                </h4>
                                <button
                                    onClick={addLink}
                                    disabled={
                                        links.length >= MAX_LINKS || isLoading
                                    }
                                    className="flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-all hover:bg-blue-700 disabled:opacity-50"
                                >
                                    <Icons.FaPlus className="h-3 w-3" />
                                    追加
                                </button>
                            </div>

                            {links.length === 0 ? (
                                <div className="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-gray-600">
                                    <Icons.FaPlus className="mx-auto h-8 w-8 text-gray-400" />
                                    <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        「追加」ボタンでリンクを追加できます
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {links.map((link, index) => {
                                        const buttonType =
                                            AVAILABLE_BUTTONS.find(
                                                (btn) =>
                                                    btn.id === link.buttonId,
                                            );

                                        return (
                                            <div
                                                key={index}
                                                className="space-y-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700"
                                            >
                                                <div className="flex items-center justify-between">
                                                    <h5 className="font-medium text-gray-900 dark:text-white">
                                                        リンク #{index + 1}
                                                    </h5>
                                                    <button
                                                        onClick={() =>
                                                            removeLink(index)
                                                        }
                                                        className="text-red-600 hover:text-red-700 dark:text-red-400"
                                                        disabled={isLoading}
                                                    >
                                                        <Icons.FaTrash className="h-4 w-4" />
                                                    </button>
                                                </div>

                                                <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                                                    {/* ボタンタイプ選択 */}
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            ボタンタイプ
                                                        </label>
                                                        <select
                                                            value={
                                                                link.buttonId
                                                            }
                                                            onChange={(e) =>
                                                                updateLink(
                                                                    index,
                                                                    'buttonId',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                                                            disabled={isLoading}
                                                        >
                                                            {AVAILABLE_BUTTONS.map(
                                                                (button) => (
                                                                    <option
                                                                        key={
                                                                            button.id
                                                                        }
                                                                        value={
                                                                            button.id
                                                                        }
                                                                    >
                                                                        {
                                                                            button.label
                                                                        }
                                                                    </option>
                                                                ),
                                                            )}
                                                        </select>
                                                    </div>

                                                    {/* 表示名 */}
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            表示名
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={
                                                                link.displayName
                                                            }
                                                            onChange={(e) => {
                                                                if (
                                                                    e.target
                                                                        .value
                                                                        .length <=
                                                                    MAX_DISPLAY_NAME_LENGTH
                                                                ) {
                                                                    updateLink(
                                                                        index,
                                                                        'displayName',
                                                                        e.target
                                                                            .value,
                                                                    );
                                                                }
                                                            }}
                                                            placeholder="My Website"
                                                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                                                            disabled={isLoading}
                                                        />
                                                        <p className="mt-1 text-xs text-gray-500">
                                                            {
                                                                link.displayName
                                                                    .length
                                                            }
                                                            /
                                                            {
                                                                MAX_DISPLAY_NAME_LENGTH
                                                            }
                                                        </p>
                                                    </div>

                                                    {/* URL */}
                                                    <div>
                                                        <label className="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            URL
                                                        </label>
                                                        <input
                                                            type="url"
                                                            value={link.url}
                                                            onChange={(e) => {
                                                                if (
                                                                    e.target
                                                                        .value
                                                                        .length <=
                                                                    MAX_URL_LENGTH
                                                                ) {
                                                                    updateLink(
                                                                        index,
                                                                        'url',
                                                                        e.target
                                                                            .value,
                                                                    );
                                                                }
                                                            }}
                                                            placeholder="https://example.com"
                                                            className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                                                            disabled={isLoading}
                                                        />
                                                        <p className="mt-1 text-xs text-gray-500">
                                                            {link.url.length}/
                                                            {MAX_URL_LENGTH}
                                                        </p>
                                                    </div>
                                                </div>

                                                {/* プレビュー */}
                                                {link.displayName &&
                                                    link.url && (
                                                        <div className="mt-3 flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-800/50">
                                                            <div
                                                                className={`rounded-full p-2 text-white ${buttonType?.color || 'bg-gray-600'}`}
                                                            >
                                                                {buttonType?.icon ? (
                                                                    <buttonType.icon className="h-4 w-4" />
                                                                ) : (
                                                                    <Icons.FaGlobe className="h-4 w-4" />
                                                                )}
                                                            </div>
                                                            <div>
                                                                <p className="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {
                                                                        link.displayName
                                                                    }
                                                                </p>
                                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                    {link.url}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    )}
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>

                        {/* 情報メッセージ */}
                        <div className="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <h5 className="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">
                                リンクについて
                            </h5>
                            <ul className="space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                <li>
                                    • 最大{MAX_LINKS}個のリンクを設定できます
                                </li>
                                <li>
                                    •
                                    URLはhttp://またはhttps://で始まる必要があります
                                </li>
                                <li>
                                    • 同じボタンタイプは重複して使用できません
                                </li>
                                <li>• プロフィールページに表示されます</li>
                            </ul>
                        </div>

                        {/* アクションボタン */}
                        <div className="flex gap-3">
                            <button
                                onClick={addLink}
                                disabled={
                                    links.length >= MAX_LINKS || isLoading
                                }
                                className="flex items-center gap-2 rounded-lg border border-blue-600 bg-white px-4 py-3 text-sm font-medium text-blue-600 transition-all hover:bg-blue-50 disabled:opacity-50 dark:bg-gray-800 dark:hover:bg-blue-900/20"
                            >
                                <Icons.FaPlus className="h-4 w-4" />
                                リンク追加
                            </button>

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
