<div class="orders-page" ng-init="initShow()">
	<h1>Просмотр заказа</h1>

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
		<div class="row justify-content-around">
			<div class="col-11">
				<div class="show-block-title">
					Заказ №@{{ order.id }}
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
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-5">
				<div class="params-title">Данные клиента</div>
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

				<div class="params-title">
					Комментарий к заказу
				</div>
				<div class="param-block">
					<div class="param-value">
						<span ng-if="order.comment">@{{ order.comment }}</span>
						<span ng-if="!order.comment">Нет комментария</span>
					</div>
				</div>
			</div>
			
			<div class="col-5">
				<div class="params-title">Общая информация</div>
				<div class="param-block">
					<div class="param-name">
						Стоимость
					</div>
					<div class="param-value">
						@{{ order.cost | number }} руб.
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Вес
					</div>
					<div class="param-value">
						<span ng-if="order.weight">@{{ order.weight | number }} кг</span>
						<span ng-if="!order.weight">Нет данных</span>
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Количество поддонов
					</div>
					<div class="param-value">
						<span ng-if="order.weight">@{{ order.pallets | number }} шт</span>
						<span ng-if="!order.weight">Нет данных</span>
					</div>
				</div>
			</div>
		</div>
		

		<div class="row justify-content-around">
			<div class="col-11">
				<div class="params-title">Состав заказа</div>

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
						<td>
							@{{ product.pivot.price | number }} руб./
							<span ng-switch on="product.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</td>
						<td>
							@{{ product.pivot.count | number }} 
							<span ng-switch on="product.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</td>
						<td>@{{ product.pivot.cost | number }} руб.</td>
						<td class="product-progress-col">
							<div class="product-progress">
								<div class="progress-realization" ng-style="{'width': Math.round(product.progress.realization / product.progress.total * 100) + '%'}" ng-if="product.progress.realization">
									<div class="progress-number">
										@{{ product.progress.realization }}
										<span ng-switch on="product.category.units">
											<span ng-switch-when="area">м<sup>2</sup></span>
											<span ng-switch-when="volume">м<sup>3</sup></span>
											<span ng-switch-when="unit">шт.</span>
										</span>
									</div>
								</div>
								<div class="progress-ready" ng-style="{'width': Math.round(product.progress.ready / product.progress.total * 100) + '%'}" ng-if="product.progress.ready">
									<div class="progress-number">
										@{{ product.progress.ready }} 
										<span ng-switch on="product.category.units">
											<span ng-switch-when="area">м<sup>2</sup></span>
											<span ng-switch-when="volume">м<sup>3</sup></span>
											<span ng-switch-when="unit">шт.</span>
										</span>
									</div>
								</div>
								<div class="progress-left" ng-style="{'width': Math.round(product.progress.left / product.progress.total * 100) + '%'}" ng-if="product.progress.left">
									<div class="progress-number">
										@{{ product.progress.left }} 
										<span ng-switch on="product.category.units">
											<span ng-switch-when="area">м<sup>2</sup></span>
											<span ng-switch-when="volume">м<sup>3</sup></span>
											<span ng-switch-when="unit">шт.</span>
										</span>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-5">
				<div class="params-title">
					Производство
				</div>

				<table class="table">
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
						<td>
							@{{ production.performed }} 
							<span ng-switch on="production.product.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</td>
					</tr>
				</table>

				<a href="{{ route('productions') }}" class="btn btn-primary">
					<i class="far fa-calendar-check"></i> Перейти к плану производства
				</a>
			</div>

			<div class="col-5">
				<div class="params-title">
					Выдача
				</div>

				<table class="table">
					<tr>
						<th>Дата</th>
						<th>Продукт</th>
						<th>Количество</th>
					</tr>
					<tr ng-repeat="realization in order.realizations" ng-if="realization.performed > 0">
						<td>@{{ realization.formatted_date }}</td>
						<td>
							@{{ realization.product.product_group.name }} <br>
							@{{ realization.product.color_text }}
						</td>
						<td>
							@{{ realization.performed }} 
							<span ng-switch on="realization.product.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>