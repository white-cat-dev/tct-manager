<span class="tct-manager-post-excerpt"><strong>Размер: </strong>{{ $productGroup->size }} @if ($productGroup->set_pair) и <br>{{ $productGroup->set_pair->size }} @endif мм <br>
    @if ($productGroup->category->variations == 'colors')
	    @foreach ($productGroup->products as $product)
	    	<strong>{{ $product->main_variation_text }} </strong>– {{ $product->price }} руб/{!! $product->units_text !!} <br>
	    @endforeach
	@else
		<strong>Цена: </strong>{{ $productGroup->products[0]->price }} руб/{!! $productGroup->products[0]->units_text !!}
	@endif
</span><!-- tct-manager-post-excerpt -->