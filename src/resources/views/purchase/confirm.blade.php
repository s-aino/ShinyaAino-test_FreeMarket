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
    <div class="purchase-grid">
        {{-- 左カラム --}}
        <div class="left">
            {{-- 見出しは非表示（要件） --}}

            {{-- 商品ヘッダ行 --}}
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

            {{-- エラー表示 --}}
            @if ($errors->any())
            <ul class="errors">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            @endif

            <form action="{{ route('purchase.store', $item->id) }}" method="POST" class="purchase-form">
                @csrf

                {{-- 支払い方法 --}}
                <section class="block">
                    <h2>支払い方法</h2>

                    {{-- ← 左は「表示用」hidden。nameは付けない（送信は右で行う） --}}
                    <input type="hidden" id="payment_method" value="{{ old('payment_method') }}">

                    <div class="pay-select" data-select>
                        <button type="button"
                            class="select-button"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            data-select-trigger>
                            <span class="select-label" data-label>
                                {{ old('payment_method') === 'conveni' ? 'コンビニ払い' : (old('payment_method') === 'card' ? 'カード支払い' : '選択してください') }}
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
                <hr class="divider"> {{-- 配送先 --}}
                <section class="block">
                    <div class="block-head">
                        <h2>配送先</h2>
                        @if ($address)
                        <a class="change-link" href="{{ route('purchase.address.edit', $item->id) }}">変更する</a>
                        @endif
                    </div>

                    @if ($address)
                    <p class="addr-line">〒 {{ $address->postal ?? 'ー' }}</p>
                    <p class="addr-line">
                        {{ ($address->prefecture ?? '') . ($address->city ?? '') . ($address->line1 ?? '') }}
                        {{ $address->line2 ? ' ' . $address->line2 : '' }}
                    </p>
                    <input type="hidden" name="address_id" value="{{ $address->id }}">
                    @else
                    <p>住所が未登録です。<a href="{{ route('purchase.address.edit', $item->id) }}">登録する</a></p>
                    @endif
                </section>

                <hr class="divider">
                <div class="buy-mobile">
                    <button type="submit" class="btn-primary" {{ $address ? '' : 'disabled' }}>購入する</button>
                </div>
            </form>
        </div>

        {{-- 右カラム（サマリカード＋購入ボタン） --}}
        <aside class="right">
            <div class="summary-card">
                <div class="row"><span>商品代金</span><span>¥{{ number_format($item->price) }}</span></div>
                <div class="row">
                    <span>支払い方法</span>
                    <span id="summary_payment">
                        @switch(old('payment_method'))
                        @case('conveni') コンビニ払い @break
                        @case('card') カード支払い @break
                        @default ー
                        @endswitch
                    </span>
                </div>
            </div>

            <form action="{{ route('purchase.store', $item->id) }}" method="POST" class="buy-box">
                @csrf
                {{-- ← 右は「送信用」hidden。必ず name="payment_method" を付ける --}}
                <input type="hidden" name="payment_method" id="shadow_payment" value="{{ old('payment_method') }}">

                @if($address)
                <input type="hidden" name="address_id" value="{{ old('address_id', $address->id) }}">
                @endif

                <button type="submit" class="btn-primary" {{ $address ? '' : 'disabled' }}>購入する</button>
            </form>
        </aside>
    </div>
</div>

{{-- セレクト→サマリの表示をその場で同期（任意） --}}
<script>
    (() => {
        const root = document.querySelector('[data-select]');
        if (!root) return;

        const btn = root.querySelector('[data-select-trigger]');
        const label = root.querySelector('[data-label]');
        const menu = root.querySelector('[data-menu]');
        const items = Array.from(root.querySelectorAll('.select-option'));

        // ← ここは上のIDに合わせています
        const inputL = document.getElementById('payment_method'); // 左：表示用（nameなし）
        const inputR = document.getElementById('shadow_payment'); // 右：送信用（nameあり）
        const summary = document.getElementById('summary_payment'); // 右：表示

        const textOf = v => v === 'conveni' ? 'コンビニ払い' :
            v === 'card' ? 'カード支払い' :
            '選択してください';

        const open = () => {
            menu.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
        };
        const close = () => {
            menu.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
        };

        function apply(val, text) {
            if (inputL) inputL.value = val || '';
            if (inputR) inputR.value = val || '';
            if (label) label.textContent = text || textOf(val);
            if (summary) summary.textContent = text || textOf(val);
            items.forEach(x => x.classList.toggle('is-selected', x.dataset.value === val));
        }

        btn.addEventListener('click', () => {
            const willOpen = !menu.classList.contains('is-open');
            menu.classList.toggle('is-open', willOpen);
            btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        items.forEach(it => {
            it.addEventListener('click', () => {
                apply(it.dataset.value, it.textContent.trim());
                close();
            });
        });

        document.addEventListener('click', e => {
            if (!root.contains(e.target)) close();
        });
        root.addEventListener('keydown', e => {
            if (e.key === 'Escape') close();
        });

        // 初期表示（old → 右hidden → 左hidden の順で復元）
        const init = (inputR && inputR.value) || (inputL && inputL.value) || '';
        if (init) {
            const it = items.find(i => i.dataset.value === init);
            apply(init, it ? it.textContent.trim() : textOf(init));
        } else {
            apply('', textOf(''));
        }
    })();
</script>
@endsection