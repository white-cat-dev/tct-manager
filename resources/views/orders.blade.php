<div class="orders-block" ng-init="init()">
	<h1>Заказы</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Поиск...">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button">Поиск</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			<a href="{{ route('order-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать заказ
			</a>
		</div>
	</div>

	<table class="table">
		<tr>
			<th>№</th>
			<th>Информация о клиенте</th>
			<th>Цена</th>
			<th colspan="2">Продукты</th>
			<th></th>
		</tr>

		<tr ng-repeat="order in orders">
			<td>
				@{{ order.id }}
			</td>
			<td>
				<div ng-if="!order.client">
					Нет данных о клиенте
				</div>
				<div ng-if="order.client">
					@{{ order.client.name }}
					@{{ order.client.phone }}
					@{{ order.client.email }}
				</div>
			</td>
			<td>
				@{{ order.cost }} руб
			</td>
			<td>
				<div ng-repeat="product in order.products">
					@{{ product.product_group.name }}
					(@{{ product.color }})
				</div>
			</td>
			<td>
				<div ng-repeat="product in order.products">
					@{{ product.pivot.count }}
				</div>
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ order.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					<a ng-href="@{{ order.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="delete(order.id)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>
			</td>
		</tr>
	</table>
</div>