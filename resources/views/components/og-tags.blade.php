@props([
    'title' => 'Miku - 多機能で便利なコミュニティ向けDiscord Bot',
    'description' => '多機能で便利なコミュニティ向けDiscord Bot',
    'image' => null,
    'type' => 'website',
    'siteName' => 'Miku'
])

@php
    $imageUrl = $image ?? asset('images/default-avatar.svg');
@endphp

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:image" content="{{ $imageUrl }}" />
@if($type === 'profile')
<meta property="og:site_name" content="{{ $siteName }}" />
@endif

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image" />
<meta property="twitter:url" content="{{ url()->current() }}" />
<meta property="twitter:title" content="{{ $title }}" />
<meta property="twitter:description" content="{{ $description }}" />
<meta property="twitter:image" content="{{ $imageUrl }}" />
