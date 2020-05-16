<div class="orders-page" ng-init="init()">
	<h1>Заказы</h1>

	@include('partials.top-alerts')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> Поиск
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			<a href="{{ route('order-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать заказ
			</a>
		</div>
	</div>

	<div class="statuses-menu-block">
		<button type="button" class="btn" ng-class="{'active': currentStatuses.indexOf({{ App\Order::STATUS_PRODUCTION }}) != -1 }" ng-click="chooseStatus({{ App\Order::STATUS_PRODUCTION }})">
			В работе
		</button>
		<button type="button" class="btn" ng-class="{'active': currentStatuses.indexOf({{ App\Order::STATUS_READY }}) != -1}" ng-click="chooseStatus({{ App\Order::STATUS_READY }})">
			Готовые к выдаче
		</button>
		<button type="button" class="btn" ng-class="{'active': currentStatuses.indexOf({{ App\Order::STATUS_FINISHED }}) != -1 }" ng-click="chooseStatus({{ App\Order::STATUS_FINISHED }})">
			Завершенные
		</button>
	</div>

	<div class="row">
		<div class="col-12 col-md-6 col-lg-4" ng-repeat="order in orders | filter: searchQuery">
			<div class="order-block">
				<div class="btn-group">
					<a ng-href="@{{ order.url }}" class="btn btn-primary btn-sm">
						<i class="fas fa-eye"></i>
					</a>
					<a ng-href="@{{ order.url + '/edit' }}" class="btn btn-primary btn-sm">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary btn-sm" ng-click="delete(order.id)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>

				<div class="order-title">
					Заказ №@{{ order.number }}
					<div class="order-date">@{{ order.formatted_date }}</div>
				</div>
				<div class="row align-items-center">
					<div class="col-5">
						<div class="order-icon">
							<img src="{{ url('/images/order.png')}}">
							<img src="{{ url('/images/order-ready.png')}}" ng-style="{'height': Math.round(order.progress.production / order.progress.total * 100) + '%'}">
							<img src="{{ url('/images/order-realize.png')}}" ng-style="{'height': Math.round(order.progress.realization / order.progress.total * 100) + '%'}">
						</div>

						<div class="order-status">
							@{{ order.status_text }}
						</div>
					</div>
					<div class="col-7">
						<div class="order-params">
							<div class="param-name">
								Стоимость
							</div>
							<div class="param-value">
								@{{ order.cost | number }} руб.
							</div>

							<div class="param-name">
								Данные клиента
							</div>
							<div class="param-value">
								<div ng-if="order.client.name">@{{ order.client.name }}</div>
								<div ng-if="order.client.phone">@{{ order.client.phone }}</div>
								<div ng-if="order.client.email">@{{ order.client.email }}</div>
								<div ng-if="!order.client.name && !order.client.phone && !order.client.email">Нет данных</div>
							</div>
						</div>
					</div>
				</div>

				<div class="order-params">
					<div class="param-name">
						Состав заказа
					</div>

					<table class="table table-sm" ng-if="order.products.length > 0">
						<tr>
							<th>Продукт</th>
							<th>Кол-во</th>
							<th>Прогресс</th>
						</tr>
						<tr ng-repeat="product in order.products">
							<td>
								<div>@{{ product.product_group.name }}</div>
								<div>@{{ product.product_group.size }}</div>
								<div class="product-color">@{{ product.variation_noun_text }}</div>
							</td>
							<td>
								@{{ product.progress.total }}
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</td>
							<td>
								<div class="product-progress">
									<div class="progress-realization" ng-style="{'width': Math.round(product.progress.realization / product.progress.total * 100) + '%'}" ng-if="product.progress.realization">
										<div class="progress-number">@{{ product.progress.realization }}</div>
									</div>
									<div class="progress-ready" ng-style="{'width': Math.round(product.progress.ready / product.progress.total * 100) + '%'}" ng-if="product.progress.ready">
										<div class="progress-number">@{{ product.progress.ready }}</div>
									</div>
									<div class="progress-left" ng-style="{'width': Math.round(product.progress.left / product.progress.total * 100) + '%'}" ng-if="product.progress.left">
										<div class="progress-number">@{{ product.progress.left }}</div>
									</div>
								</div>
							</td>
						</tr>
					</table>

					<div ng-if="order.products.length == 0">
						Пустой заказ
					</div>
				</div>

				<div class="buttons-block">
					<button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-cog"></i> Доступные действия
					</button>
					<div class="dropdown-menu" aria-labelledby="actionsButton">
						<button type="button" class="btn-sm dropdown-item" ng-click="showRealizationModal(order)">
							Отпустить заказ
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12">
			<div class="no-data-block" ng-if="orders.length == 0">
				<div>
					<i class="fas fa-th"></i>
				</div>
				Вы еще не добавили ни одного заказа
			</div>
		</div>
	</div>

	@include('partials.order-realization-modal')
</div>