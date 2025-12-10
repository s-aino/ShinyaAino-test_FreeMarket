@extends('layouts.app')

@section('title', '購入手続き')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showMypage' => true,
'showSell' => true,
])
@endsection
@section('content')
<div class="purchase-wrap">

    <form action="{{ route('purchase.checkout', $item->id) }}" method="POST" class="purchase-form">
        @csrf <div class="purchase-grid">
            {{-- ▼ 左カラム --}}
            <div class="left">

                {{-- 商品ヘッダ --}}
                <div class="product-head">
                    <div class="thumb">
                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">
                    </div>
                    <div class="meta">
                        <p class="title">{{ $item->name ?? $item->title }}</p>
                        <p class="price">¥{{ number_format($item->price ?? 0) }}</p>
                    </div>
                </div>

                <hr class="divider">

                {{-- 支払い方法 --}}
                <section class="block">
                    <h2>支払い方法</h2>
                    <input type="hidden" name="payment_method" id="payment_method" value="{{ old('payment_method') }}">

                    <div class="pay-select" data-select>
                        <button type="button"
                            class="select-button"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            data-select-trigger>
                            <span class="select-label" data-label>
                                @php
                                $method = old('payment_method');
                                @endphp
                                {{ $method === 'conveni' ? 'コンビニ払い' : ($method === 'card' ? 'カード支払い' : '選択してください') }}
                            </span>
                            <svg class="chevron" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </button>

                        <ul class="select-menu" role="listbox" tabindex="-1" data-menu>
                            <li class="select-option" role="option" data-value="conveni">コンビニ払い</li>
                            <li class="select-option" role="option" data-value="card">カード支払い</li>
                        </ul>
                    </div>
                </section>

                <hr class="divider">

                {{-- 配送先 --}}
                <section class="block">
                    <div class="block-head">
                        <h2>配送先</h2>
                        @if ($address)
                        <a class="change-link" href="{{ route('purchase.address.edit', $item->id) }}">変更する</a>
                        @endif
                    </div>

                    @if ($address)
                    <p class="addr-line">〒 {{ $address->postal }}</p>

                    <p class="addr-line">
                        {{ $address->line1 }}
                        {{ $address->line2 ? '・' . $address->line2 : '' }}
                    </p>
                    <input type="hidden" name="address_id" value="{{ $address->id }}">
                    <hr class="divider">
                    @else
                    <p>住所が未登録です。<a href="{{ route('purchase.address.edit', $item->id) }}">登録する</a></p>
                    @endif
                </section>

            </div>
            {{-- ▲ 左カラムここまで --}}

            {{-- ▼ 右カラム（サマリー＋ボタン） --}}
            <aside class="right">
                <div class="summary-card">
                    <div class="row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>

                    <section class="block">
                        <p id="summary_payment">
                            <span>支払い方法</span>
                            <span id="summary-method">
                                @if (old('payment_method') === 'conveni')
                                コンビニ払い
                                @elseif (old('payment_method') === 'card')
                                カード支払い
                                @else
                                ――
                                @endif
                            </span>
                    </section>
                </div>
                {{-- ▼ 購入ボタン --}}
                @if(!$address)
                <!-- <button class="btn-purchase-disabled" disabled>購入できません</button> -->
                <p class="address-alert">※マイページで住所を登録してください。</p>
                @else
                <button type="submit" class="btn btn-purchase">購入する</button>
                @endif
            </aside>
            {{-- ▲ 右カラムここまで --}}

        </div>
    </form>
    {{-- ▲ フォーム終了 --}}
</div>

{{-- ▼ JS --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selects = document.querySelectorAll('[data-select]');
        selects.forEach(select => {
            const button = select.querySelector('[data-select-trigger]');
            const menu = select.querySelector('[data-menu]');
            const options = select.querySelectorAll('.select-option');
            const label = select.querySelector('[data-label]');
            const hiddenInput = document.querySelector('#payment_method');
            const summary = document.querySelector('#summary-method'); // 右カラム

            // ▼ 開閉制御
            button.addEventListener('click', () => {
                const isOpen = select.getAttribute('data-open') === 'true';
                select.setAttribute('data-open', !isOpen);
                button.setAttribute('aria-expanded', !isOpen);

                // 開くたびに一旦全てリセット（青・チェックを消す）
                if (!isOpen) {
                    options.forEach(opt => opt.removeAttribute('aria-selected'));
                }
            });

            // ▼ 選択処理
            options.forEach(option => {
                option.addEventListener('click', () => {
                    // ① すべてリセット
                    options.forEach(opt => opt.removeAttribute('aria-selected'));

                    // ② 選択中の項目にマーク（青・✔）
                    option.setAttribute('aria-selected', 'true');

                    // ③ ラベル更新（白い1行に反映）
                    label.textContent = option.textContent;

                    // ④ hiddenInputと右カラムに反映
                    const val = option.dataset.value;
                    hiddenInput.value = val;
                    if (summary) summary.textContent = option.textContent;

                    // ⑤ メニューを閉じる
                    select.setAttribute('data-open', 'false');
                    button.setAttribute('aria-expanded', 'false');
                });
            });

            // ▼ 外クリックで閉じる
            document.addEventListener('click', e => {
                if (!select.contains(e.target)) {
                    select.setAttribute('data-open', 'false');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
</script>
@push('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush
@endsection