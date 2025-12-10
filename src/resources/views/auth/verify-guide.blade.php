@extends('layouts.app')

@section('title', 'メール認証ガイド')

@section('header')
@include('partials.header', [
'showSearch' => false,
'showLogin' => false,
'showMypage' => false,
'showSell' => false,
])
@endsection

@section('content')
<div class="verify-container">

    <div class="verify-text">

        <p>
            <strong>「認証はこちらから」ボタンを押すと Mailtrap に移動し、受信メール内のリンクから認証を完了します。</strong>
        </p>

        <p>
            ただし現在の練習環境では外部サイト（Mailtrap）への自動遷移ができないため、
            この案内ページを表示しています。
        </p>

    </div>

    <div class="verify-instruction">

        <p>1. ブラウザで Mailtrap（https://mailtrap.io/）を開いてください。</p>
        <p>2. Sandboxes 内の受信メール「Verify Email Address」を確認してください。</p>
        <p>3. メール本文内の「Verify Email Address」ボタンをクリックすると認証が完了します。</p>

    </div>
    <form method="GET" action="{{ route('verification.notice') }}">
        <button type="submit" class="back-btn">メール認証案内に戻る</button>
    </form>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endpush