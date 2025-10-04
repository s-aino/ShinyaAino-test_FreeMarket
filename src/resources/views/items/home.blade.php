@extends('layouts.app')

@section('title', '商品一覧')

@section('header')
@include('partials.header', ['showSearch' => true, 'showLogin' => true, 'showMypage' => true, 'showSell'=> true])
@endsection

@section('content')
<div class="card">
    <h1>おすすめ</h1>

    <ul class="items-grid">
        @forelse ($items as $item)
        <li class="items-card">
            <a href="{{ route('items.show', $item) }}" class="items-link">
                <img class="items-thumb" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                <p class="items-name">{{ $item->name }}</p>
                <p class="items-price">¥{{ number_format($item->price ?? 0) }}</p>
            </a>
        </li>
        @empty
        <li>商品がありません。</li>
        @endforelse
    </ul>
</div>
@endsection