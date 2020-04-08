<div class="production-page" ng-init="init()">
	<h1>Производство</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group">
				<button class="btn btn-primary input-group-prepend" type="button">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<select class="custom-select" ng-model="currentYear" ng-change="init()" ng-options="item as item for item in years"></select>

				<button class="btn btn-primary input-group-append" type="button">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>

			<div class="input-group">
			    <button class="btn btn-primary input-group-prepend" type="button">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<select class="custom-select" ng-model="currentMonth" ng-change="init()" ng-options="month.key as month.name for month in monthes"></select>

				<button class="btn btn-primary input-group-append" type="button">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="production-block" ng-if="productionProducts.length > 0">
		<div class="products-block">
			<div class="product-block"></div>
			<div class="product-block" ng-repeat="product in productionProducts">
				<div class="product-name">
					@{{ product.product_group.name }}
				</div>
				<div class="product-size">
					@{{ product.product_group.size }} мм
				</div>
				<div class="product-color">
					@{{ product.color_text }} цвет
				</div>
			</div>
		</div>

		<div class="productions-block">
			<table class="table">
				<tr>
					<th ng-repeat="x in [].constructor(days) track by $index" ng-class="{'current': $index + 1 == currentDay}" ng-click="showModal($index + 1)" ng-mouseenter="$parent.hoverDay = $index + 1" ng-mouseleave="$parent.hoverDay = 0">@{{ $index + 1 }}</th>
				</tr>
			
				<tr ng-repeat="product in productionProducts">
					<td ng-repeat="x in [].constructor(days) track by $index" ng-class="{'hover': $index + 1 == $parent.hoverDay, 'current': $index + 1 == currentDay, 'done': product.productions[$index+1].status == 'done', 'failed': product.productions[$index+1].status == 'failed'}" ng-click="showModal($index + 1, product.id)">
						<div class="production-planned" ng-if="product.productions[$index+1]">
							@{{ product.productions[$index+1] ? product.productions[$index+1].planned : 0 }} м<sup>2</sup>
						</div>
						<div class="production-performed" ng-if="product.productions[$index+1]">
							@{{ product.productions[$index+1] ? product.productions[$index+1].performed : 0 }} м<sup>2</sup>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="no-ptoduction-block" ng-if="productionProducts.length == 0">
		Нет данных
	</div>

	<div class="modal" ng-show="isModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">@{{ modalDate }}</div>
					<button type="button" class="close" ng-click="hideModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="modal-body">
					<div class="order-block" ng-repeat="order in modalProductionOrders">
						<a ng-href="@{{ order.url }}" class="order-name">
							Заказ №@{{ order.id }}
						</a>

						<table class="table">
							<tr>
								<th>Продукт</th>
								<th>План</th>
								<th>Выполнено</th>
							</tr>
							<tr ng-repeat="production in order.productions">
								<td>
									<div class="product-name">
										@{{ production.product.product_group.name }}
									</div>
									<div class="product-size">
										@{{ production.product.product_group.size }} мм
									</div>
									<div class="product-color">
										@{{ production.product.color_text }} цвет
									</div>
								</td>
								<td>
									@{{ production.planned }} м<sup>2</sup>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="production.performed"> м<sup>2</sup>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-primary">
						<i class="fas fa-print"></i> Распечатать
					</button>
					<button type="button" class="btn btn-primary" ng-click="save()">
						<i class="fas fa-save"></i> Сохранить
					</button>
				</div>
			</div>
		</div>
	</div>
</div>