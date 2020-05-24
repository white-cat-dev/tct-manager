<div class="tct-manager-post-content" data-post-title="{{ $wpSlug }}">
	<div>
		<h3>Размер:</h3>
		<table>
			<tbody>
				<tr>
					<td><strong>длина</strong></td>
					<td>{{ $productGroup->length }} мм</td>
				</tr>
				<tr>
					<td><strong>ширина</strong></td>
					<td>{{ $productGroup->width }} мм</td>
				</tr>
				<tr>
					<td><strong>высота</strong></td>
					<td>{{ $productGroup->depth }} мм</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		<h3>Цена:</h3>
		<table>
			<tbody>
				<tr>
					<td>
						<strong>цвет </strong>
					</td>
					@foreach ($productGroup->products as $product)
					<td>
						<strong>{{ $product->main_variation_text }}</strong>
					</td>
					@endforeach
				</tr>
				<tr>
					<td>
						<strong>шт.</strong>
					</td>
					@foreach ($productGroup->products as $product)
					<td>
						{{ $product->price_unit }} руб.
					</td>
					@endforeach
				</tr>
				<tr>
					<td>
						<strong>кв. м</strong>
					</td>
					@foreach ($productGroup->products as $product)
					<td>
						{{ $product->price }} руб.
					</td>
					@endforeach
				</tr>
				<tr>
					<td>
						<strong>поддон</strong>
					</td>
					@foreach ($productGroup->products as $product)
						<td>
							{{ $product->price_pallete }} руб.
						</td>
					@endforeach
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		<h3>Вес:</h3>

		<table>
			<tbody>
				<tr>
					<td><strong>шт.</strong></td>
					<td>{{ $productGroup->weight_unit }} кг</td>
				</tr>
				<tr>
					<td><strong>кв. м</strong></td>
					<td>{{ $productGroup->weight_units }} кг</td>
				</tr>
				<tr>
					<td><strong>поддона</strong></td>
					<td>{{ $productGroup->weight_pallete }} кг</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		<h3>Дополнительная информация:</h3>
		В <strong><em>1</em> кв. м</strong> – {{ $productGroup->unit_in_units }} шт.
		В <strong><em>1</em> поддоне</strong> – {{ $productGroup->unit_in_pallete }} шт., {{ $productGroup->units_in_pallete }} м<sup>2</sup>
	</div>
</div><!-- tct-manager-post-content -->
