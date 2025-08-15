'use client';

import { useState, useEffect, useCallback } from 'react';
import Image from 'next/image';
import { useParams, useSearchParams } from 'next/navigation';
import { MdLocationPin } from 'react-icons/md';
import Loading from '@/components/ui/Loading';
import * as Icons from 'react-icons/fa';
import { IconType } from 'react-icons';
import { Header } from '@/components/ui/Header/Header';
import { Footer } from '@/components/ui/Footer';

interface Link {
    buttonId: string;
    url: string;
    displayName: string;
    customColor?: string;
}

interface IGradientStop {
    color: string;
    position: number;
}

interface IColorConfig {
    type: 'solid' | 'gradient';
    solid?: string;
    gradient?: {
        stops: IGradientStop[];
        direction: number;
    };
}

interface Theme {
    backgroundColor?: IColorConfig | string;
    displayNameColor?: IColorConfig | string;
    pronounsColor?: IColorConfig | string;
    locationColor?: IColorConfig | string;
    bioTextColor?: IColorConfig | string;
    bioBackgroundColor?: IColorConfig | string;
}

interface Profile {
    userId: string;
    displayName?: string;
    bio?: string;
    pronouns?: string;
    location?: string;
    avatarUrl?: string;
    links?: Link[];
    createdAt?: string;
    updatedAt?: string;
    theme?: Theme;
}

interface ButtonType {
    id: string;
    label: string;
    icon: IconType;
    defaultColor: string;
}

const BUTTON_TYPES: ButtonType[] = [
    {
        id: 'website',
        label: 'ウェブサイト',
        icon: Icons.FaGlobe,
        defaultColor: '#2563eb',
    },
    {
        id: 'twitter',
        label: 'Twitter',
        icon: Icons.FaTwitter,
        defaultColor: '#0ea5e9',
    },
    {
        id: 'github',
        label: 'GitHub',
        icon: Icons.FaGithub,
        defaultColor: '#1f2937',
    },
    {
        id: 'youtube',
        label: 'YouTube',
        icon: Icons.FaYoutube,
        defaultColor: '#dc2626',
    },
    {
        id: 'instagram',
        label: 'Instagram',
        icon: Icons.FaInstagram,
        defaultColor: '#db2777',
    },
    {
        id: 'discord',
        label: 'Discord',
        icon: Icons.FaDiscord,
        defaultColor: '#4f46e5',
    },
    {
        id: 'twitch',
        label: 'Twitch',
        icon: Icons.FaTwitch,
        defaultColor: '#7c3aed',
    },
    {
        id: 'linkedin',
        label: 'LinkedIn',
        icon: Icons.FaLinkedin,
        defaultColor: '#1d4ed8',
    },
    {
        id: 'facebook',
        label: 'Facebook',
        icon: Icons.FaFacebook,
        defaultColor: '#1e40af',
    },
    {
        id: 'tiktok',
        label: 'TikTok',
        icon: Icons.FaTiktok,
        defaultColor: '#000000',
    },
    {
        id: 'steam',
        label: 'Steam',
        icon: Icons.FaSteam,
        defaultColor: '#374151',
    },
    {
        id: 'spotify',
        label: 'Spotify',
        icon: Icons.FaSpotify,
        defaultColor: '#16a34a',
    },
    {
        id: 'paypal',
        label: 'PayPal',
        icon: Icons.FaPaypal,
        defaultColor: '#2563eb',
    },
    {
        id: 'amazon',
        label: 'Amazon',
        icon: Icons.FaAmazon,
        defaultColor: '#f97316',
    },
    {
        id: 'email',
        label: 'メール',
        icon: Icons.FaEnvelope,
        defaultColor: '#4b5563',
    },
];

