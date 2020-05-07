@if ($productGroup)
	<div>
		<strong>Размер:</strong>
		<table border="1" cellspacing="2">
			<tbody>
				<tr>
					<td><strong>длина:</strong></td>
					<td><em>{{ $productGroup->length }}</em> мм</td>
				</tr>
				<tr>
					<td><strong>ширина: </strong></td>
					<td><em>{{ $productGroup->width }}</em> мм</td>
				</tr>
				<tr>
					<td><strong>высота:</strong></td>
					<td><em>{{ $productGroup->depth }}</em> мм</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		<strong>Цена:</strong>
		<table border="1" cellspacing="2">
			<tbody>
				<tr>
					<td>
						<strong>цвет: </strong>
					</td>
					@foreach ($productGroup->products as $product)
					<td>
						<strong>{{ $product->main_color_text }}</strong>
					</td>
					@endforeach
				</tr>
				<tr>
					<td>
						<strong>шт.</strong>
					</td>
					@foreach ($productGroup->products as $product)
					<td>
						<strong>{{ $product->price_unit }}</strong> руб.
					</td>
					@endforeach
				</tr>
				<tr>
					<td>
						<strong>кв.м.</strong>
					</td>
					@foreach ($productGroup->products as $product)
					<td>
						<strong>{{ $product->price }}</strong> руб.
					</td>
					@endforeach
				</tr>
				<tr>
					<td>
						<strong>поддон</strong>
					</td>
					@foreach ($productGroup->products as $product)
						<td>
							<strong>{{ $product->price_pallete }}</strong> руб.
						</td>
					@endforeach
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		<strong>Вес:</strong>

		<table border="1" cellspacing="2">
			<tbody>
				<tr>
					<td><strong> шт. </strong></td>
					<td><em>{{ $productGroup->weight_unit }}</em> кг</td>
				</tr>
				<tr>
					<td><strong> кв.м</strong></td>
					<td><em>{{ $productGroup->weight_units }}</em> кг</td>
				</tr>
				<tr>
					<td><strong> поддона </strong></td>
					<td><em>{{ $productGroup->weight_pallete }}</em> кг</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div>
		<strong>  Дополнительная информация:</strong>
		В <strong><em>1</em></strong><strong> кв. м</strong> - {{ $productGroup->unit_in_units }} шт.
		В <strong><em>1</em></strong><strong> поддоне</strong> - {{ $productGroup->unit_in_pallete }} шт., {{ $productGroup->units_in_pallete }} кв.м
	</div>

	<div>
		<strong>Информация о наличии:</strong>

		<table border="1" cellspacing="2">
			<thead>
				<th>Цвет</th>
				<th>В наличии</th>
			</thead>
			<tbody>
				@foreach ($productGroup->products as $product)
				<tr>
					<td>
						<strong>{{ $product->main_color_text }}</strong>
					</td>
					<td>
						@if ($product->main_color == 'color')
							Производится на заказ
						@else
							{{ $product->in_stock }} 
							@if ($product->category->units == 'area') кв.м @endif
							@if ($product->category->units == 'volume') куб.м @endif
							@if ($product->category->units == 'unit') шт. @endif
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif