{{-- resources/views/items/show.blade.php --}}
@extends('layouts.app')

@section('title', $item->title ?? '商品詳細')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogin' => true,
'showMypage' => true,
'showSell' => true,
])
@endsection

@section('content')
<div class="container">
    <div class="detail">
        {{-- 画像 --}}
        <div class="detail__image">
            <div class="thumb">
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy" decoding="async">
                @if(method_exists($item, 'getIsSoldAttribute') ? $item->is_sold : ($item->status === 'sold'))
                <span class="badge--sold">SOLD</span>
                @endif
            </div>
        </div>

        {{-- 本文 --}}
        <div class="detail__main">
            <h1 class="detail__title">{{ $item->title }}</h1>
            @if(!empty($item->brand))
            <div class="detail__brand">{{ $item->brand }}</div>
            @endif

            {{-- 価格（※（税込）はCSSで付与） --}}
            <div class="detail__price">¥{{ number_format((int)($item->price ?? 0)) }}</div>

            {{-- いいね / コメント数（白抜き風アイコン） --}}
            @php
            $liked = auth()->check() ? $item->isLikedBy(auth()->user()) : false;
            @endphp

            <div class="detail__stats">
                {{-- 星（いいね） --}}
                <div class="stat">
                    @auth
                    <form method="POST" action="{{ $liked ? route('likes.destroy',$item) : route('likes.store',$item) }}">
                        @csrf
                        @if($liked) @method('DELETE') @endif

                        <button type="submit"
                            class="icon-btn {{ $liked ? 'is-on' : '' }}"
                            aria-label="{{ $liked ? 'いいねを取り消す' : 'いいねする' }}">
                            {{-- 好き/嫌いで SVG を切り替え（※あなたのポイント値をそのまま使用） --}}
                            @if($liked)
                            {{-- 塗り（ON）--}}
                            <svg class="glyph-svg" viewBox="0 0 24 24" aria-hidden="true">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 13.97 18.18 21 12 17.77 5.82 21 7 13.97 2 9.27 8.91 8.26"
                                    fill="currentColor" />
                            </svg>
                            @else
                            {{-- アウトライン（OFF） --}}
                            <svg class="glyph-svg" viewBox="0 0 24 24" aria-hidden="true">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 13.97 18.18 21 12 17.77 5.82 21 7 13.97 2 9.27 8.91 8.26"
                                    fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            @endif
                        </button>
                    </form>
                    @else
                    {{-- 未ログインはログインへ誘導 --}}
                    <a href="{{ route('login') }}" class="icon-btn" aria-label="ログインしていいね">
                        <svg class="glyph-svg" viewBox="0 0 24 24" aria-hidden="true">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 13.97 18.18 21 12 17.77 5.82 21 7 13.97 2 9.27 8.91 8.26"
                                fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    @endauth

                    <span class="count">{{ $item->likes_count ?? $item->likes()->count() }}</span>
                </div>

                {{-- 吹き出しはそのまま（表示だけ） --}}
                <div class="stat">
                    {{-- いま使っている吹き出し SVG を維持 --}}
                    <svg class="glyph-svg comment-rounded" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3.8c4.66 0 8.44 3.16 8.44 7.06S16.66 17.9 12 17.9a9.4 9.4 0 0 1-2.72-.39L6 18l.83-2.25A7.9 7.9 0 0 1 3.56 10.9C3.56 6.96 7.34 3.8 12 3.8Z"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="count">{{ $item->comments_count ?? $item->comments()->count() }}</span>
                </div>
            </div>
            {{-- 購入ボタン（ゲストはログイン導線） --}}

            <div class="actions">
                @auth
                <a class="btn btn--primary btn--square"
                    href="{{ route('purchase.create', $item) }}">
                    購入手続きへ
                </a>
                @else
                <a class="btn btn--primary btn--square" href="{{ route('login') }}">ログインして購入する</a>
                @endauth
            </div> {{-- 商品説明 --}}
            @if(!empty($item->description))
            <section class="detail__section">
                <h2>商品説明</h2>
                <p class="detail__desc">{{ $item->description }}</p>
            </section>
            @endif

            {{-- 商品情報（カテゴリ／状態） --}}
            <section class="detail__section">
                <h2>商品情報</h2>
                <dl class="detail__spec">
                    <dt>カテゴリ</dt>
                    <dd>
                        @if(isset($categories) && $categories->isNotEmpty())
                        @foreach($categories as $name)
                        <span class="chip">{{ $name }}</span>
                        @endforeach
                        @elseif(isset($item->category) && $item->category)
                        <span class="chip">{{ $item->category->name }}</span>
                        @else
                        <span class="muted">-</span>
                        @endif
                    </dd>

                    <dt>商品の状態</dt>
                    <dd>{{ $item->condition ?? ($item->condition_label ?? '不明') }}</dd>
                </dl>
            </section>

            {{-- コメント一覧 --}}
            <section  class="detail__section detail__section--comments">
                <h2>コメント（{{ $item->comments_count ?? $item->comments()->count() }}）</h2>
                …

                @forelse ($item->comments as $comment)
                <article class="cmt">
                    {{-- アバター（左の列） --}}
                    <img
                        class="cmt__avatar"
                        src="{{ $comment->user->avatar_url }}"
                        alt="{{ $comment->user->name }} のアイコン">

                    {{-- 名前＋時刻（右の列の1行目） --}}
                    <div class="cmt__meta">
                        <strong class="cmt__user">{{ $comment->user->name }}</strong>
                        <time class="cmt__time" datetime="{{ $comment->created_at->toIso8601String() }}">
                            {{ $comment->created_at->format('Y/m/d H:i') }}
                        </time>
                    </div>

                    {{-- グレーのボックス：コメント本文（右の列の2行目 / 幅は列いっぱい） --}}
                    <div class="cmt__bubble">
                        {!! nl2br(e($comment->body)) !!}
                    </div>
                </article>
                @empty
                <p class="muted">まだコメントはありません。</p>
                @endforelse {{-- コメント投稿欄 --}}
                <form action="{{ auth()->check()
                  ? route('comments.store', ['item' => $item->id])
                  : route('comments.prepare', ['item' => $item->id]) }}"
                    method="POST" class="comment__form actions" id="comments">
                    @csrf
                    <div class="comment-title">商品へのコメント</div>

                    <textarea id="comment-body" class="comment-box"
                        name="body"
                        maxlength="1000"
                        placeholder="ここにコメントを入力…">{{ old('body') }}</textarea>

                    @error('body')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="item-detail">
                        <button type="submit" class="btn btn--primary btn--square" style="margin-top:10px;">
                            コメントを送信する
                        </button>
                    </div>
            </section>
        </div>
    </div>
</div>
@endsection