@extends('layouts.app')

@php
$isAuth = ($mode ?? 'guest') === 'auth';
$tab = request('tab', 'recommend');
$isRecommend = $tab === 'recommend';
@endphp

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogin' => !$isAuth,
'showRegister' => !$isAuth,
'showLogout' => $isAuth,
'showMypage' => true,
'showSell' => true,
])
@endsection

@section('title', $isAuth ? '商品一覧（ログイン後）' : '商品一覧')

@section('content')
@include('items.partials.tabs', [
'baseRoute' => $isAuth ? 'items.home' : 'items.index'
])

@php
// いまは「おすすめ」だけリスト表示。マイリストは後日実装で空に。
$list = $isRecommend ? ($items ?? []) : [];
@endphp

<div class="items-grid">
    @forelse($list as $x)
    <a href="#" class="card">
        @if(!empty($x['img_url']))
        <img class="thumb" src="{{ $x['img_url'] }}" alt="{{ $x['name'] }}">
        @else
        <div class="thumb thumb--placeholder">商品画像</div>
        @endif
        <div class="meta">
            <div class="title">{{ $x['name'] }}</div>
            @if(!empty($x['brand'])) <div class="brand">{{ $x['brand'] }}</div> @endif
            @if(!empty($x['price'])) <div class="price">¥{{ number_format($x['price']) }}</div> @endif
        </div>
    </a>
    @empty
    <ul class="empty">
        <li>{{ $isRecommend ? 'おすすめ商品がありません。' : 'マイリストは空です。' }}</li>
    </ul>
    @endforelse
</div>
@endsection