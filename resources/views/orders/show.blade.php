<div class="order-block" ng-init="initShow()">
	<h1></h1>
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('orders') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ order.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="order.id" ng-click="delete(order.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>


	<div class="show-block">
		<div class="row">
			<div class="col-6">
				<div class="show-block-title">
					Заказ №@{{ order.id }}
				</div>
			</div>
			<div class="col-6">
				<div class="order-status">
					@{{ order.status_text }}
				</div>
			</div>
		</div>
		<div class="order-progress">
			<div class="progress-realization" ng-style="{'width': Math.round(order.progress.realization / order.progress.total * 100) + '%'}" ng-if="order.progress.realization">
				<div class="progress-number">@{{ Math.round(order.progress.realization / order.progress.total * 100) }} %</div>
				<div class="progress-label">Выдано</div>
			</div>
			<div class="progress-ready" ng-style="{'width': Math.round(order.progress.ready / order.progress.total * 100) + '%'}" ng-if="order.progress.production">
				<div class="progress-number">@{{ Math.round(order.progress.ready / order.progress.total * 100) }} %</div>
				<div class="progress-label">Готово</div>
			</div>
		</div>

		<div class="row">
			<div class="col-6">
				<div class="params-title">Данные клиента</div>
				<div class="params-block">
					<div class="param-block">
						<div class="param-name">
							Имя
						</div>
						<div class="param-value">
							@{{ order.client.name }}
						</div>
					</div>
					<div class="param-block">
						<div class="param-name">
							Телефон
						</div>
						<div class="param-value">
							@{{ order.client.phone }}
						</div>
					</div>
					<div class="param-block">
						<div class="param-name">
							E-mail
						</div>
						<div class="param-value">
							@{{ order.client.email }}
						</div>
					</div>
				</div>

				<div class="params-title">
					Комментарий к заказу
				</div>
				<div class="params-block">
					<div class="param-block">
						<div class="param-value">
							<span ng-if="order.comment">@{{ order.comment }}</span>
							<span ng-if="!order.comment">Нет комментария</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="params-title">Общая информация</div>
				<div class="params-block">
					<div class="param-block">
						<div class="param-name">
							Стоимость
						</div>
						<div class="param-value">
							@{{ order.cost }}
						</div>
					</div>
					<div class="param-block">
						<div class="param-name">
							Вес
						</div>
						<div class="param-value">
							<span ng-if="order.weight">@{{ order.weight }}</span>
							<span ng-if="!order.weight">Нет данных</span>
						</div>
					</div>
					<div class="param-block">
						<div class="param-name">
							Количество поддонов
						</div>
						<div class="param-value">
							<span ng-if="order.weight">@{{ order.pallets }}</span>
							<span ng-if="!order.weight">Нет данных</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="params-title">Состав заказа</div>

		<div class="params-block">
			<table class="table">
				<tr>
					<th>Название</th>
					<th>Цвет</th>
					<th>Цена</th>
					<th>Количество</th>
					<th>Стоимость</th>
					<th>Прогресс</th>
				</tr>
				<tr ng-repeat="product in order.products">
					<td>@{{ product.product_group.name }}</td>
					<td>@{{ product.color_text }}</td>
					<td>@{{ product.pivot.price }} руб.</td>
					<td>@{{ product.pivot.count }} м<sup>2</sup></td>
					<td>@{{ product.pivot.cost }} руб.</td>
					<td class="product-progress-col">
						<div class="product-progress">
							<div class="progress-realization" ng-style="{'width': Math.round(product.progress.realization / product.progress.total * 100) + '%'}" ng-if="product.progress.realization">
								<div class="progress-number">@{{ product.progress.realization }} м<sup>2</sup></div>
							</div>
							<div class="progress-ready" ng-style="{'width': Math.round(product.progress.ready / product.progress.total * 100) + '%'}" ng-if="product.progress.ready">
								<div class="progress-number">@{{ product.progress.ready }} м<sup>2</sup></div>
							</div>
							<div class="progress-left" ng-style="{'width': Math.round(product.progress.left / product.progress.total * 100) + '%'}" ng-if="product.progress.left">
								<div class="progress-number">@{{ product.progress.left }} м<sup>2</sup></div>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>

		<div class="params-title">
			История заказа
		</div>

		<div class="row">
			<div class="col-6">
				<div class="param-name">
					Производство
				</div>

				<div class="params-block">
					<table class="table" ng-if="order.productions.length > 0">
						<tr>
							<th>Дата</th>
							<th>Продукт</th>
							<th>Количество</th>
						</tr>
						<tr ng-repeat="production in order.productions">
							<td>@{{ production.formatted_date }}</td>
							<td>
								@{{ production.product.product_group.name }} <br>
								@{{ production.product.color_text }}
							</td>
							<td>@{{ production.performed }} м<sup>2</sup></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-6">
				<div class="param-name">
					Реализация
				</div>
				<div class="params-block">
					<table class="table" ng-if="order.realizations.length > 0">
						<tr>
							<th>Дата</th>
							<th>Продукт</th>
							<th>Количество</th>
						</tr>
						<tr ng-repeat="realization in order.realizations">
							<td>@{{ realization.formatted_date }}</td>
							<td>
								@{{ realization.product.product_group.name }} <br>
								@{{ realization.product.color_text }}
							</td>
							<td>@{{ realization.performed }} м<sup>2</sup></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>