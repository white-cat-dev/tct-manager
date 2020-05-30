<div class="orders-page" ng-init="init({{ App\Order::STATUS_PRODUCTION }})">
	<h1>Заказы</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery" ng-keypress="searchInputKeyPressed($event)">
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
		<button type="button" class="btn" ng-class="{'active': currentStatus == 0 }" ng-click="chooseStatus(0)">
			Все заказы
		</button>
		<button type="button" class="btn" ng-class="{'active': currentStatus == {{ App\Order::STATUS_PRODUCTION }} }" ng-click="chooseStatus({{ App\Order::STATUS_PRODUCTION }})">
			В работе
		</button>
		<button type="button" class="btn" ng-class="{'active': currentStatus == {{ App\Order::STATUS_READY }} }" ng-click="chooseStatus({{ App\Order::STATUS_READY }})">
			Готовые к выдаче
		</button>
		<button type="button" class="btn" ng-class="{'active': currentStatus == {{ App\Order::STATUS_FINISHED }} }" ng-click="chooseStatus({{ App\Order::STATUS_FINISHED }})">
			Завершенные
		</button>
	</div>

	<div class="row" ng-if="(orders | filter: {'number': searchQuery}).length > 0">
		<div class="col-12 col-lg-7">
			<div class="main-orders-block">
				<table class="table">
					<tr>
						<th>Номер</th>
						<th>Дата принятия</th>
						<th>Дата готовности</th>
						<th>Стоимость</th>
						<th>Оплачено</th>
					</tr>
					<tr ng-repeat="order in orders | filter: {'number': searchQuery}" ng-click="chooseOrder(order)" ng-class="{'active': currentOrder.id == order.id}">
						<td>
							<div class="order-name">
								@{{ order.number }}
								<div class="order-priority" ng-if="order.priority == {{ App\Order::PRIORITY_HIGH }}">
									Важно
								</div>
							</div>
						</td>
						<td>
							@{{ order.formatted_date }}
						</td>
						<td>
							@{{ order.formatted_date_to }}
						</td>
						<td>
							@{{ order.cost | number }} руб.
						</td>
						<td>
							@{{ order.paid | number }} руб.
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="col-12 col-lg-5">
			<div class="main-products-block">
				<div ng-if="currentOrder">
					<div class="title-block">
						Заказ №@{{ currentOrder.number }}
					</div>

					<div class="btn-group">
						<a ng-href="@{{ currentOrder.url }}" class="btn btn-primary btn-sm">
							<i class="fas fa-eye"></i>
						</a>
						<a ng-href="@{{ currentOrder.url + '/edit' }}" class="btn btn-primary btn-sm">
							<i class="fas fa-edit"></i>
						</a>
						<button type="button" class="btn btn-primary btn-sm" ng-click="showDelete(currentOrder)">
							<i class="far fa-trash-alt"></i>
						</button>
					</div>

					<table class="table table-sm" ng-if="currentOrder.products.length > 0">
						<tr>
							<th>Продукт</th>
							<th>Кол-во</th>
							<th>Отпущено</th>
							<th>Готово</th>
						</tr>
						<tr ng-repeat="product in currentOrder.products">
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
								@{{ product.progress.realization }}
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</td>
							<td>
								@{{ product.progress.ready }}
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</td>
						</tr>
					</table>

					<div class="buttons-block">
						<button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-cog"></i> Доступные действия
						</button>
						<div class="dropdown-menu" aria-labelledby="actionsButton">
							<button type="button" class="btn-sm dropdown-item" ng-click="showRealizationModal(currentOrder)">
								Отпустить заказ
							</button>
						</div>
					</div>
				</div>

				<div ng-if="!currentOrder">
					<div class="no-current-order">
						<div class="icon">
							<i class="fas fa-shopping-cart"></i>
						</div>
						Выберите заказ в таблице слева
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="no-data-block" ng-if="(orders | filter: {'number': searchQuery}).length == 0">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного
		<span ng-switch on="currentStatus">
			<span ng-switch-when="{{ App\Order::STATUS_PRODUCTION }}">заказа в работе</span>
			<span ng-switch-when="{{ App\Order::STATUS_READY }}">готового к выдаче заказа</span>
			<span ng-switch-when="{{ App\Order::STATUS_FINISHED }}">завершенного заказа</span>
			<span ng-switch-default>заказа</span>
		</span><br>
		<small ng-if="searchQuery"> по запросу "@{{ searchQuery }}"</small>


		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('order-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать новый заказ
			</a>
		</div>
		@endif
	</div>

	@include('partials.order-realization-modal')
	@include('partials.delete-modal')
</div>