const DEFAULT_THEME: Required<Record<keyof Theme, IColorConfig>> = {
    backgroundColor: { type: 'solid', solid: '#8686f0' },
    displayNameColor: { type: 'solid', solid: '#ffffff' },
    pronounsColor: { type: 'solid', solid: '#e2e8f0' },
    locationColor: { type: 'solid', solid: '#f1f5f9' },
    bioTextColor: { type: 'solid', solid: '#ffffff' },
    bioBackgroundColor: { type: 'solid', solid: 'rgba(255, 255, 255, 0.1)' },
};

// ユーティリティ関数
const normalizeColorConfig = (value?: IColorConfig | string): IColorConfig => {
    if (!value) return { type: 'solid', solid: '#000000' };
    if (typeof value === 'string') {
        return { type: 'solid', solid: value };
    }
    return value;
};

const generateCSSColor = (config: IColorConfig): string => {
    if (config.type === 'solid' && config.solid) {
        return config.solid;
    }
    if (config.type === 'gradient' && config.gradient) {
        const { stops, direction } = config.gradient;
        const sortedStops = [...stops].sort((a, b) => a.position - b.position);
        const stopStrings = sortedStops.map(
            (stop) => `${stop.color} ${stop.position}%`,
        );
        return `linear-gradient(${direction}deg, ${stopStrings.join(', ')})`;
    }
    return '#000000';
};

