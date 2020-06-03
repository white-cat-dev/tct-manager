<div class="orders-page" ng-init="initShow()">
	<h1>Просмотр заказа</h1>

	@include('partials.loading')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('orders') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку заказов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ order.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="order.id" ng-click="showDelete(order)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-around">
			<div class="col-12 col-xl-11">
				<div class="show-block-title m-0">
					Заказ №@{{ order.number }} от @{{ order.formatted_date }}

					<div class="status-block">
						@{{ order.status_text }}
					</div>
				</div>
			</div>

			<div class="col-6 col-xl-5">
				<div class="params-title">Общая информация</div>
				<div class="param-block">
					<div class="param-name">
						Номер заказа
					</div>
					<div class="param-value">
						@{{ order.number }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Дата принятия
					</div>
					<div class="param-value">
						@{{ order.formatted_date }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Приоритет заказа
					</div>
					<div class="param-value">
						@{{ order.priority_text }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Способ оплаты
					</div>
					<div class="param-value">
						@{{ order.pay_type_text }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Доставка
					</div>
					<div class="param-value">
						@{{ order.delivery_text }}
					</div>
				</div>
			</div>

			<div class="col-6 col-xl-5">
				<div class="params-title">Данные клиента</div>
				<div class="param-block">
					<div class="param-name">
						Имя
					</div>
					<div class="param-value">
						<span ng-if="order.client.name">
							@{{ order.client.name }}
						</span>
						<span ng-if="!order.client.name">
							Не указано
						</span>
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Телефон
					</div>
					<div class="param-value">
						<span ng-if="order.client.phone">
							@{{ order.client.phone }}
						</span>
						<span ng-if="!order.client.phone">
							Не указан
						</span>
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						E-mail
					</div>
					<div class="param-value">
						<span ng-if="order.client.email">
							@{{ order.client.email }}
						</span>
						<span ng-if="!order.client.email">
							Не указана
						</span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Комментарий к заказу
					</div>
					<div class="param-value">
						<span ng-if="order.comment">@{{ order.comment }}</span>
						<span ng-if="!order.comment">Нет комментария</span>
					</div>
				</div>
			</div>
		</div>
		

		<div class="row justify-content-around">
			<div class="col-12 col-xl-11">
				<div class="params-title">Состав заказа</div>

				<table class="table">
					<tr>
						<th>Название</th>
						<th>Вид</th>
						<th>Цена</th>
						<th>Количество</th>
						<th>Стоимость</th>
						<th>Отпущено</th>
						<th>Готово</th>
					</tr>
					<tr ng-repeat="product in order.products">
						<td>
							@{{ product.product_group.name }} @{{ product.product_group.size }}
						</td>
						<td>
							@{{ product.variation_text }}
						</td>
						<td>
							@{{ product.pivot.price | number }} руб/<span ng-bind-html="product.units_text"></span>
						</td>
						<td>
							@{{ product.pivot.count | number }} <span ng-bind-html="product.units_text"></span>
						</td>
						<td>
							@{{ product.pivot.cost | number }} руб
						</td>
						<td>
							@{{ product.progress.realization | number }} <span ng-bind-html="product.units_text"></span>
						</td>
						<td>
							@{{ product.progress.ready | number }} <span ng-bind-html="product.units_text"></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-6 col-xl-5">
				<div class="params-title">
					Итоговая информация
				</div>

				<div class="param-block">
					<div class="param-name">
						Поддоны
					</div>
					<div class="param-value">
						@{{ order.pallets | number }} шт по @{{ order.pallets_price | number }} руб
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Вес заказа
					</div>
					<div class="param-value">
						@{{ order.weight | number }} кг
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Стоимость заказа
					</div>
					<div class="param-value">
						@{{ order.cost | number }} руб
					</div>
				</div>
			</div>

			<div class="col-6 col-xl-5">
				
			</div>


			<div class="col-6 col-xl-5">
				<div class="params-title">
					История оплаты
				</div>

				<div class="alert alert-secondary">
					<table class="table table-sm" ng-if="order.payments.length > 0">
						<tr>
							<th>Дата</th>
							<th>Сумма</th>
						</tr>
						<tr ng-repeat="payment in order.payments">
							<td>
								@{{ payment.formatted_date }}
							</td>
							<td>
								@{{ payment.paid | number }} руб
							</td>
						</tr>
						<tr>
							<td>
								Итого:
							</td>
							<td>
								@{{ order.paid | number }} руб
							</td>
						</tr>
					</table>

					<div ng-if="order.payments.length == 0">
						<i class="far fa-calendar-times"></i> Оплат заказа еще не поступало
					</div>
				</div>
			</div>

			<div class="col-6 col-xl-5">
				<div class="params-title">
					История выдачи
				</div>

				<div class="alert alert-secondary">
					<table class="table table-sm" ng-if="(order.realizations | filter: {'date': '!= null'}).length > 0">
						<tr>
							<th>Дата</th>
							<th>Продукт</th>
							<th>Количество</th>
						</tr>
						<tr ng-repeat="realization in order.realizations | filter: {'date': '!= null'}">
							<td>@{{ realization.formatted_date }}</td>
							<td>
								@{{ realization.product.product_group.name }} @{{ production.product.product_group.size }}<br>
								@{{ realization.product.color_text }}
							</td>
							<td>
								@{{ realization.performed }} <span ng-bind-html="realization.product.units_text"></span>
							</td>
						</tr>
					</table>

					<div ng-if="(order.realizations | filter: {'date': '!= null'}).length == 0">
						<i class="far fa-calendar-times"></i> Заказ еще не выдан
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('partials.delete-modal')
</div>