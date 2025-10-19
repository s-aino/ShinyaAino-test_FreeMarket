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

    {{-- ▼ フォーム開始 --}}
    <form action="{{ route('purchase.checkout', $item->id) }}" method="POST" class="purchase-form">
        @csrf
        <div class="purchase-grid">

            {{-- ▼ 左カラム --}}
            <div class="left">

                {{-- 商品ヘッダ --}}
                <div class="product-head">
                    <div class="thumb">
                        <img src="{{ $item->image_url ?? asset('img/placeholder.png') }}" alt="商品画像">
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
                    <p class="addr-line">〒 {{ $address->postal ?? '―' }}</p>
                    <p class="addr-line">
                        {{ ($address->prefecture ?? '') . ($address->city ?? '') . ($address->line1 ?? '') }}
                        {{ $address->line2 ? ' ' . $address->line2 : '' }}
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
                        <h2>支払い方法</h2>
                        <p id="summary_payment">
                            @if (old('payment_method') === 'conveni')
                            コンビニ払い
                            @elseif (old('payment_method') === 'card')
                            カード支払い
                            @else
                            ――
                            @endif
                        </p>
                    </section>
                </div>

                <button type="submit" class="btn btn-purchase">購入する</button>
            </aside>
            {{-- ▲ 右カラムここまで --}}

        </div>
    </form>
    {{-- ▲ フォーム終了 --}}
</div>

{{-- ▼ JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-select]');
    const input = document.getElementById('payment_method');
    const summary = document.getElementById('summary_payment');

    if (!root || !input) return;

    const btn = root.querySelector('[data-select-trigger]');
    const menu = root.querySelector('[data-menu]');
    const items = root.querySelectorAll('.select-option');

    // ▼ 支払方法をクリックしたときに hidden に値を入れる
    items.forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value;
            input.value = value;});
        });
        // 開閉
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', menu.classList.contains('is-open'));
        });

        // 外クリックで閉じる
        document.addEventListener('click', (e) => {
            if (!root.contains(e.target)) {
                menu.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        // 選択処理
        items.forEach(it => {
            it.addEventListener('click', () => {
                const val = it.dataset.value;
                input.value = val;

                summary.textContent =
                    (val === 'conveni') ? 'コンビニ払い' :
                    (val === 'card') ? 'カード支払い' :
                    '――';

                const label = root.querySelector('.select-label');
                if (label) {
                    label.textContent =
                        (val === 'conveni') ? 'コンビニ払い' :
                        (val === 'card') ? 'カード支払い' :
                        '選択してください';
                }

                menu.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
            });
        });
    });
</script>
@endsection