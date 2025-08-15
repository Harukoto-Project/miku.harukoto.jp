'use client';

import { useState, useEffect, useCallback } from 'react';
import {
    FaPalette,
    FaUndo,
    FaSave,
    FaEye,
    FaTimes,
    FaPlus,
    FaMinus,
    FaGripVertical,
} from 'react-icons/fa';
import { MdLocationPin } from 'react-icons/md';
import { Skeleton } from '@/components/ui/skeleton';

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

const DEFAULT_THEME: Required<Record<keyof Theme, IColorConfig>> = {
    backgroundColor: { type: 'solid', solid: '#8686f0' },
    displayNameColor: { type: 'solid', solid: '#ffffff' },
    pronounsColor: { type: 'solid', solid: '#e2e8f0' },
    locationColor: { type: 'solid', solid: '#f1f5f9' },
    bioTextColor: { type: 'solid', solid: '#ffffff' },
    bioBackgroundColor: { type: 'solid', solid: 'rgba(255, 255, 255, 0.1)' },
};

const PRESET_THEMES: {
    name: string;
    theme: Required<Record<keyof Theme, IColorConfig>>;
}[] = [
    {
        name: 'デフォルト',
        theme: DEFAULT_THEME,
    },
    {
        name: 'ダーク',
        theme: {
            backgroundColor: { type: 'solid', solid: '#1f2937' },
            displayNameColor: { type: 'solid', solid: '#f9fafb' },
            pronounsColor: { type: 'solid', solid: '#d1d5db' },
            locationColor: { type: 'solid', solid: '#9ca3af' },
            bioTextColor: { type: 'solid', solid: '#f3f4f6' },
            bioBackgroundColor: { type: 'solid', solid: 'rgba(0, 0, 0, 0.3)' },
        },
    },
    {
        name: 'オーシャン',
        theme: {
            backgroundColor: {
                type: 'gradient',
                gradient: {
                    stops: [
                        { color: '#0ea5e9', position: 0 },
                        { color: '#06b6d4', position: 100 },
                    ],
                    direction: 135,
                },
            },
            displayNameColor: { type: 'solid', solid: '#ffffff' },
            pronounsColor: { type: 'solid', solid: '#e0f2fe' },
            locationColor: { type: 'solid', solid: '#bae6fd' },
            bioTextColor: { type: 'solid', solid: '#ffffff' },
            bioBackgroundColor: {
                type: 'solid',
                solid: 'rgba(255, 255, 255, 0.2)',
            },
        },
    },
    {
        name: 'サンセット',
        theme: {
            backgroundColor: {
                type: 'gradient',
                gradient: {
                    stops: [
                        { color: '#f97316', position: 0 },
                        { color: '#dc2626', position: 50 },
                        { color: '#9333ea', position: 100 },
                    ],
                    direction: 45,
                },
            },
            displayNameColor: { type: 'solid', solid: '#ffffff' },
            pronounsColor: { type: 'solid', solid: '#fed7aa' },
            locationColor: { type: 'solid', solid: '#fdba74' },
            bioTextColor: { type: 'solid', solid: '#ffffff' },
            bioBackgroundColor: {
                type: 'solid',
                solid: 'rgba(255, 255, 255, 0.2)',
            },
        },
    },
];

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

