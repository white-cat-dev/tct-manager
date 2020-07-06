<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<style type="text/css">
		* { 
			font-family: times;
			font-size: 14px;
			line-height: 15px;
		}
		table {
			margin: 0 0 15px 0;
			width: 100%;
			border-collapse: collapse; 
			border-spacing: 0;
		}		
		table td {
			padding: 5px;
		}	
		table th {
			padding: 5px;
			font-weight: bold;
		}

		h1 {
			margin: 0 0 10px 0;
			padding: 10px 0 10px 0;
			border-bottom: 2px solid #000;
			font-weight: bold;
			font-size: 20px;
		}

		.copy {
			margin-bottom: 100px;
		}

		.contacts th {
			padding: 3px 0px;
			vertical-align: top;
			text-align: left;
		}	

		.contacts td {
			padding: 3px 0px;
		}	

		.list thead, 
		.list tbody  {
			border: 2px solid #000;
		}

		.list thead th {
			padding: 4px 0;
			border: 1px solid #000;
			vertical-align: middle;
			text-align: center;
		}

		.list tbody td {
			padding: 3px 5px;
			border: 1px solid #000;
			vertical-align: middle;
		}	

		.list tbody tr td:first-child {
			text-align: center;
		}

		.list tfoot th {
			padding: 3px 5px;
			border: none;
			text-align: right;
		}

		.list tfoot th:last-child {
			text-align: left;
		}	

		.sign {
			margin-top: 10px;
		}
		.sign table {
			width: 100%;
		}
		.sign th {
			padding: 0px;
			text-align: left;
		}
		.sign td {
			padding: 0px;
			border-bottom: 1px solid #000;
			text-align: right;
		}
	</style>
</head>

<body>
	@for ($i = 0; $i < 2; $i++)
		<div class="copy" style="page-break-before: auto; page-break-inside: avoid;">
			<h1>Накладная № {{ $order->number }} от {{ $order->full_formatted_date }}</h1>
		 
			<table class="contacts">
				<tbody>
					<tr>
						<th width="18%">Кому:</th>
						<td>
							{{ $order->client->name }} {{ $order->client->phone }} {{ $order->client->email }}
						</td>
					</tr>
					<tr>
						<th>Телефон цеха:</th>
						<td>
							8-914-923-77-43, ежедневно с 9:00 до 18:00
						</td>
					</tr>
					<tr>
						<th>Дата готовности:</th>
						<td>
							ориентировочно <strong>{{ $order->full_formatted_date_to }}</strong>
						</td>
					</tr>
				</tbody>
			</table>
		 
			<table class="list">
				<thead>
					<tr>
						<th width="5%">№</th>
						<th width="46%">Наименование товара</th>
						<th width="14%">Цена</th>
						<th width="14%">Кол-во</th>
						<th width="16%">Сумма</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($order->products as $productNum => $product)
						<tr>
							<td>
								{{ $productNum + 1 }}
							</td>
							<td>
								{{ $product->product_group->name }} {{ $product->product_group->size }} {{ $product->variation_text }}
							</td>
							<td>
								{{ number_format($product->pivot->price, 2, ',', ' ') }} руб.
							</td>
							<td>
								{{ str_replace('.', ',', $product->pivot->count) }}
								@if ($product->category->units == 'unit') шт. @endif
								@if ($product->category->units == 'area') кв.м. @endif
								@if ($product->category->units == 'volume') куб.м. @endif
							</td>
							<td>
								{{ number_format($product->pivot->cost, 2, ',', ' ') }} руб.
							</td>
						</tr>
					@endforeach
					@if ($order->pallets > 0)
						<tr>
							<td>
								{{ $order->products->count() + 1 }}
							</td>
							<td>
								Поддоны
							</td>
							<td>
								{{ number_format($order->pallets_price, 2, ',', ' ') }} руб.
							</td>
							<td>
								{{ $order->pallets }} шт.
							</td>
							<td>
								{{ number_format($order->pallets * $order->pallets_price, 2, ',', ' ') }} руб.
							</td>
						</tr>
					@endif
					@if ($order->delivery_price > 0)
						<tr>
							<td>
								{{ $order->products->count() + (($order->pallets > 0) ? 2 : 1) }}
							</td>
							<td>
								Доставка
							</td>
							<td>
							</td>
							<td>
							</td>
							<td>
								{{ number_format($order->delivery_price, 2, ',', ' ') }} руб.
							</td>
						</tr>
					@endif
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5"></th>
					</tr>
					<tr>
						<th colspan="4">Итого:</th>
						<th>
							{{ number_format($order->cost, 2, ',', ' ') }} руб.
						</th>
					</tr>
					<tr>
						<th colspan="4">Оплачено:</th>
						<th>
							{{ number_format($order->paid, 2, ',', ' ') }} руб.
						</th>
					</tr>
				</tfoot>
			</table>
			
			<div class="sign">
				<table>
					<tbody>
						<tr>
							<th width="52%">С составом и условиями заказа ознакомлен и согласен</th>
							<td width="35%"></td>
							<th width="13%"></th>
						</tr>
					</tbody>
				</table>

				<table>
					<tbody>
						<tr>
							<th width="8%">Сдал</th>
							<td width="35%"></td>
							<th width="12%"></th>
							<th width="10%">Принял</th>
							<td width="35%"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	@endfor
</body>
</html>