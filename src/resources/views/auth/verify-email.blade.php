@extends('layouts.app')
@section('title','メール認証案内')
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
    <h2>登録していただいたメールアドレスに認証メールを送付しました。</h2>
    <p class="verify-subtext">メール認証を完了してください。</p>

    {{-- 「認証はこちらから」ボタン --}}
    <form method="GET" action="{{ route('verification.guide') }}">
        <button type="submit" class="verify-btn">認証はこちらから</button>
    </form>

    {{-- 再送ボタン --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
    </form>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endpush