const ColorEditor = ({
    label,
    value,
    onChange,
    disabled,
}: {
    label: string;
    value: IColorConfig;
    onChange: (config: IColorConfig) => void;
    disabled?: boolean;
}) => {
    const [isGradientEnabled, setIsGradientEnabled] = useState(
        value.type === 'gradient',
    );

    const handleTypeChange = (enabled: boolean) => {
        setIsGradientEnabled(enabled);
        if (enabled) {
            onChange({
                type: 'gradient',
                gradient: {
                    stops: [
                        { color: value.solid || '#000000', position: 0 },
                        { color: '#ffffff', position: 100 },
                    ],
                    direction: 90,
                },
            });
        } else {
            onChange({
                type: 'solid',
                solid:
                    value.gradient?.stops[0]?.color || value.solid || '#000000',
            });
        }
    };

    const handleSolidColorChange = (color: string) => {
        onChange({ ...value, solid: color });
    };

    const handleGradientStopChange = (index: number, color: string) => {
        if (!value.gradient) return;
        const newStops = [...value.gradient.stops];
        newStops[index] = { ...newStops[index], color };
        onChange({
            ...value,
            gradient: { ...value.gradient, stops: newStops },
        });
    };

    const handleGradientPositionChange = (index: number, position: number) => {
        if (!value.gradient) return;
        const newStops = [...value.gradient.stops];
        newStops[index] = { ...newStops[index], position };
        onChange({
            ...value,
            gradient: { ...value.gradient, stops: newStops },
        });
    };

    const handleDirectionChange = (direction: number) => {
        if (!value.gradient) return;
        onChange({
            ...value,
            gradient: { ...value.gradient, direction },
        });
    };

    const addGradientStop = () => {
        if (!value.gradient || value.gradient.stops.length >= 5) return;
        const stops = [...value.gradient.stops].sort(
            (a, b) => a.position - b.position,
        );
        const newPosition =
            stops.length > 1
                ? Math.round(
                      (stops[stops.length - 2].position +
                          stops[stops.length - 1].position) /
                          2,
                  )
                : 50;

        const newStops = [
            ...stops,
            { color: '#ffffff', position: newPosition },
        ].sort((a, b) => a.position - b.position);

        onChange({
            ...value,
            gradient: { ...value.gradient, stops: newStops },
        });
    };

    const removeGradientStop = (index: number) => {
        if (!value.gradient || value.gradient.stops.length <= 2) return;
        const newStops = value.gradient.stops.filter((_, i) => i !== index);
        onChange({
            ...value,
            gradient: { ...value.gradient, stops: newStops },
        });
    };

    return (
        <div className="space-y-3">
            <div className="flex items-center justify-between">
                <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {label}
                </label>
                <div className="flex items-center gap-2">
                    <span className="text-xs text-gray-500">
                        グラデーション
                    </span>
                    <button
                        type="button"
                        onClick={() => handleTypeChange(!isGradientEnabled)}
                        disabled={disabled}
                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors ${
                            isGradientEnabled
                                ? 'bg-blue-600'
                                : 'bg-gray-300 dark:bg-gray-600'
                        } ${disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'}`}
                    >
                        <span
                            className={`inline-block h-3 w-3 transform rounded-full bg-white transition-transform ${
                                isGradientEnabled
                                    ? 'translate-x-5'
                                    : 'translate-x-1'
                            }`}
                        />
                    </button>
                </div>
            </div>

            {/* プレビュー */}
            <div
                className="h-8 w-full rounded border border-gray-300 dark:border-gray-600"
                style={{ background: generateCSSColor(value) }}
            />

            {isGradientEnabled && value.gradient ? (
                <div className="space-y-3">
                    {/* 方向設定 */}
                    <div>
                        <label className="mb-1 block text-xs text-gray-600 dark:text-gray-400">
                            方向: {value.gradient.direction}°
                        </label>
                        <input
                            type="range"
                            min="0"
                            max="360"
                            value={value.gradient.direction}
                            onChange={(e) =>
                                handleDirectionChange(Number(e.target.value))
                            }
                            disabled={disabled}
                            className="w-full"
                        />
                    </div>

                    {/* グラデーションストップ */}
                    <div className="space-y-2">
                        <div className="flex items-center justify-between">
                            <span className="text-xs text-gray-600 dark:text-gray-400">
                                カラーストップ ({value.gradient.stops.length}/5)
                            </span>
                            {value.gradient.stops.length < 5 && (
                                <button
                                    type="button"
                                    onClick={addGradientStop}
                                    disabled={disabled}
                                    className="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 disabled:opacity-50"
                                >
                                    <FaPlus className="h-3 w-3" />
                                    追加
                                </button>
                            )}
                        </div>

                        {value.gradient.stops
                            .sort((a, b) => a.position - b.position)
                            .map((stop, index) => (
                                <div
                                    key={index}
                                    className="flex items-center gap-2"
                                >
                                    <FaGripVertical className="h-3 w-3 text-gray-400" />
                                    <input
                                        type="color"
                                        value={stop.color}
                                        onChange={(e) =>
                                            handleGradientStopChange(
                                                index,
                                                e.target.value,
                                            )
                                        }
                                        disabled={disabled}
                                        className="h-8 w-12 cursor-pointer rounded border border-gray-300 disabled:opacity-50"
                                    />
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={stop.position}
                                        onChange={(e) =>
                                            handleGradientPositionChange(
                                                index,
                                                Number(e.target.value),
                                            )
                                        }
                                        disabled={disabled}
                                        className="w-16 rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700"
                                    />
                                    <span className="text-xs text-gray-500">
                                        %
                                    </span>
                                    {value.gradient &&
                                        value.gradient.stops.length > 2 && (
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    removeGradientStop(index)
                                                }
                                                disabled={disabled}
                                                className="text-red-500 hover:text-red-700 disabled:opacity-50"
                                            >
                                                <FaMinus className="h-3 w-3" />
                                            </button>
                                        )}
                                </div>
                            ))}
                    </div>
                </div>
            ) : (
                <input
                    type="color"
                    value={value.solid || '#000000'}
                    onChange={(e) => handleSolidColorChange(e.target.value)}
                    disabled={disabled}
                    className="h-10 w-full cursor-pointer rounded-lg border border-gray-300 disabled:opacity-50 dark:border-gray-600"
                />
            )}
        </div>
    );
};

