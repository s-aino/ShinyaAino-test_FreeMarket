@extends('layouts.app')

@section('title', '商品の出品')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showMypage' => true,
'showSell' => true,
])
@endsection

@section('content')
<div class="container">
    <h1 class="sell-title">商品の出品</h1>

    {{-- ★ フォーム開始ここに移動 ★ --}}
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-group image-upload">
            <label for="image" class="form-label">商品画像</label>
            <div class="image-frame">
                <input type="file" name="image" id="image" accept="image/*" hidden>
                <label for="image" class="image-button">画像を選択する</label>
                <img id="preview" src="" alt="" class="image-preview" style="display:none;">
            </div>
            @error('image')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        {{-- 商品の詳細 --}}
        <h2 class="section-title">商品の詳細</h2>
        <hr class="section-line">

        {{-- カテゴリー --}}
        <div class="form-group">
            <label class="form-label">カテゴリー</label>
            <div class="category-group">
                @foreach($categories as $index => $category)
                <label class="category-chip">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                        {{ (is_array(old('categories')) && in_array($category->id, old('categories'))) ? 'checked' : '' }}>
                    <span>{{ $category->name }}</span>
                </label>
                {{-- ✅ 改行ポイントを追加 --}}
                @if ($index === 5 || $index === 11)
                <br class="category-break">
                @endif
                @endforeach
            </div>
            @error('categories')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        {{-- 商品の状態 --}}
        <div class="form-group">
            <label class="form-label">商品の状態</label>
            <section class="pay-select" data-select>
                <input type="hidden" name="condition" id="condition" value="{{ old('condition') }}">

                {{-- 表示ボタン部分 --}}
                <button type="button"
                    class="select-button"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    data-select-trigger>
                    <span class="select-label" data-label>
                        @php
                        $condition = old('condition');
                        @endphp
                        {{ $condition ?: '選択してください' }}
                    </span>
                    <svg class="chevron" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" />
                    </svg>
                </button>

                {{-- 選択リスト --}}
                <ul class="select-menu" role="listbox" tabindex="-1" data-menu>
                    <li class="select-option" role="option" data-value="良好">良好</li>
                    <li class="select-option" role="option" data-value="目立った傷や汚れなし">目立った傷や汚れなし</li>
                    <li class="select-option" role="option" data-value="やや傷や汚れあり">やや傷や汚れあり</li>
                    <li class="select-option" role="option" data-value="状態が悪い">状態が悪い</li>
                </ul>
            </section>
            @error('condition')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        {{-- ▼ 商品名と説明 --}}
        <h2 class="section-title">商品名と説明</h2>
        <hr class="section-line">

        {{-- 商品名 --}}
        <div class="form-group">
            <label for="title" class="form-label">商品名</label>
            <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}">
            @error('title')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ブランド名 --}}
        <div class="form-group">
            <label for="brand" class="form-label">ブランド名</label>
            <input type="text" name="brand" id="brand" class="form-input" value="{{ old('brand') }}">
        </div>

        {{-- 商品説明 --}}
        <div class="form-group">
            <label for="description" class="form-label">商品の説明</label>
            <textarea name="description" id="description" class="form-textarea" rows="5">{{ old('description') }}</textarea>
            @error('description')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 価格 --}}
        <div class="form-group">
            <label for="price" class="form-label">販売価格</label>
            <input type="number" name="price" id="price" class="form-input" value="{{ old('price') }}" placeholder="¥">
            @error('price')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        {{-- 出品ボタン --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit">出品する</button>
        </div>
    </form>

</div>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const selects = document.querySelectorAll('[data-select]');
        selects.forEach(select => {
            const button = select.querySelector('[data-select-trigger]');
            const menu = select.querySelector('[data-menu]');
            const options = select.querySelectorAll('.select-option');
            const label = select.querySelector('[data-label]');
            const hiddenInput = select.querySelector('input[type="hidden"]');

            // ▼開閉制御
            button.addEventListener('click', () => {
                const isOpen = select.getAttribute('data-open') === 'true';
                select.setAttribute('data-open', !isOpen);
                button.setAttribute('aria-expanded', !isOpen);

                // 開いたときに前回の青チェックを全リセット
                if (!isOpen) {
                    options.forEach(opt => opt.removeAttribute('aria-selected'));
                }
            });

            // ▼選択処理
            options.forEach(option => {
                option.addEventListener('click', () => {
                    // すべてリセット（青チェック消す）
                    options.forEach(opt => opt.removeAttribute('aria-selected'));

                    // ラベル更新（選択済みをボタンに反映）
                    const val = option.dataset.value;
                    label.textContent = option.textContent;
                    hiddenInput.value = val;

                    // メニューを閉じる
                    select.setAttribute('data-open', 'false');
                    button.setAttribute('aria-expanded', 'false');
                });
            });

            // ▼外クリックで閉じる
            document.addEventListener('click', e => {
                if (!select.contains(e.target)) {
                    select.setAttribute('data-open', 'false');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}?v={{ filemtime(public_path('css/items-show.css')) }}">
@endpush
@endsection