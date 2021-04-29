<div class="orders-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового заказа</h1>
	<h1 ng-if="id">Редактирование заказа</h1>

	@include('partials.loading')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('orders') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> 
				<span class="d-none d-md-inline">Вернуться</span>
				<span class="d-none d-lg-inline">к списку заказов</span>
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ order.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> 
				<span class="d-none d-md-inline">Просмотреть</span>
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="loadExportFile(order)">
				<i class="fas fa-print"></i>
				<span class="d-none d-md-inline">Распечатать</span>
			</button>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(order)">
				<i class="far fa-trash-alt"></i> 
				<span class="d-none d-md-inline">Удалить</span>
			</button>
		</div>
	</div>

	<div class="edit-form-block" ng-show="!isLoading">
		<div class="row justify-content-around">
			<div class="col-12 col-md-6 col-xl-5 params-col">
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
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" ng-model="order.priority" id="radioVeryHigh" value="3">
						<label class="custom-control-label" for="radioVeryHigh">Очень высокий</label>
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
							Самовывоз
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
						<input type="text" class="form-control" ng-model="order.delivery_distance" ng-blur="updateOrderInfo()">
						<span>км за городом</span>
					</div>
				</div>		
			</div>

			<div class="col-12 col-md-6 col-xl-5 params-col">
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

			<div class="col-12 col-xl-11 params-col">
				<div class="params-title">Состав заказа</div>

				<table class="table order-products-table table-with-buttons d-none d-lg-table">
					<tr>
						<th>Название</th>
						<th>Вид</th>
						<th>Цена</th>
						<th>Количество</th>
						<th>Стоимость</th>
						<th></th>
					</tr>

					<tr ng-repeat="product in order.products track by $index">
						<td>
							<ui-select ng-model="product.product_group_id" ng-change="chooseProductGroup(product, $select.selected)" skip-focusser="true">
					            <ui-select-match placeholder="Выберите из списка...">
						            @{{ $select.selected.name }} @{{ $select.selected.size }}
						        </ui-select-match>
					            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
					                <span ng-bind-html="productGroup.name + ' ' + productGroup.size | highlight: $select.search"></span>
					            </ui-select-choices>
							</ui-select>
						</td>
						<td class="d-none d-lg-table-cell">
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
							<span class="empty-select" ng-if="!product.category">
							</span>
							<span class="empty-select" ng-if="product.category && !product.category.variations">
								—
							</span>
						</td>
						<td>
							<div class="form-group-units">
								<input type="text" class="form-control" ng-model="product.pivot.price" ng-change="inputFloat(product.pivot, 'price')" ng-blur="product.pivot.manual_price = product.pivot.price; updateOrderInfo()" ng-disabled="!product.id">
								<span class="units">
									@{{ product.pivot.price }} <span>руб</span>
								</span>
							</div>
						</td>
						<td>
							<div class="form-group-units">
								<input type="text" class="form-control" ng-model="product.pivot.count" ng-disabled="!product.id" ng-change="inputFloat(product.pivot, 'count')" ng-blur="updateOrderInfo()">
								<span class="units">
									@{{ product.pivot.count }} <span ng-bind-html="product.units_text"></span>
								</span>
							</div>
							<div class="product-stock">
								<div>В наличии: @{{ product.in_stock ? product.in_stock : 0 }} <span ng-bind-html="product.units_text"></span></div>
							</div>
						</td>
						<td class="number-col">
							@{{ product.pivot.cost | number }} руб
						</td>
						<td>
							<button type="button" class="btn btn-primary" ng-click="deleteProduct($index)">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>

					<tr>
						<td colspan="5">
							<div class="form-group">
								<button type="button" class="btn btn-primary" ng-click="addProduct()">
									<i class="fas fa-plus"></i> Добавить продукт	
								</button>
							</div>
						</td>
						<td></td>
					</tr>

					<tr>
						<td class="border-0">
							<span class="empty-select text-left">Поддоны</span>
						</td>
						<td class="border-0">
							<span class="empty-select">
								—
							</span>
						</td>
						<td class="border-0">
							<div class="form-group-units">
								<input type="text" class="form-control" ng-model="order.pallets_price" ng-change="inputFloat(order, 'pallets_price')" ng-blur="order.manual_pallets_price = order.pallets_price; updateOrderInfo()">
								<span class="units">
									@{{ order.pallets_price }} <span>руб</span>
								</span>
							</div>
						</td>
						<td class="border-0">
							<div class="form-group-units">
								<input type="text" class="form-control" ng-model="order.pallets" ng-change="inputFloat(order, 'pallets')" ng-blur="order.manual_pallets = order.pallets; updateOrderInfo()">
								<span class="units">
									@{{ order.pallets }} <span>шт</span>
								</span>
							</div>
						</td>
						<td class="number-col border-0">
							@{{ order.pallets * order.pallets_price | number }} руб
						</td>
						<td class="border-0"></td>
					</tr>

					<tr>
						<td>
							<span class="empty-select text-left">Доставка</span>
						</td>
						<td>
							<span class="empty-select">
								—
							</span>
						</td>
						<td>
							<div class="form-group-units">
								<input type="text" class="form-control" ng-model="order.delivery_price" ng-change="inputFloat(order, 'delivery_price')" ng-blur="order.manual_delivery_price = order.delivery_price; updateOrderInfo()">
								<span class="units">
									@{{ order.delivery_price }} <span>руб</span>
								</span>
							</div>
						</td>
						<td>
							<span class="empty-select">
								—
							</span>
						</td>
						<td class="number-col">
							@{{ order.delivery_price | number }} <span>руб</span>
						</td>
						<td></td>
					</tr>
				</table>


				<div class="order-product-block d-block d-lg-none" ng-repeat="product in order.products track by $index">
					<div class="form-group">
						<ui-select ng-model="product.product_group_id" ng-change="chooseProductGroup(product, $select.selected)" skip-focusser="true">
				            <ui-select-match placeholder="Выберите из списка...">
					            @{{ $select.selected.name }} @{{ $select.selected.size }}
					        </ui-select-match>
				            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
				                <span ng-bind-html="productGroup.name + ' ' + productGroup.size | highlight: $select.search"></span>
				            </ui-select-choices>
						</ui-select>
					</div>
					<div class="form-group" ng-if="product.category && product.category.variations">
						<ui-select ng-model="product.product_id" ng-change="chooseProduct(product, $select.selected)" skip-focusser="true">
				            <ui-select-match placeholder="Выберите...">
					            @{{ $select.selected.variation_text }}
					        </ui-select-match>
				            <ui-select-choices repeat="product.id as product in product.products | filter: $select.search">
				                <span ng-bind-html="product.variation_text | highlight: $select.search"></span>
				            </ui-select-choices>
						</ui-select>
					</div>
					<table class="table table-sm">
						<tr>
							<th>Цена</th>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control" ng-model="product.pivot.price" ng-change="inputFloat(product.pivot, 'price')" ng-blur="product.pivot.manual_price = product.pivot.price; updateOrderInfo()" ng-disabled="!product.id">
									<span class="units">
										@{{ product.pivot.price }} <span>руб</span>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>Количество</th>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control" ng-model="product.pivot.price" ng-change="inputFloat(product.pivot, 'price')" ng-blur="product.pivot.manual_price = product.pivot.price; updateOrderInfo()" ng-disabled="!product.id">
									<span class="units">
										@{{ product.pivot.price }} <span>руб</span>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>Стоимость</th>
							<td>
								@{{ product.pivot.cost | number }} руб
							</td>
						</tr>
					</table>
				</div>
				<button type="button" class="btn btn-primary d-block d-lg-none" ng-click="addProduct()">
					<i class="fas fa-plus"></i> Добавить продукт	
				</button>
			</div>

			<div class="col-12 col-md-5 col-xl-4 params-col">
				<div class="params-title mb-3">Итоговая информация</div>

				<div class="form-group">
					<span class="param-label">Вес заказа: </span>@{{ order.weight | number }} кг
				</div>

				<div class="form-group">
					<span class="param-label">Стоимость заказа: </span>@{{ order.cost | number }} руб
				</div>
			</div>

			<div class="col-12 col-md-7 col-xl-6 params-col">
				<div class="params-title mb-3">Производство заказа</div>

				<div class="form-group">
					<div class="order-form-group">
						<div class="param-label">Дата готовности</div>
						<input type="text" class="form-control" ng-model="order.date_to_raw" ui-mask="99.99.9999">
					</div>

					<button type="button" class="btn btn-primary" ng-if="!id"  ng-click="getDate()" ng-disabled="isAddSaving">
						<span ng-if="isAddSaving">
							<i class="fa fa-spinner fa-spin"></i> Рассчет даты готовности
						</span>
						<span ng-if="!isAddSaving">
							<i class="far fa-calendar-check"></i> Рассчитать дату готовности
						</span>
					</button>
				</div>
			</div>

			<div class="col-5 col-xl-4 params-col" ng-show="!id">
				<div class="params-title mt-3 mb-3">Оплата заказа</div>

				<div class="order-form-group">
					<div class="param-label">Оплачено:</div>
					<div class="form-group-units">
						<input type="text" class="form-control" ng-model="order.paid" ng-change="inputFloat(order, 'paid')" ng-blur="checkFullPayment()">
						<span class="units">
							@{{ order.paid }} <span>руб</span>
						</span>
					</div>
				</div>

				<div class="custom-control custom-checkbox mb-3">
					<input type="checkbox" class="custom-control-input" ng-model="isFullPaymentChosen" ng-change="chooseFullPayment()" id="checkboxPayment">
					<label class="custom-control-label" for="checkboxPayment">
						Полная оплата
					</label>
				</div>
			</div>

			<div class="col-7 col-xl-6 params-col" ng-show="!id">
				<div class="params-title mt-3 mb-3">Выдача заказа</div>

				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" ng-model="isOrderAllRealizationsChosen" ng-change="chooseOrderRealizations(true)" id="checkboxAllRealizations" ng-disabled="!isOrderAllRealizationsActive">
					<label class="custom-control-label" for="checkboxAllRealizations">
						Отпустить полностью
					</label>
				</div>

				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" ng-model="isOrderPartRealizationsChosen" ng-change="chooseOrderRealizations(false)" id="checkboxPartRealizations" ng-disabled="!isOrderPartRealizationsActive">
					<label class="custom-control-label" for="checkboxPartRealizations">
						Отпустить частично
					</label>
				</div>

				<table class="table table-sm order-realizations-table" ng-if="isOrderPartRealizationsChosen">
					<tr>
						<th>Продукт</th>
						<th>Кол-во</th>
						<th>В наличии</th>
						<th>Отпустить</th>
					</tr>
					<tr ng-repeat="product in order.products" ng-if="product.id">
						<td>
							@{{ product.product_group.name }}
							@{{ product.product_group.size }}
							<div class="product-color" ng-if="product.variation_noun_text">
								@{{product.variation_noun_text }}
							</div>		
						</td>

						<td>
							@{{ product.pivot.count }} <span ng-bind-html="product.units_text"></span>
						</td>

						<td>
							@{{ product.in_stock }} <span ng-bind-html="product.units_text"></span>
						</td>

						<td>
							<div class="form-group-units">
								<input type="text" class="form-control form-control-sm" ng-model="product.pivot.realization_performed" ng-change="inputFloat(product.pivot, 'realization_performed')"> 
								<div class="units">
									@{{ product.pivot.realization_performed }} <span ng-bind-html="product.units_text"></span>
								</div>
							</div>
						</td>
					</tr>
					<tr ng-if="order.pallets > 0">
						<td>
							Поддоны	
						</td>

						<td>
							@{{ order.pallets }} шт
						</td>

						<td>
							—
						</td>

						<td>
							<div class="form-group-units">
								<input type="text" class="form-control form-control-sm" ng-model="order.pallets_realization_performed" ng-change="inputFloat(order, 'pallets_realization_performed')"> 
								<div class="units">
									@{{ order.pallets_realization_performed }} <span>шт</span>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()" ng-disabled="isSaving">
				<span ng-if="isSaving">
					<i class="fa fa-spinner fa-spin"></i> Сохранение...
				</span>
				<span ng-if="!isSaving">
					<i class="fas fa-save"></i> Сохранить и выйти
				</span>
			</button>
		</div>
	</div>

	@include('partials.delete-modal')
</div>