export function ThemeEditor() {
    const [theme, setTheme] =
        useState<Required<Record<keyof Theme, IColorConfig>>>(DEFAULT_THEME);
    const [originalTheme, setOriginalTheme] =
        useState<Required<Record<keyof Theme, IColorConfig>>>(DEFAULT_THEME);
    const [isExpanded, setIsExpanded] = useState(false);
    const [previewMode, setPreviewMode] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [saveMessage, setSaveMessage] = useState<{
        type: 'success' | 'error';
        text: string;
    } | null>(null);

    const normalizeTheme = useCallback(
        (profileTheme?: Theme): Required<Record<keyof Theme, IColorConfig>> => {
            return {
                backgroundColor: normalizeColorConfig(
                    profileTheme?.backgroundColor,
                ),
                displayNameColor: normalizeColorConfig(
                    profileTheme?.displayNameColor,
                ),
                pronounsColor: normalizeColorConfig(
                    profileTheme?.pronounsColor,
                ),
                locationColor: normalizeColorConfig(
                    profileTheme?.locationColor,
                ),
                bioTextColor: normalizeColorConfig(profileTheme?.bioTextColor),
                bioBackgroundColor: normalizeColorConfig(
                    profileTheme?.bioBackgroundColor,
                ),
            };
        },
        [],
    );

    // プロフィールデータを取得
    useEffect(() => {
        const fetchProfile = async () => {
            setIsLoading(true);
            try {
                const response = await fetch('/api/profile');
                if (response.ok) {
                    const profile = await response.json();
                    const normalizedTheme = normalizeTheme(profile.theme);
                    setTheme(normalizedTheme);
                    setOriginalTheme(normalizedTheme);
                } else {
                    console.error('Failed to fetch profile:', response.status);
                }
            } catch (error) {
                console.error('プロフィールの取得に失敗しました:', error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchProfile();
    }, [normalizeTheme]);

    const handleColorChange = (key: keyof Theme, value: IColorConfig) => {
        setTheme((prev) => ({ ...prev, [key]: value }));
    };

    const handlePresetSelect = (
        presetTheme: Required<Record<keyof Theme, IColorConfig>>,
    ) => {
        setTheme(presetTheme);
    };

    const handleReset = () => {
        setTheme(DEFAULT_THEME);
    };

    const handleRevert = () => {
        setTheme(originalTheme);
    };

    const handleSave = async () => {
        setIsSaving(true);
        setSaveMessage(null);

        try {
            const updateData = {
                theme: theme,
            };

            const updateResponse = await fetch('/api/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(updateData),
            });

            if (!updateResponse.ok) {
                const errorData = await updateResponse.json();
                throw new Error(
                    errorData.error || 'テーマの保存に失敗しました',
                );
            }

            setOriginalTheme(theme);
            setSaveMessage({ type: 'success', text: 'テーマを保存しました！' });

            setTimeout(() => setSaveMessage(null), 3000);
        } catch (error) {
            console.error('テーマの保存に失敗しました:', error);
            setSaveMessage({
                type: 'error',
                text:
                    error instanceof Error
                        ? error.message
                        : 'テーマの保存に失敗しました',
            });
        } finally {
            setIsSaving(false);
        }
    };

    const hasChanges = JSON.stringify(theme) !== JSON.stringify(originalTheme);

    if (isLoading) {
        return (
            <div className="rounded-lg bg-white p-4 shadow-lg dark:border-gray-600 dark:bg-dark-primary dark:text-gray-300">
                <div className="space-y-4">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <Skeleton height="6" width="6" />
                            <Skeleton height="6" width="32" />
                        </div>
                        <Skeleton height="8" width="20" />
                    </div>
                    <div className="space-y-3">
                        <Skeleton height="4" width="full" />
                        <Skeleton height="8" width="full" />
                        <Skeleton height="10" width="full" />
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="rounded-lg bg-white p-6 shadow-lg dark:border-gray-600 dark:bg-dark-primary dark:text-gray-300">
            {/* ヘッダー */}
            <div className="mb-6 flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <FaPalette className="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    <h2 className="text-xl font-bold text-gray-900 dark:text-white">
                        テーマ設定
                    </h2>
                </div>
                <div className="flex items-center gap-2">
                    <button
                        onClick={() => setPreviewMode(!previewMode)}
                        className="flex items-center gap-2 rounded-lg bg-blue-100 px-3 py-2 text-sm font-medium text-blue-700 transition-colors hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800"
                    >
                        <FaEye className="h-4 w-4" />
                        プレビュー
                    </button>
                    <button
                        onClick={() => setIsExpanded(!isExpanded)}
                        className="rounded-lg bg-gray-100 p-2 text-gray-600 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600"
                    >
                        {isExpanded ? (
                            <FaTimes className="h-4 w-4" />
                        ) : (
                            <FaPalette className="h-4 w-4" />
                        )}
                    </button>
                </div>
            </div>

            {/* プリセットテーマ */}
            <div className="mb-6">
                <h3 className="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                    プリセットテーマ
                </h3>
                <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    {PRESET_THEMES.map((preset, index) => (
                        <button
                            key={index}
                            onClick={() => handlePresetSelect(preset.theme)}
                            className="group relative overflow-hidden rounded-lg p-3 text-left transition-all hover:scale-105"
                            style={{
                                background: generateCSSColor(
                                    preset.theme.backgroundColor,
                                ),
                            }}
                        >
                            <div className="relative z-10">
                                <div className="text-xs font-medium text-white">
                                    {preset.name}
                                </div>
                                <div className="mt-1 flex gap-1">
                                    {Object.values(preset.theme)
                                        .slice(0, 3)
                                        .map((color, colorIndex) => (
                                            <div
                                                key={colorIndex}
                                                className="h-3 w-3 rounded-full border border-white/30"
                                                style={{
                                                    background:
                                                        generateCSSColor(color),
                                                }}
                                            />
                                        ))}
                                </div>
                            </div>
                        </button>
                    ))}
                </div>
            </div>

            {/* カラー設定 */}
            {isExpanded && (
                <div className="space-y-6">
                    <div className="grid gap-6 md:grid-cols-2">
                        <ColorEditor
                            label="背景色"
                            value={theme.backgroundColor}
                            onChange={(value) =>
                                handleColorChange('backgroundColor', value)
                            }
                            disabled={isSaving}
                        />
                        <ColorEditor
                            label="表示名の色"
                            value={theme.displayNameColor}
                            onChange={(value) =>
                                handleColorChange('displayNameColor', value)
                            }
                            disabled={isSaving}
                        />
                        <ColorEditor
                            label="代名詞の色"
                            value={theme.pronounsColor}
                            onChange={(value) =>
                                handleColorChange('pronounsColor', value)
                            }
                            disabled={isSaving}
                        />
                        <ColorEditor
                            label="場所の色"
                            value={theme.locationColor}
                            onChange={(value) =>
                                handleColorChange('locationColor', value)
                            }
                            disabled={isSaving}
                        />
                        <ColorEditor
                            label="自己紹介文の色"
                            value={theme.bioTextColor}
                            onChange={(value) =>
                                handleColorChange('bioTextColor', value)
                            }
                            disabled={isSaving}
                        />
                        <ColorEditor
                            label="自己紹介背景色"
                            value={theme.bioBackgroundColor}
                            onChange={(value) =>
                                handleColorChange('bioBackgroundColor', value)
                            }
                            disabled={isSaving}
                        />
                    </div>
                </div>
            )}

            {/* プレビュー */}
            {previewMode && (
                <div className="mt-6">
                    <h3 className="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        プレビュー
                    </h3>
                    <div
                        className="rounded-lg p-6 text-center"
                        style={{
                            background: generateCSSColor(theme.backgroundColor),
                        }}
                    >
                        <div className="mx-auto h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                        <h1
                            className="mt-3 text-2xl font-bold"
                            style={{
                                color: generateCSSColor(theme.displayNameColor),
                            }}
                        >
                            プレビュー名
                        </h1>
                        <h2
                            className="mt-1 text-lg"
                            style={{
                                color: generateCSSColor(theme.pronounsColor),
                            }}
                        >
                            They/Them
                        </h2>
                        <div
                            className="mt-2 flex items-center justify-center gap-1"
                            style={{
                                color: generateCSSColor(theme.locationColor),
                            }}
                        >
                            <MdLocationPin className="h-4 w-4" />
                            <span>Tokyo, Japan</span>
                        </div>
                        <div
                            className="mt-4 rounded-lg p-3"
                            style={{
                                background: generateCSSColor(
                                    theme.bioBackgroundColor,
                                ),
                            }}
                        >
                            <p
                                className="text-sm"
                                style={{
                                    color: generateCSSColor(theme.bioTextColor),
                                }}
                            >
                                これは自己紹介文のプレビューです。テーマの色がどのように表示されるかを確認できます。
                            </p>
                        </div>
                    </div>
                </div>
            )}

            {/* アクションボタン */}
            <div className="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-4 dark:border-gray-600">
                <div className="flex gap-2">
                    <button
                        onClick={handleReset}
                        disabled={isSaving}
                        className="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        <FaUndo className="h-4 w-4" />
                        デフォルトに戻す
                    </button>
                    {hasChanges && (
                        <button
                            onClick={handleRevert}
                            disabled={isSaving}
                            className="flex items-center gap-2 rounded-lg bg-yellow-100 px-3 py-2 text-sm font-medium text-yellow-700 transition-colors hover:bg-yellow-200 disabled:opacity-50 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800"
                        >
                            <FaUndo className="h-4 w-4" />
                            変更を破棄
                        </button>
                    )}
                </div>

                <div className="flex items-center gap-3">
                    {saveMessage && (
                        <span
                            className={`text-sm ${
                                saveMessage.type === 'success'
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-red-600 dark:text-red-400'
                            }`}
                        >
                            {saveMessage.text}
                        </span>
                    )}
                    <button
                        onClick={handleSave}
                        disabled={!hasChanges || isSaving}
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <FaSave className="h-4 w-4" />
                        {isSaving ? '保存中...' : '保存する'}
                    </button>
                </div>
            </div>
        </div>
    );
}
