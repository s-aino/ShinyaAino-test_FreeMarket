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
    <!-- <h2>認証メールを送信しました</h2> -->
    <p class="verify-subtext">
        <strong>「認証はこちらから」ボタンを押すと Mailtrapに移動</strong>し、<br>
        受信メール内のリンクから認証を完了します。<br><br>
        ただし現在の練習環境では外部サイト（Mailtrap）への<br>自動遷移ができないため、
        このページを表示しています。
    </p>

    <div class="verify-instruction">
        <p>手動で Mailtrap を開き、受信メール「Verify Email Address」を確認してください。</p>
        <p>メール本文内の「Verify Email Address」ボタンをクリックすると認証が完了します。</p>
    </div>

    <form method="GET" action="{{ route('verification.notice') }}">
        <button type="submit" class="back-btn">メール認証案内に戻る</button>
    </form>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endpush
