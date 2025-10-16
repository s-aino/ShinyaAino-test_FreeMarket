@extends('layouts.app')

@section('title', '商品の出品')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showSell' => true,
])
@endsection

@section('content')
<div class="container">
    <h1>商品の出品</h1>

    {{-- エラーメッセージ --}}
    @if ($errors->any())
    <div class="error-box">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ★ フォーム開始ここに移動 ★ --}}
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-group">
            <label for="image">商品画像</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        {{-- 商品詳細 --}}
        <h2>カテゴリー</h2>
        <div class="category-group">
            @php
            $categories = [
            'ファッション', '美容', 'インテリア', 'レディース', 'メンズ', 'コスメ',
            '本', 'ゲーム', 'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー',
            'おもちゃ', 'ベビー・キッズ'
            ];
            @endphp

            @foreach($categories as $category)
            <label class="category-chip">
                <input type="checkbox" name="categories[]" value="{{ $category }}">
                <span>{{ $category }}</span>
            </label>
            @endforeach
        </div>
        {{-- 商品の状態 --}}
        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition">
                <option value="">--選択してください--</option>
                <option value="新品・未使用" {{ old('condition') == '新品・未使用' ? 'selected' : '' }}>新品・未使用</option>
                <option value="未使用に近い" {{ old('condition') == '未使用に近い' ? 'selected' : '' }}>未使用に近い</option>
                <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="全体的に状態が悪い" {{ old('condition') == '全体的に状態が悪い' ? 'selected' : '' }}>全体的に状態が悪い</option>
            </select>
        </div>

        {{-- 商品名 --}}
        <div class="form-group">
            <label for="title">商品名</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}">
        </div>

        {{-- ブランド名 --}}
        <div class="form-group">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
        </div>

        {{-- 商品説明 --}}
        <div class="form-group">
            <label for="description">商品説明</label>
            <textarea name="description" id="description" rows="5">{{ old('description') }}</textarea>
        </div>

        {{-- 価格 --}}
        <div class="form-group">
            <label for="price">販売価格（円）</label>
            <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="0">
        </div>

        {{-- 出品ボタン --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">出品する</button>
        </div>
    </form>
    {{-- ★ フォームここまで ★ --}}
</div>
@endsection