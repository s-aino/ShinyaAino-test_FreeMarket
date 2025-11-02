@extends('layouts.app')

@section('title', 'メール認証')

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
    <h2>認証メールを送信しました</h2>
    <p class="verify-subtext">受信したメール内のリンクをクリックして認証を完了してください。</p>

    <div class="verify-instruction">
        <p>※Mailtrapにてメール内容を確認し、「Verify Email Address」ボタンをクリックしてください。</p>
    </div>

    {{-- 戻るボタン（任意） --}}
    <form method="GET" action="{{ route('verification.notice') }}">
        <button type="submit" class="back-btn">戻る</button>
    </form>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endpush
