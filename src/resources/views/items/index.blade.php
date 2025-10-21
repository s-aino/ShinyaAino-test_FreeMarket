@extends('layouts.app')

@section('title', '商品一覧')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogin' => true,
'showMypage' => true,
'showSell' => true
])
@endsection

@section('content')
<div class="container">
    @php $tab = request('tab', 'recommend'); @endphp

    {{-- タブ --}}
    <nav class="tabs">
        <a
            class="tab {{ $tab === 'recommend' ? 'is-active' : '' }}"
            href="{{ route('items.index', ['tab' => 'recommend', 'q' => request('q')]) }}"
            aria-current="{{ $tab === 'recommend' ? 'page' : null }}">おすすめ</a>

        <a
            class="tab {{ $tab === 'likes' ? 'is-active' : '' }}"
            href="{{ route('items.index', ['tab' => 'likes', 'q' => request('q')]) }}"
            aria-current="{{ $tab === 'likes' ? 'page' : null }}">マイリスト</a>
    </nav>

    {{-- 一覧 --}}
    <div class="items-wrap">
        <div class="item-grid">
            @forelse($items as $item)
            <a class="item-card" href="{{ route('items.show',$item) }}">
                <div class="thumb">
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}">
                    <!-- loading="lazy" decoding="async"> -->
                    @if($item->is_sold)
                    <span class="badge--sold">SOLD</span>
                    @endif
                </div>
                <div class="meta">
                    <div class="title">{{ $item->title }}</div>
                    @isset($item->price)
                    <div class="price">¥{{ number_format($item->price) }}</div>
                    @endisset
                </div>
            </a>
            @empty
            {{-- likesタブ未認証などで空の場合はメッセージ非表示の仕様 --}}
            @endforelse
        </div>
    </div>

    {{-- ページネーション --}}
    <div class="paginate">
        {{ $items->links() }}
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/items-index.css') }}?v={{ filemtime(public_path('css/items-index.css')) }}">
@endpush