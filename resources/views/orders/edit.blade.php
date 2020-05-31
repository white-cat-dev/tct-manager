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
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(order)">
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
			<div class="col-6 col-xl-5">
				<div class="params-title">Общая информация</div>

				<div class="form-group">
					<div class="param-label">Номер заказа</div>
					<input type="text" class="form-control" ng-model="order.number">
				</div>

				<div class="form-group">
					<div class="param-label">Дата принятия</div>
					<input type="text" class="form-control" ng-model="order.date_raw" ui-mask="99.99.9999">
				</div>

				<div class="form-group">
					<div class="param-label">Приоритет заказа</div>

					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" ng-model="order.priority" id="radioNormal" value="1">
						<label class="custom-control-label" for="radioNormal">Обычный</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" ng-model="order.priority" id="radioHigh" value="2">
						<label class="custom-control-label" for="radioHigh">Высокий</label>
					</div>
				</div>	

				<div class="form-group">
					<div class="param-label">Способ оплаты</div>

					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioCash" ng-model="order.pay_type" value="cash" ng-change="updateOrderInfo()">
						<label class="custom-control-label" for="radioCash">
							Наличный
						</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioCashless" ng-model="order.pay_type" value="cashless" ng-change="updateOrderInfo()">
						<label class="custom-control-label" for="radioCashless">
							Безнал
						</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioVat" ng-model="order.pay_type" value="vat" ng-change="updateOrderInfo()">
						<label class="custom-control-label" for="radioVat">
							НДС
						</label>
					</div>
				</div>	

				<div class="form-group">
					<div class="param-label">Доставка</div>

					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioNone" ng-model="order.delivery" value="" ng-change="updateOrderInfo()">
						<label class="custom-control-label" for="radioNone">
							Без доставки
						</label>
					</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioSverdlovsk" ng-model="order.delivery" value="sverdlovsk" ng-change="updateOrderInfo()">
						<label class="custom-control-label" for="radioSverdlovsk">
							Свердловский район
						</label>
					</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioOther" ng-model="order.delivery" value="other" ng-change="updateOrderInfo()">
						<label class="custom-control-label" for="radioOther">
							Другой район
						</label>
					</div>

					<div class="delivery-distance-block">
						<input type="text" class="form-control" ng-model="order.delivery_distance" ng-change="updateOrderInfo()">
						<span>км за городом</span>
					</div>
				</div>		
			</div>

			<div class="col-6 col-xl-5">
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
		</div>
		
		<div class="params-section">
			<div class="row justify-content-around">
				<div class="col-12 col-xl-11">
					<div class="params-title">Состав заказа</div>

					<table class="table products-table table-with-buttons" ng-if="order.products.length > 0">
						<tr>
							<th>Название</th>
							<th>Разновидность</th>
							<th>Количество</th>
							<th>Цена</th>
							<th>Стоимость</th>
							<th></th>
						</tr>

						<tr ng-repeat="product in order.products track by $index">
							<td style="width: 30%;">
								<ui-select ng-model="product.product_group_id" ng-change="chooseProductGroup(product, $select.selected)" skip-focusser="true">
						            <ui-select-match placeholder="Выберите из списка...">
							            @{{ $select.selected.name }} @{{ $select.selected.size }}
							        </ui-select-match>
						            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
						                <span ng-bind-html="productGroup.name + ' ' + productGroup.size | highlight: $select.search"></span>
						            </ui-select-choices>
								</ui-select>
							</td>
							<td style="width: 20%;">
								<span ng-if="product.category && product.category.variations">
									<ui-select ng-model="product.product_id" ng-change="chooseProduct(product, $select.selected)" skip-focusser="true">
							            <ui-select-match placeholder="Выберите...">
								            @{{ $select.selected.variation_text }}
								        </ui-select-match>
							            <ui-select-choices repeat="product.id as product in product.products | filter: $select.search">
							                <span ng-bind-html="product.variation_text | highlight: $select.search"></span>
							            </ui-select-choices>
									</ui-select>
								</span>
								<span ng-if="product.category && !product.category.variations">
									—
								</span>
							</td>
							<td style="width: 15%;">
								<span ng-if="product.id">
									<input type="text" class="form-control" ng-model="product.pivot.count" ng-change="updateCount(product)">
									<span class="product-units">
										@{{ product.pivot.count }} <span ng-bind-html="product.units_text"></span>
									</span>
									<div class="product-stock" ng-if="product.id">
										<div>В наличии: @{{ product.in_stock }} <span ng-bind-html="product.units_text"></span></div>
										<div>Свободно: @{{ product.free_in_stock }} <span ng-bind-html="product.units_text"></span></div>
									</div>
								</span>
							</td>
							<td class="number-col" style="width: 15%;">
								<span ng-if="product.id">
									<span ng-switch on="order.pay_type">
										<span ng-switch-when="cash">
											@{{ product.pivot.price | number }} руб/<span ng-bind-html="product.units_text"></span>
										</span>
										<span ng-switch-when="cashless">
											@{{ product.pivot.price_cashless | number }} руб/<span ng-bind-html="product.units_text"></span>
										</span>
										<span ng-switch-when="vat">
											@{{ product.pivot.price_vat | number }} руб/<span ng-bind-html="product.units_text"></span>
										</span>
									</span>
								</span>
							</td>
							<td class="number-col" style="width: 15%;">
								<span ng-if="product.id">
									@{{ product.pivot.cost | number }} руб
								</span>
							</td>
							<td>
								<button type="button" class="btn btn-primary" ng-click="deleteProduct($index)">
									<i class="far fa-trash-alt"></i>
								</button>
							</td>
						</tr>
					</table>

					<div class="form-group">
						<button type="button" class="btn btn-primary" ng-click="addProduct()">
							<i class="fas fa-plus"></i> Добавить продукт	
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="params-section">
			<div class="row justify-content-around">
				<div class="col-6 col-xl-5">
					<div class="params-title">Итоговая информация</div>

					<div class="form-group">
						<div class="param-label">Количество поддонов, шт</div>
						<input type="text" class="form-control" ng-model="order.pallets" ng-change="updateOrderInfo(true)">
					</div>

					<div class="form-group">
						<div class="param-label">Вес заказа</div>
						@{{ order.weight | number }} кг
					</div>

					<div class="form-group">
						<div class="param-label">Стоимость заказа</div>
						@{{ order.cost | number }} руб
					</div>

					<div class="form-group">
						<div class="param-label">Оплачено, руб</div>
						<input type="text" class="form-control" ng-model="order.paid">
					</div>
				</div>

				<div class="col-6 col-xl-5">

				</div>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>

	@include('partials.delete-modal')
</div>