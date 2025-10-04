@extends('layouts.app')

@section('title', $item->name)

@section('header')
@include('partials.header', ['showSearch' => true, 'showLogin' => true, 'showRegister' => true])
@endsection

@section('content')
<div class="container">
    <div class="detail">
        <div class="detail-image">
            <img src="{{ $item->image_url }}" alt="{{ $item->title }}">
            @if($item->status === 'sold')
            <span class="badge">SOLD</span>
            @endif
        </div>
        <div class="detail-info">
            <h1>{{ $item->title }}</h1>
            <p class="price">¥{{ number_format($item->price) }}</p>
            <p class="desc">{{ $item->description }}</p>
        </div>
    </div>
</div>
@endsection