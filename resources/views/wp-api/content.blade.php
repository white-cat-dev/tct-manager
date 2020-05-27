<div class="tct-manager-post-content">
	<p><strong>Размер:</strong></p>

	<table>
		<tbody>
			@if ($productGroup->set_pair)
			<tr>
				<td></td>
				<td><strong>1 элемент</strong></td>
				<td><strong>2 элемент</strong></td>
			</tr>
			@endif
			<tr>
				<td>@switch($productGroup->size_params) @case('lwh')<strong>длина</strong>@break @case('lhw')<strong>длина</strong>@break @case('whl')<strong>ширина</strong>@break @case('lh')<strong>диаметр</strong>@break @endswitch</td>
				<td>{{ $productGroup->length }} мм</td>
				@if ($productGroup->set_pair)
				<td>{{ $productGroup->set_pair->length }} мм</td>
				@endif
			</tr>
			@if ($productGroup->size_params != 'lh')
			<tr>
				<td>@switch($productGroup->size_params) @case('lwh')<strong>ширина</strong>@break @case('lhw')<strong>высота</strong>@break @case('whl')<strong>высота</strong>@break @endswitch</td>
				<td>{{ $productGroup->width }} мм</td>
				@if ($productGroup->set_pair)
				<td>{{ $productGroup->set_pair->width }} мм</td>
				@endif
			</tr>
			@endif
			<tr>
				<td>@switch($productGroup->size_params) @case('lwh')<strong>высота</strong>@break @case('lhw')<strong>ширина</strong>@break @case('whl')<strong>длина</strong>@break @case('lh')<strong>высота</strong>@break @endswitch</td>
				<td>{{ $productGroup->height }} мм</td>
				@if ($productGroup->set_pair)
				<td>{{ $productGroup->set_pair->height }} мм</td>
				@endif
			</tr>
		</tbody>
	</table>

	&nbsp;

	<p><strong>Цена:</strong></p>

	<table>
		<tbody>
			@if ($productGroup->category->variations == 'colors')
			<tr>
				<td>
					<strong>цвет</strong>
				</td>
				@foreach ($productGroup->products as $product)
					<td>
						<strong>{{ $product->main_variation_text }}</strong>
					</td>
				@endforeach
			</tr>
			@endif
			<tr>
				<td>
					<strong>@switch($productGroup->category->units) @case('area')кв. м@break @case('volume')куб. м@break @case('unit')шт@break @endswitch</strong>
				</td>
				@foreach ($productGroup->products as $product)
				<td>
					{{ $product->price }} руб.
				</td>
				@endforeach
			</tr>
			@if ($productGroup->category->units != 'unit')
			<tr>
				<td>
					<strong>шт</strong>
				</td>
				@foreach ($productGroup->products as $product)
				<td>
					{{ $product->price_unit }} @if ($productGroup->set_pair) и {{ $productGroup->set_pair->products->where('variation', $product->variation)->first()->price_unit }} @endif руб.
				</td>
				@endforeach
			</tr>
			@endif
		</tbody>
	</table>

	&nbsp;

	@if ($productGroup->weight_unit || $productGroup->weight_pallete)
	<p><strong>Вес:</strong></p>

	<table>
		<tbody>
			<tr>
				<td><strong>шт</strong></td>
				<td>{{ $productGroup->weight_unit }} @if ($productGroup->set_pair) и {{ $productGroup->set_pair->weight_unit }} @endif кг</td>
			</tr>
			@if ($productGroup->weight_pallete)
			<tr>
				<td><strong>поддона</strong></td>
				<td>{{ $productGroup->weight_pallete }} кг</td>
			</tr>
			@endif
		</tbody>
	</table>

	&nbsp;
	@endif
	
	@if ($productGroup->category->main_category == 'blocks')
	<p><strong>Объем:</strong></p>

	<table>
		<tbody>
			<tr>
				<td><strong>шт</strong></td>
				<td>{{ $productGroup->length * $productGroup->width * $productGroup->height / 1000000000 }} м<sup>3</sup></td>
			</tr>
			<tr>
				<td><strong>поддона</strong></td>
				<td>{{ $productGroup->length * $productGroup->width * $productGroup->height / 1000000000 * $productGroup->unit_in_pallete }} м<sup>3</sup></td>
			</tr>
		</tbody>
	</table>

	&nbsp;
	@endif


	@if ($productGroup->unit_in_units || $productGroup->unit_in_pallete)
	<p>
		<strong>Дополнительная информация:</strong><br>
	</p>

	<p>
		@if ($productGroup->category->units != 'unit') В <strong>1 @switch($productGroup->category->units) @case('area')кв. м@break @case('volume')куб. м@break @endswitch</strong> – {{ $productGroup->unit_in_units }} @if ($productGroup->set_pair) и {{ $productGroup->set_pair->unit_in_units }} @endif шт. @endif
		
		@if ($productGroup->category->units != 'unit')
		На <strong>1 поддоне</strong> – {{ $productGroup->unit_in_pallete }}@if ($productGroup->set_pair) и {{ $productGroup->set_pair->unit_in_pallete }} @endif шт, {{ $productGroup->units_in_pallete }} @switch($productGroup->category->units) @case('area')м<sup>2</sup>@break @case('volume')м<sup>3</sup>@break @endswitch
		@else
		На <strong>1 поддоне</strong> – {{ $productGroup->unit_in_pallete }} шт
		@endif
	</p>
	@endif
</div><!-- tct-manager-post-content -->
