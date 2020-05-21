<span class="tct-manager-post-excerpt" data-post-title="{{ $wpSlug }}"><strong>Размер: </strong>{{ $productGroup->size }} мм <br>
    @foreach ($productGroup->products as $product)
    <strong>{{ $product->main_variation_text }}</strong> – {{ $product->price }} руб/{!! $product->units_text !!} <br>
    @endforeach
</span><!-- tct-manager-post-excerpt -->