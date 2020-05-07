<div class="product-excerpt-block">
    <div>
        <strong>Размер: </strong>{{ $productGroup->size }} мм
    </div>
    @foreach ($productGroup->products as $product)
    <div>
        <strong>Цена: </strong>{{ $product->main_color_text }} — {{ $product->price }}  руб/
        @if ($productGroup->category->units == 'area') м<sup>2</sup> @endif
        @if ($productGroup->category->units == 'volume') м<sup>3</sup> @endif
        @if ($productGroup->category->units == 'units') шт. @endif
    </div>
    @endforeach
</div>