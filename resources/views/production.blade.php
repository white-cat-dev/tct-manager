<div class="production-page" ng-init="init()">
	<h1>Производство</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group date-group">
				<button class="btn btn-primary input-group-prepend" type="button" ng-click="currentYear = currentYear - 1" ng-disabled="currentYear == years[0]">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<ui-select theme="bootstrap" ng-model="currentYear">
		            <ui-select-match placeholder="Год">
			            <span ng-bind-html="$select.selected"></span>
			        </ui-select-match>
		            <ui-select-choices repeat="year in years">
		                <span ng-bind-html="year"></span>
		            </ui-select-choices>
				</ui-select>

				<button class="btn btn-primary input-group-append" type="button" ng-click="currentYear = currentYear + 1" ng-disabled="currentYear == years[years.length - 1]">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>

			<div class="input-group date-group">
			    <button class="btn btn-primary input-group-prepend" type="button" ng-click="currentMonth = currentMonth - 1" ng-disabled="currentMonth == monthes[0].id">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<ui-select theme="bootstrap" ng-model="currentMonth">
		            <ui-select-match placeholder="Месяц">
			            <span ng-bind-html="$select.selected.name"></span>
			        </ui-select-match>
		            <ui-select-choices repeat="month.id as month in monthes">
		                <span ng-bind-html="month.name"></span>
		            </ui-select-choices>
				</ui-select>

				<button class="btn btn-primary input-group-append" type="button" ng-click="currentMonth = currentMonth + 1" ng-disabled="currentMonth == monthes[monthes.length - 1].id">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>
		</div>

		<div class="right-buttons">
			<button type="button" class="btn btn-primary" ng-click="showModal(currentDay)">
				<i class="far fa-calendar-check"></i> План на сегодня
			</button>
		</div>
	</div>

	<div class="row">
		<div class="col-9">
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
							<th ng-repeat="x in [].constructor(days) track by $index" {{-- ng-class="{'current': $index + 1 == currentDay}" --}} ng-click="showModal($index + 1)" ng-mouseenter="$parent.hoverDay = $index + 1" ng-mouseleave="$parent.hoverDay = 0">@{{ $index + 1 }}</th>
						</tr>
					
						<tr ng-repeat="product in productionProducts">
							<td ng-repeat="x in [].constructor(days) track by $index" 
								ng-class="{'hover': $index + 1 == $parent.hoverDay, {{-- 'current': $index + 1 == currentDay, --}} 'done': product.productions[$index+1].status == 'done', 'failed': product.productions[$index+1].status == 'failed'}" 
								ng-click="showModal($index + 1, product.id)">

								<div class="production" ng-style="{'box-shadow': 'inset -0.5px -0.5px 4px ' + getOrderMarkColor(product.productions[$index+1]), 'border-color': getOrderMarkColor(product.productions[$index+1])}">
									<div class="production-performed" ng-if="product.productions[$index+1]">
										@{{ product.productions[$index+1] ? product.productions[$index+1].performed : 0 }} м<sup>2</sup>
									</div>
									<div class="production-planned" ng-if="product.productions[$index+1]">
										@{{ product.productions[$index+1] ? product.productions[$index+1].planned : 0 }} м<sup>2</sup>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="no-production-block" ng-if="productionProducts.length == 0">
				Нет данных
			</div>
		</div>
		<div class="col-3">
			<div class="production-orders">
				<div class="orders-title">
					Текущие заказы
				</div>
				<div class="order-block" ng-repeat="order in productionOrders">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="checkbox@{{order.id}}" ng-click="markOrder(order.id)">
						<label class="custom-control-label" for="checkbox@{{order.id}}" ng-style="{'background-color': ordersMarkColors[order.id]}"></label>
						<a href="@{{ order.url }}">Заказ №@{{ order.id }}</a>
					</div>

					<table class="table order-products">
						<tr ng-repeat="product in order.products">
							<td>
								@{{ product.product_group.name }} <br>
								<span class="product-color">
									@{{ product.color_text }}
								</span>
							</td>
							<td>
								@{{ product.pivot.count }} м<sup>2</sup>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal production-modal" ng-show="isModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">
						План на @{{ modalDate }}
					</div>
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

					<div class="order-block" ng-if="modalNoOrderProductions.length > 0">
						<div class="order-name">
							Производство без заказов
						</div>

						<table class="table">
							<tr>
								<th>Продукт</th>
								<th>План</th>
								<th>Выполнено</th>
							</tr>
							<tr ng-repeat="production in modalNoOrderProductions">
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

					<button type="button" class="btn btn-primary btn-sm">
						<i class="fas fa-plus"></i> Производство без заказов
					</button>
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