export default function ProfilePage() {
    const params = useParams();
    const searchParams = useSearchParams();
    const userId = params.userId as string;
    const [profile, setProfile] = useState<Profile | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const getColorFromParams = useCallback(
        (key: string, defaultValue: string): string => {
            const paramValue = searchParams.get(key);
            if (!paramValue) return defaultValue;
            return paramValue.startsWith('#') ? paramValue : `#${paramValue}`;
        },
        [searchParams],
    );

    const resolveTheme = useCallback(
        (profileTheme?: Theme): Required<Record<keyof Theme, IColorConfig>> => {
            return {
                backgroundColor: normalizeColorConfig(
                    profileTheme?.backgroundColor || {
                        type: 'solid',
                        solid: getColorFromParams(
                            'bg',
                            DEFAULT_THEME.backgroundColor.solid!,
                        ),
                    },
                ),
                displayNameColor: normalizeColorConfig(
                    profileTheme?.displayNameColor || {
                        type: 'solid',
                        solid: getColorFromParams(
                            'name',
                            DEFAULT_THEME.displayNameColor.solid!,
                        ),
                    },
                ),
                pronounsColor: normalizeColorConfig(
                    profileTheme?.pronounsColor || {
                        type: 'solid',
                        solid: getColorFromParams(
                            'pronouns',
                            DEFAULT_THEME.pronounsColor.solid!,
                        ),
                    },
                ),
                locationColor: normalizeColorConfig(
                    profileTheme?.locationColor || {
                        type: 'solid',
                        solid: getColorFromParams(
                            'location',
                            DEFAULT_THEME.locationColor.solid!,
                        ),
                    },
                ),
                bioTextColor: normalizeColorConfig(
                    profileTheme?.bioTextColor || {
                        type: 'solid',
                        solid: getColorFromParams(
                            'bioText',
                            DEFAULT_THEME.bioTextColor.solid!,
                        ),
                    },
                ),
                bioBackgroundColor: normalizeColorConfig(
                    profileTheme?.bioBackgroundColor || {
                        type: 'solid',
                        solid: getColorFromParams(
                            'bioBg',
                            DEFAULT_THEME.bioBackgroundColor.solid!,
                        ),
                    },
                ),
            };
        },
        [getColorFromParams],
    );

    const [theme, setTheme] =
        useState<Required<Record<keyof Theme, IColorConfig>>>(DEFAULT_THEME);

    useEffect(() => {
        const fetchProfile = async () => {
            if (!userId) {
                setError('ユーザーIDが指定されていません');
                setIsLoading(false);
                return;
            }

            try {
                const response = await fetch(`/api/profile/${userId}`);

                if (!response.ok) {
                    if (response.status === 404) {
                        setError('プロフィールが見つかりません');
                    } else {
                        setError('プロフィールの取得に失敗しました');
                    }
                    return;
                }

                const profileData: Profile = await response.json();
                setProfile(profileData);

                const resolvedTheme = resolveTheme(profileData.theme);
                setTheme(resolvedTheme);
            } catch (error) {
                console.error('Profile fetch error:', error);
                setError('プロフィールの取得中にエラーが発生しました');
            } finally {
                setIsLoading(false);
            }
        };

        fetchProfile();
    }, [userId, resolveTheme]);

    useEffect(() => {
        if (profile) {
            const resolvedTheme = resolveTheme(profile.theme);
            setTheme(resolvedTheme);
        }
    }, [profile, resolveTheme]);

    const getButtonInfo = (buttonId: string): ButtonType => {
        return (
            BUTTON_TYPES.find((button) => button.id === buttonId) ||
            BUTTON_TYPES[0]
        );
    };

    const handleLinkClick = (url: string): void => {
        const fullUrl = url.startsWith('http') ? url : `https://${url}`;
        window.open(fullUrl, '_blank', 'noopener,noreferrer');
    };

    if (isLoading) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
                <Loading />
            </div>
        );
    }

    if (error || !profile) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
                <div className="rounded-xl bg-white/80 p-8 text-center shadow-lg backdrop-blur-md dark:bg-gray-900/80">
                    <Icons.FaExclamationTriangle className="mx-auto mb-4 h-16 w-16 text-red-500" />
                    <h1 className="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                        エラーが発生しました
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400">
                        {error || 'プロフィールが見つかりません'}
                    </p>
                </div>
            </div>
        );
    }

    return (
        <>
            <Header />
            <div
                className="flex min-h-screen items-center justify-center p-4 transition-colors duration-300"
                style={{
                    background: `linear-gradient(135deg, ${generateCSSColor(theme.backgroundColor)}20, ${generateCSSColor(theme.backgroundColor)}10)`,
                }}
            >
                <div
                    className="hover:shadow-3xl flex w-full max-w-[40rem] flex-col justify-center rounded-xl p-6 pt-8 text-center shadow-2xl backdrop-blur-sm transition-all duration-500"
                    style={{
                        background: generateCSSColor(theme.backgroundColor),
                        boxShadow: `0 25px 50px -12px ${generateCSSColor(theme.backgroundColor)}40`,
                    }}
                >
                    <div className="flex flex-col items-center justify-center">
                        {/* アバター */}
                        <div className="relative h-32 w-32 overflow-hidden rounded-full border-4 border-white shadow-lg transition-transform duration-300 hover:scale-105">
                            {profile.avatarUrl ? (
                                <Image
                                    src={profile.avatarUrl}
                                    alt={`${profile.displayName || 'ユーザー'}のアバター`}
                                    fill
                                    sizes="128px"
                                    className="object-cover transition-transform duration-300 hover:scale-110"
                                    priority
                                />
                            ) : (
                                <div className="flex h-full w-full items-center justify-center bg-gray-300">
                                    <Icons.FaUser className="h-16 w-16 text-gray-600" />
                                </div>
                            )}
                        </div>

                        {/* 表示名 */}
                        {profile.displayName && (
                            <h1
                                className="pt-4 text-4xl font-bold transition-all duration-300 hover:scale-105"
                                style={{
                                    color: generateCSSColor(
                                        theme.displayNameColor,
                                    ),
                                    textShadow: `0 2px 4px ${generateCSSColor(theme.displayNameColor)}30`,
                                }}
                            >
                                {profile.displayName}
                            </h1>
                        )}

                        {/* 代名詞 */}
                        {profile.pronouns && (
                            <h2
                                className="mt-2 text-xl font-medium transition-colors duration-300"
                                style={{
                                    color: generateCSSColor(
                                        theme.pronounsColor,
                                    ),
                                }}
                            >
                                {profile.pronouns}
                            </h2>
                        )}

                        {/* 場所 */}
                        {profile.location && (
                            <div
                                className="mt-2 flex items-center justify-center gap-1 text-lg transition-colors duration-300"
                                style={{
                                    color: generateCSSColor(
                                        theme.locationColor,
                                    ),
                                }}
                            >
                                <MdLocationPin className="h-5 w-5" />
                                <span>{profile.location}</span>
                            </div>
                        )}

                        {/* 自己紹介 */}
                        {profile.bio && (
                            <div
                                className="mt-4 rounded-lg p-4 backdrop-blur-sm transition-all duration-300 hover:backdrop-blur-md"
                                style={{
                                    background: generateCSSColor(
                                        theme.bioBackgroundColor,
                                    ),
                                    border: `1px solid ${generateCSSColor(theme.bioTextColor)}20`,
                                    boxShadow: `0 4px 6px ${generateCSSColor(theme.bioBackgroundColor)}20`,
                                }}
                            >
                                <p
                                    className="whitespace-pre-wrap text-sm leading-relaxed transition-colors duration-300"
                                    style={{
                                        color: generateCSSColor(
                                            theme.bioTextColor,
                                        ),
                                    }}
                                >
                                    {profile.bio}
                                </p>
                            </div>
                        )}
                    </div>

                    {/* リンク */}
                    {profile.links && profile.links.length > 0 && (
                        <div className="mt-6">
                            <div className="grid gap-3 sm:grid-cols-2">
                                {profile.links.map((link, index) => {
                                    const buttonInfo = getButtonInfo(
                                        link.buttonId,
                                    );
                                    const IconComponent = buttonInfo.icon;
                                    const buttonColor =
                                        link.customColor ||
                                        buttonInfo.defaultColor;

                                    return (
                                        <button
                                            key={index}
                                            onClick={() =>
                                                handleLinkClick(link.url)
                                            }
                                            className="group flex items-center gap-3 rounded-lg p-4 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg active:scale-95"
                                            style={{
                                                backgroundColor: buttonColor,
                                                boxShadow: `0 4px 14px 0 ${buttonColor}40`,
                                            }}
                                            onMouseEnter={(e) => {
                                                e.currentTarget.style.boxShadow = `0 8px 25px 0 ${buttonColor}60`;
                                            }}
                                            onMouseLeave={(e) => {
                                                e.currentTarget.style.boxShadow = `0 4px 14px 0 ${buttonColor}40`;
                                            }}
                                        >
                                            <IconComponent className="h-6 w-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" />
                                            <div className="flex-1 text-left">
                                                <div className="font-medium transition-transform duration-300 group-hover:translate-x-1">
                                                    {link.displayName}
                                                </div>
                                                <div className="text-sm opacity-75">
                                                    {buttonInfo.label}
                                                </div>
                                            </div>
                                            <Icons.FaExternalLinkAlt className="h-4 w-4 opacity-75 transition-all duration-300 group-hover:translate-x-1 group-hover:opacity-100" />
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* 空の状態 */}
                    {(!profile.links || profile.links.length === 0) &&
                        !profile.bio && (
                            <div className="py-8 text-center">
                                <Icons.FaInfoCircle
                                    className="mx-auto mb-4 h-12 w-12 opacity-50 transition-opacity duration-300 hover:opacity-70"
                                    style={{
                                        color: generateCSSColor(
                                            theme.displayNameColor,
                                        ),
                                    }}
                                />
                                <p
                                    className="text-sm opacity-75 transition-opacity duration-300"
                                    style={{
                                        color: generateCSSColor(
                                            theme.displayNameColor,
                                        ),
                                    }}
                                >
                                    このユーザーはまだプロフィール情報を設定していません
                                </p>
                            </div>
                        )}
                </div>
            </div>
            <Footer />
        </>
    );
}
