@extends('layouts.app')

@section('content')
<div class="address-wrap">
    <h1>住所の変更</h1>

    @if ($errors->any())
    <ul class="errors">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    @endif

    <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="address-form">
        @csrf
        <label class="field"><span>郵便番号</span>
            <input type="text" name="postal_code" placeholder="123-4567"
                value="{{ old('postal_code', optional($address)->postal_code) }}" required>
        </label>

        <label class="field"><span>都道府県</span>
            <input type="text" name="prefecture" value="{{ old('prefecture', optional($address)->prefecture) }}" required>
        </label>

        <label class="field"><span>市区町村</span>
            <input type="text" name="city" value="{{ old('city', optional($address)->city) }}" required>
        </label>

        <label class="field"><span>住所（番地）</span>
            <input type="text" name="address_line1" value="{{ old('address_line1', optional($address)->address_line1) }}" required>
        </label>

        <label class="field"><span>建物名（任意）</span>
            <input type="text" name="address_line2" value="{{ old('address_line2', optional($address)->address_line2) }}">
        </label>

        <div class="actions">
            <button type="submit" class="btn-primary">更新する</button>
            <a href="{{ route('purchase.create', $item->id) }}" class="btn-secondary">戻る</a>
        </div>
    </form>
</div>
@endsection