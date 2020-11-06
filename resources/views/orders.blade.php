<div class="orders-page" ng-init="init('production')">
	<h1>Заказы</h1>

	@include('partials.loading')

	@if (Auth::user() && Auth::user()->type == 'admin')
	<a href="{{ route('order-create') }}" class="btn btn-primary top-right-button">
		<i class="fas fa-plus"></i>
	</a>
	@endif

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите номер заказа..." ng-model="tempSearchQuery" ng-keypress="searchInputKeyPressed($event)">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> <span class="d-none d-md-inline">Поиск</span>
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons d-none d-md-flex">
			<button class="btn btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-cog"></i> Действия
			</button>
			<div class="dropdown-menu" aria-labelledby="actionsButton">
				<button type="button" class="dropdown-item" ng-click="showPaidCostReportModal()">
					<i class="fas fa-paste"></i> Сформировать отчёт
				</button>
			</div>

			<a href="{{ route('order-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать заказ
			</a>
		</div>
	</div>


	<div class="statuses-menu-block" ng-init="isStatusesShown = false">	
		<div class="statuses-menu" ng-class="{'shown': isStatusesShown}" ng-click="isStatusesShown = !isStatusesShown">
			<div class="statuses-title btn d-block d-md-none">
				<span ng-if="isStatusesShown">Выберите категорию...</span>
				<span ng-if="currentStatus == 0 && !isStatusesShown">Все категории</span>
				<span ng-switch on="currentStatus" ng-if="!isCategoriesShown">
					<span ng-switch-when="production">В работе</span>
					<span ng-switch-when="ready">Готовые к выдаче</span>
					<span ng-switch-when="unpaid">Неоплаченные</span>
					<span ng-switch-when="finished">Завершенные</span>
					<span ng-switch-when="new">С сайта</span>
				</span>

				<div class="icon">
					<i class="fas fa-caret-down"></i>
				</div>
			</div>

			<button type="button" class="btn" ng-class="{'active': currentStatus == '' }" ng-click="chooseStatus('')">
				Все заказы
			</button>
			<button type="button" class="btn" ng-class="{'active': currentStatus == 'production'}" ng-click="chooseStatus('production')">
				В работе
			</button>
			<button type="button" class="btn" ng-class="{'active': currentStatus == 'ready'}" ng-click="chooseStatus('ready')">
				Готовые к выдаче
			</button>
			<button type="button" class="btn" ng-class="{'active': currentStatus == 'unpaid'}" ng-click="chooseStatus('unpaid')">
				Неоплаченные
			</button>
			<button type="button" class="btn" ng-class="{'active': currentStatus == 'finished'}" ng-click="chooseStatus('finished')">
				Завершенные
			</button>
			<button type="button" class="btn" ng-class="{'active': currentStatus == 'new'}" ng-click="chooseStatus('new')">
				С сайта
			</button>
		</div>

		<div class="main-category-block">
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="checkbox" class="custom-control-input" ng-checked="currentMainCategory.indexOf('tiles') !== -1" id="checkboxBlocks" ng-click="chooseMainCategory('tiles')">
				<label class="custom-control-label" for="checkboxBlocks">
					Плитка
				</label>
			</div>
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="checkbox" class="custom-control-input" ng-checked="currentMainCategory.indexOf('blocks') !== -1" id="checkboxTiles" ng-click="chooseMainCategory('blocks')">
				<label class="custom-control-label" for="checkboxTiles">
					Блоки
				</label>
			</div>
		</div>
	</div>

	@include('partials.date-pagination')

	<div class="row orders-row" ng-if="(orders | filter: {'number': searchQuery}).length > 0">
		<div class="col">
			<div class="main-orders-block">
				<table class="table">
					<tr>
						<th>Номер</th>
						<th>Дата принятия</th>
						<th class="d-none d-lg-table-cell">Дата готовности</th>
						<th>Стоимость</th>
						<th class="d-none d-lg-table-cell">Оплачено</th>
					</tr>
					<tr ng-repeat="order in orders | filter: {'number': searchQuery}" ng-click="chooseOrder(order)" ng-class="{'active': currentOrder.id == order.id}">
						<td ng-class="{'text-success': order.priority == {{ App\Order::PRIORITY_HIGH }} }">
							<div class="order-name">
								<span ng-if="order.number">@{{ order.number }}</span>
								<span ng-if="!order.number">@{{ order.id }}</span>
								{{-- <div class="order-priority" ng-if="order.priority == {{ App\Order::PRIORITY_HIGH }}">
									Важно
								</div> --}}
							</div>
						</td>
						<td>
							@{{ order.formatted_date }}
						</td>
						<td class="d-none d-lg-table-cell">
							@{{ order.formatted_date_to }}
						</td>
						<td ng-class="{'text-success': order.pay_type != 'cash'}">
							@{{ order.cost | number }} руб
						</td>
						<td ng-class="{'text-success': order.pay_type != 'cash'}" class="d-none d-lg-table-cell">
							@{{ order.payments_paid | number }} руб
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="col">
			<div class="main-products-block">
				<div ng-if="currentOrder">
					<div class="title-block">
						Заказ №<span ng-if="currentOrder.number">@{{ currentOrder.number }}</span><span ng-if="!currentOrder.number">@{{ currentOrder.id }}</span>
						<div class="order-status-block">@{{ currentOrder.status_text }}</div>
					</div>

					<div class="btn-group">
						<a ng-href="@{{ currentOrder.url }}" class="btn btn-primary btn-sm">
							<i class="fas fa-eye"></i>
						</a>
						<a ng-href="@{{ currentOrder.url + '/edit' }}" class="btn btn-primary btn-sm">
							<i class="fas fa-edit"></i>
						</a>
						<button type="button" class="btn btn-primary btn-sm" ng-click="loadExportFile(currentOrder)">
							<i class="fas fa-print"></i>
						</button>
					</div>

					<div class="order-info-block">
						<div ng-if="currentOrder.pay_type != 'cash'">
							<span class="order-info-title">Способ оплаты:</span>
							@{{ currentOrder.pay_type_text }}
						</div> 
						<div ng-if="currentOrder.delivery">
							<span class="order-info-title">Доставка:</span>
							@{{ currentOrder.delivery_text }}
						</div> 
						<div ng-if="currentOrder.priority > {{ App\Order::PRIORITY_NORMAL }}">
							<span class="order-info-title">Приоритет:</span>
							@{{ currentOrder.priority_text }}
						</div> 
						<div ng-if="currentOrder.client.name || currentOrder.client.phone || currentOrder.client.email">
							<span class="order-info-title">Данные клиента:</span> 
							@{{ currentOrder.client.name }} @{{ currentOrder.client.phone }} @{{ currentOrder.client.email }}
						</div> 
						<div ng-if="currentOrder.comment">
							<span class="order-info-title">Комментарий к заказу:</span>
							@{{ currentOrder.comment }}
						</div> 
					</div>

					<table class="table table-sm" ng-if="currentOrder.products.length > 0">
						<tr>
							<th>Продукт</th>
							<th>Кол-во</th>
							<th>Отпущено</th>
							<th>Осталось</th>
							<th ng-if="currentOrder.status != {{ App\Order::STATUS_FINISHED }} && currentOrder.status != {{ App\Order::STATUS_UNPAID }}">В наличии</th>
						</tr>
						<tr ng-repeat="product in currentOrder.products">
							<td>
								<div>@{{ product.product_group.name }}</div>
								<div>@{{ product.product_group.size }}</div>
								<div class="product-color">@{{ product.variation_noun_text }}</div>
							</td>
							<td ng-class="{'text-success': product.progress.total == product.progress.realization}">
								@{{ product.progress.total }}
								<span ng-bind-html="product.units_text"></span>
							</td>
							<td ng-class="{'text-success': product.progress.total == product.progress.realization}">
								@{{ product.progress.realization }}
								<span ng-bind-html="product.units_text"></span>
							</td>
							<td ng-class="{'text-success': product.progress.total == product.progress.realization}">
								@{{ product.progress.planned }}
								<span ng-bind-html="product.units_text"></span>
							</td>
							<td ng-if="currentOrder.status != {{ App\Order::STATUS_FINISHED }} && currentOrder.status != {{ App\Order::STATUS_UNPAID }}">
								<span ng-if="product.progress.total != product.progress.realization">
									@{{ product.in_stock }}
									<span ng-bind-html="product.units_text"></span>
								</span>
								<span ng-if="product.progress.total == product.progress.realization">
									—
								</span>
							</td>
						</tr>
						<tr ng-if="currentOrder.pallets > 0">
							<td>
								<div>Поддоны</div>
							</td>
							<td ng-class="{'text-success': currentOrder.pallets_progress.total == currentOrder.pallets_progress.realization}">
								@{{ currentOrder.pallets_progress.total }} шт
							</td>
							<td ng-class="{'text-success': currentOrder.pallets_progress.total == currentOrder.pallets_progress.realization}">
								@{{ currentOrder.pallets_progress.realization }} шт
							</td>
							<td ng-class="{'text-success': currentOrder.pallets_progress.total == currentOrder.pallets_progress.realization}">
								@{{ currentOrder.pallets_progress.planned }} шт
							</td>
							<td ng-if="currentOrder.status != {{ App\Order::STATUS_FINISHED }} && currentOrder.status != {{ App\Order::STATUS_UNPAID }}">
								—
							</td>
						</tr>
					</table>

					<div class="buttons-block">
						<button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-cog"></i> Действия
						</button>
						<div class="dropdown-menu" aria-labelledby="actionsButton">
							<button type="button" class="btn-sm dropdown-item" ng-if="currentOrder.status != {{ App\Order::STATUS_NEW }}" ng-click="showRealizationModal(currentOrder)">
								Отпустить заказ
							</button>
							<button type="button" class="btn-sm dropdown-item" {{-- ng-if="currentOrder.paid < currentOrder.cost" --}} ng-click="showPaymentModal(currentOrder)">
								Внести платеж
							</button>
							<button type="button" class="btn-sm dropdown-item" ng-if="currentOrder.status == {{ App\Order::STATUS_NEW }}" ng-click="save()">
								В производство
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

	<div ng-if="(orders | filter: {'number': searchQuery}).length > 20">	
		@include('partials.date-pagination')
	</div>

	<div class="no-data-block" ng-if="(orders | filter: {'number': searchQuery}).length == 0 && !isLoading">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного
		<span ng-switch on="currentStatus">
			<span ng-switch-when="production">заказа в работе</span>
			<span ng-switch-when="ready">готового к выдаче заказа</span>
			<span ng-switch-when="unpaid">неоплаченного заказа</span>
			<span ng-switch-when="finished">завершенного заказа</span>
			<span ng-switch-when="new">заказа с сайта</span>
			<span ng-switch-default>заказа</span>
		</span><br>
		<small ng-if="currentMainCategory.length == 1 && currentMainCategory[0] == 'tiles'"> в категории «плитка»</small>
		<small ng-if="currentMainCategory.length == 1 && currentMainCategory[0] == 'blocks'"> в категории «блоки»</small>
		<small ng-if="searchQuery"> по запросу «@{{ searchQuery }}»</small>
		<div ng-if="currentMainCategory.length == 0">
			<small>Выберите категорию (плитка или блоки)</small>
		</div>
		<div ng-if="currentStatus == 'finished' || !currentStatus">
			<small>
				за
				<span ng-repeat="month in monthes" ng-if="currentDate.month == month.id">
					@{{ month.name | lowercase }}
				</span> 
				<span>
					@{{ currentDate.year }} года
				</span>
			</small>
		</div>

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('order-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать новый заказ
			</a>
		</div>
		@endif
	</div>

	@include('partials.orders-paid-cost-report-modal')
	@include('partials.order-realization-modal')
	@include('partials.order-payment-modal')
	@include('partials.delete-modal')
</div>