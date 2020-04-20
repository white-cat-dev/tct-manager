<div class="orders-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового заказа</h1>
	<h1 ng-if="id">Редактирование заказа</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('orders') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку заказов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ order.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': showAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('orders') }}" class="btn-link">перейти к списку заказов</a> или <a href="{{ route('order-create') }}" class="btn-link">создать новый заказ</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-5">
				<div class="params-title">Данные клиента</div>

				<div class="form-group">
					<div class="param-label">Имя</div>
					<input type="text" class="form-control" ng-model="order.client.name" ng-class="{'is-invalid': orderErrors.client.name}">
				</div>

				<div class="form-group">
					<div class="param-label">Номер телефона</div>
					<input type="text" class="form-control" ng-model="order.client.phone" ng-class="{'is-invalid': orderErrors.client.phone}">
				</div>

				<div class="form-group">
					<div class="param-label">Электронная почта</div>
					<input type="text" class="form-control" ng-model="order.client.email" ng-class="{'is-invalid': orderErrors.client.email}">
				</div>

				<div class="form-group">
					<div class="param-label">Комментарий к заказу</div>
					<textarea class="form-control" rows="3" ng-model="order.comment"></textarea>
				</div>
			</div>

			<div class="col-5">
				<div class="params-title">Общая информация</div>
				<div class="main-params-block">
					<div class="form-group">
						<div class="param-label">Стоимость заказа</div>
						@{{ order.cost | number }} руб.
					</div>

					<div class="form-group">
						<div class="param-label">Вес заказа</div>
						@{{ order.weight | number }} кг.
					</div>

					<div class="form-group">
						<div class="param-label">Количество поддонов</div>
						@{{ order.weight | number }} шт.
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">Приоритет заказа</div>

					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" ng-model="order.priority" id="radioLow" value="0">
						<label class="custom-control-label" for="radioLow">Низкий</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" ng-model="order.priority" id="radioNormal" value="1">
						<label class="custom-control-label" for="radioNormal">Обычный</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" ng-model="order.priority" id="radioHigh" value="2">
						<label class="custom-control-label" for="radioHigh">Высокий</label>
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-11">
				<div class="params-title">Состав заказа</div>

				<table class="table products-table table-with-buttons" ng-if="order.products.length > 0">
					<tr>
						<th>Название</th>
						<th>Цвет</th>
						<th>Количество</th>
						<th>В наличии</th>
						<th>Цена</th>
						<th>Стоимость</th>
						<th></th>
					</tr>

					<tr ng-repeat="product in order.products track by $index">
						<td>
							<ui-select theme="bootstrap" ng-model="product.product_group_id" ng-change="chooseProductGroup(product, $select.selected.id)">
					            <ui-select-match placeholder="Продукт...">
						            @{{ $select.selected.name }}
						        </ui-select-match>
					            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
					                <span ng-bind-html="productGroup.name | highlight: $select.search"></span>
					            </ui-select-choices>
							</ui-select>
						</td>
						<td>
							<ui-select theme="bootstrap" ng-model="product.product_id" ng-change="chooseProduct(product, $select.selected)" ng-show="product.products.length > 0">
					            <ui-select-match placeholder="Цвет...">
						            @{{ $select.selected.color_text }}
						        </ui-select-match>
					            <ui-select-choices repeat="product.id as product in product.products | filter: $select.search">
					                <span ng-bind-html="product.color_text | highlight: $select.search"></span>
					            </ui-select-choices>
							</ui-select>
						</td>
						<td>
							<input type="text" class="form-control" ng-model="product.pivot.count" ng-change="changeCount(product, 0)">
						</td>
						<td>
							<span ng-if="product.id">
								@{{ product.in_stock }} 
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</span>
						</td>
						<td>
							<span ng-if="product.id">
								@{{ product.price | number }} руб.
							</span>
						</td>
						<td>
							<span ng-if="product.id">
								@{{ product.pivot.cost | number }} руб.
							</span>
						</td>
						<td>
							<button type="button" class="btn btn-primary" ng-click="deleteProduct($index)">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>
				</table>

				<button type="button" class="btn btn-primary" ng-click="addProduct()">
					<i class="fas fa-plus"></i> Добавить продукт	
				</button>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>