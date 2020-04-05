<div class="orders-block" ng-init="initEdit()">
	<h1 ng-if="order.id">@{{ order.name }}</h1>
	<h1 ng-if="!order.id">Новый заказ</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('orders') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ order.url }}" class="btn btn-primary" ng-if="order.id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="order.id" ng-click="delete(order.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block">
		<h2>Продукты</h2>

		<table class="table">
			<tr>
				<th>Название</th>
				<th>Цвет</th>
				<th>Цена</th>
				<th>Количество</th>
				<th>В наличии</th>
				<th>Стоимость</th>
				<th></th>
			</tr>

			<tr ng-repeat="productData in orderData.products track by $index">
				<td>
					<ui-select theme="bootstrap" ng-model="productData.product_group_id" ng-change="chooseProductGroup(productData, $select.selected.id)">
			            <ui-select-match placeholder="Название">
				            @{{ $select.selected.name }}
				        </ui-select-match>
			            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
			                <span ng-bind-html="productGroup.name | highlight: $select.search"></span>
			            </ui-select-choices>
					</ui-select>
				</td>
				<td>
					<ui-select theme="bootstrap" ng-model="productData.product_id" ng-change="chooseProduct(productData, $select.selected)">
			            <ui-select-match placeholder="Цвет">
				            @{{ $select.selected.color }}
				        </ui-select-match>
			            <ui-select-choices repeat="product.id as product in productData.products | filter: $select.search">
			                <span ng-bind-html="product.color | highlight: $select.search"></span>
			            </ui-select-choices>
					</ui-select>
				</td>
				<td>
					@{{ productData.price }} руб.
				</td>
				<td>
					<input type="text" class="form-control" ng-model="productData.pivot.count" ng-change="changeCount(productData, 0)"> м<sup>2</sup>
				</td>
				<td>
					@{{ productData.in_stock }} м<sup>2</sup>
				</td>
				<td>
					@{{ productData.pivot.cost }} руб.
				</td>
				<td>
					<button type="button" class="btn btn-primary" ng-click="deleteProduct($index)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>
				</td>
			</tr>
		</table>

		<button type="button" class="btn btn-primary" ng-click="addProduct()">
			<i class="fas fa-plus"></i> Добавить продукт	
		</button>

		<h2>Общая информация</h2>
		Стоимость: @{{ orderData.cost }} руб

		<h2>Данные о клиенте</h2>

		<div class="form-group">
			<label>Имя</label>
			<input type="text" class="form-control" ng-model="orderData.client.name">
		</div>

		<div class="form-group">
			<label>Номер телефона</label>
			<input type="text" class="form-control" ng-model="orderData.client.phone">
		</div>

		<div class="form-group">
			<label>Электронная почта</label>
			<input type="text" class="form-control" ng-model="orderData.client.email">
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save('{{ route('orders', [], false) }}')">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>