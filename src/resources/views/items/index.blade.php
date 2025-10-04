@extends('layouts.app')

@section('title', '商品一覧')

@section('header')
@include('partials.header', ['showSearch' => true, 'showLogin' => true, 'showMypage' => true, 'showSell'=> true])
@endsection

@section('content')
<div class="container">
    @php $tab = request('tab', 'recommend'); @endphp
    <nav class="tabs">
        <a href="{{ url('/?tab=recommend') }}" class="tab {{ $tab==='recommend' ? 'is-active' : '' }}">おすすめ</a>
        <a href="{{ url('/?tab=likes') }}" class="tab {{ $tab==='likes' ? 'is-active' : '' }}">マイリスト</a>
    </nav>
    <div class="item-grid">
        @foreach($items as $item)
        <a class="item-card" href="{{ url('/item/'.$item->id) }}">
            <div class="thumb">
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}">
                @if($item->is_sold)
                <span class="badge">SOLD</span>
                @endif
            </div>
            <div class="meta">
                <div class="title">{{ $item->title }}</div>
                <div class="price">¥{{ number_format($item->price) }}</div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="paginate">{{ $items->links() }}</div>
</div>
@endsection