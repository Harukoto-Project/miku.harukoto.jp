'use client';

import { useState, useEffect } from 'react';
import {
    FaLink,
    FaCopy,
    FaCheck,
    FaExternalLinkAlt,
    FaShare,
    FaQrcode,
    FaDownload,
    FaTimes,
} from 'react-icons/fa';
import { useSession } from 'next-auth/react';
import { Skeleton } from '@/components/ui/skeleton';
import QRCode from 'react-qr-code';

interface UrlEditorProps {
    className?: string;
}

export function UrlEditor({ className = '' }: UrlEditorProps) {
    const { data: session, status } = useSession();
    const [profileUrl, setProfileUrl] = useState<string>('');
    const [copiedStates, setCopiedStates] = useState<{
        [key: string]: boolean;
    }>({});
    const [isLoading, setIsLoading] = useState(true);
    const [showQRCode, setShowQRCode] = useState(false);

    useEffect(() => {
        if (status === 'loading') return;

        if (session?.user?.id) {
            const baseUrl =
                process.env.NEXT_PUBLIC_APP_URL || window.location.origin;
            const url = `${baseUrl}/profile/${session.user.id}`;
            setProfileUrl(url);
        }
        setIsLoading(false);
    }, [session, status]);

    const handleCopy = async (text: string, key: string) => {
        try {
            await navigator.clipboard.writeText(text);
            setCopiedStates((prev) => ({ ...prev, [key]: true }));

            setTimeout(() => {
                setCopiedStates((prev) => ({ ...prev, [key]: false }));
            }, 2000);
        } catch (error) {
            console.error('コピーに失敗しました:', error);

            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            setCopiedStates((prev) => ({ ...prev, [key]: true }));
            setTimeout(() => {
                setCopiedStates((prev) => ({ ...prev, [key]: false }));
            }, 2000);
        }
    };

    const handleOpenProfile = () => {
        if (profileUrl) {
            window.open(profileUrl, '_blank', 'noopener,noreferrer');
        }
    };

    const handleShare = async () => {
        if (navigator.share && profileUrl) {
            try {
                await navigator.share({
                    title: 'プロフィールページ',
                    text: 'プロフィールをチェックしてください！',
                    url: profileUrl,
                });
            } catch (error) {
                console.error('共有に失敗しました:', error);
                await handleCopy(profileUrl, 'profile');
            }
        } else {
            await handleCopy(profileUrl, 'profile');
        }
    };

    const handleDownloadQRCode = () => {
        const svg = document.getElementById(
            'qr-code-svg',
        ) as unknown as SVGSVGElement;
        if (!svg) return;

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const qrCodeSize = 256;
        const data = new XMLSerializer().serializeToString(svg);
        const DOMURL = window.URL || window.webkitURL || window;
        const img = new Image();
        const svgBlob = new Blob([data], {
            type: 'image/svg+xml;charset=utf-8',
        });
        const url = DOMURL.createObjectURL(svgBlob);

        img.onload = function () {
            canvas.width = qrCodeSize;
            canvas.height = qrCodeSize;
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
            DOMURL.revokeObjectURL(url);

            const link = document.createElement('a');
            link.download = `profile-qr-${session?.user?.id || 'code'}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        };

        img.src = url;
    };

    const handleQRCodeToggle = () => {
        setShowQRCode(!showQRCode);
    };

    if (status === 'loading' || isLoading) {
        return (
            <div
                className={`rounded-lg bg-white p-4 shadow-lg dark:border-gray-600 dark:bg-dark-primary dark:text-gray-300 ${className}`}
            >
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <Skeleton height="5" width="5" />
                        <Skeleton height="6" width="32" />
                    </div>

                    <div className="space-y-2">
                        <Skeleton height="4" width="20" />
                        <div className="flex items-center gap-2">
                            <Skeleton height="10" width="full" />
                            <Skeleton height="10" width="10" />
                        </div>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <Skeleton height="9" width="20" />
                        <Skeleton height="9" width="16" />
                        <Skeleton height="9" width="16" />
                    </div>
                </div>
            </div>
        );
    }

    if (!session?.user?.id) {
        return (
            <div
                className={`rounded-lg bg-white p-4 shadow-lg dark:border-gray-600 dark:bg-dark-primary dark:text-gray-300 ${className}`}
            >
                <div className="text-center">
                    <p className="text-gray-500 dark:text-gray-400">
                        プロフィールURLを生成するにはログインが必要です
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div
            className={`rounded-lg bg-white p-4 shadow-lg dark:border-gray-600 dark:bg-dark-primary dark:text-gray-300 ${className}`}
        >
            <div className="space-y-4">
                <h3 className="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                    <FaLink className="h-5 w-5" />
                    プロフィールURL
                </h3>

                <div className="space-y-2">
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        あなたのプロフィールページ
                    </label>
                    <div className="flex items-center gap-2">
                        <div className="flex-1 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <code className="break-all font-mono">
                                {profileUrl}
                            </code>
                        </div>
                        <button
                            onClick={() => handleCopy(profileUrl, 'profile')}
                            className={`flex items-center justify-center rounded-lg p-2 transition-colors ${
                                copiedStates.profile
                                    ? 'bg-green-500 text-white'
                                    : 'bg-blue-500 text-white hover:bg-blue-600'
                            }`}
                            title={
                                copiedStates.profile
                                    ? 'コピー完了！'
                                    : 'URLをコピー'
                            }
                        >
                            {copiedStates.profile ? (
                                <FaCheck className="h-4 w-4" />
                            ) : (
                                <FaCopy className="h-4 w-4" />
                            )}
                        </button>
                    </div>
                </div>

                <div className="flex flex-wrap gap-2">
                    <button
                        onClick={handleOpenProfile}
                        className="flex items-center gap-2 rounded-lg bg-gray-500 px-4 py-2 text-white transition-colors hover:bg-gray-600"
                    >
                        <FaExternalLinkAlt className="h-4 w-4" />
                        プレビュー
                    </button>

                    <button
                        onClick={handleShare}
                        className="flex items-center gap-2 rounded-lg bg-purple-500 px-4 py-2 text-white transition-colors hover:bg-purple-600"
                    >
                        <FaShare className="h-4 w-4" />
                        共有
                    </button>

                    <button
                        onClick={handleQRCodeToggle}
                        className="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-white transition-colors hover:bg-green-600"
                    >
                        <FaQrcode className="h-4 w-4" />
                        QRコード
                    </button>
                </div>

                {showQRCode && (
                    <div className="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-800/50">
                        <div className="flex items-center justify-between">
                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                QRコード
                            </h4>
                            <button
                                onClick={handleQRCodeToggle}
                                className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <FaTimes className="h-4 w-4" />
                            </button>
                        </div>

                        <div className="flex flex-col items-center gap-4">
                            <div className="rounded-lg bg-white p-4 shadow-sm">
                                <QRCode
                                    id="qr-code-svg"
                                    value={profileUrl}
                                    size={256}
                                    style={{
                                        height: 'auto',
                                        maxWidth: '100%',
                                        width: '100%',
                                    }}
                                    viewBox="0 0 256 256"
                                />
                            </div>

                            <button
                                onClick={handleDownloadQRCode}
                                className="flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-white transition-colors hover:bg-blue-600"
                            >
                                <FaDownload className="h-4 w-4" />
                                PNGでダウンロード
                            </button>
                        </div>

                        <div className="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                            <p className="text-sm text-blue-800 dark:text-blue-200">
                                💡
                                QRコードをスマートフォンで読み取ると、プロフィールページに直接アクセスできます！
                            </p>
                        </div>
                    </div>
                )}

                <div className="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                    <p className="text-sm text-blue-800 dark:text-blue-200">
                        💡
                        このURLを他の人と共有して、あなたの美しいプロフィールページを見てもらいましょう！
                    </p>
                </div>

                <div className="space-y-2">
                    <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300">
                        使用例
                    </h4>
                    <div className="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <div className="flex items-start gap-2">
                            <span className="text-blue-500">•</span>
                            <span>SNSのプロフィール欄に貼り付け</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <span className="text-blue-500">•</span>
                            <span>メールの署名に追加</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <span className="text-blue-500">•</span>
                            <span>名刺やポートフォリオに記載</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <span className="text-blue-500">•</span>
                            <span>Discordのステータスメッセージに設定</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <span className="text-green-500">•</span>
                            <span>QRコードを印刷して名刺に貼付</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <span className="text-green-500">•</span>
                            <span>イベントやセミナーでの自己紹介に活用</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
