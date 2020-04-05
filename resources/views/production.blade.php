<div class="production-page" ng-init="init()">
	<h1>Производство</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group">
				<button class="btn btn-primary input-group-prepend" type="button">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<ui-select theme="bootstrap" ng-model="productData.product_group_id" ng-change="chooseProductGroup(productData, $select.selected.id)">
		            <ui-select-match placeholder="Название">
			            @{{ $select.selected.name }}
			        </ui-select-match>
		            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
		                <span ng-bind-html="productGroup.name | highlight: $select.search"></span>
		            </ui-select-choices>
				</ui-select>

				<select class="custom-select">
					<option value="2020">2020</option>
			     </select>

				<button class="btn btn-primary input-group-append" type="button">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>

			<div class="input-group">
			    <button class="btn btn-primary input-group-prepend" type="button">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<select class="custom-select">
					<option value="1" ng-repeat="(monthKey, month) in monthes" ng-selected="monthKey == currentMonth">
						@{{ month }}
					</option>
			     </select>

				<button class="btn btn-primary input-group-append" type="button">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="production-block">
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
					<th ng-repeat="x in [].constructor(days) track by $index" ng-class="{'current': $index + 1 == currentDay}">@{{ $index + 1 }}</th>
				</tr>
			
				<tr ng-repeat="product in productionProducts">
					<td ng-repeat="x in [].constructor(days) track by $index" ng-class="{'current': $index + 1 == currentDay}" ng-click="showModal()">
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

	<div class="modal" ng-show="isModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">@{{ modalData.date }}</div>
					<button type="button" class="close" ng-click="hideModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="modal-body">
					<div class="order-block" ng-repeat="order in productionOrders">
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
									@{{ production.planned }}
								</td>
								<td>
									<input type="text" class="form-control" ng-model="production.performed">
								</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-primary">
						<i class="fas fa-save"></i> Сохранить
					</button>
					<button type="button" class="btn btn-primary">
						<i class="fas fa-print"></i> Распечатать
					</button>
				</div>
			</div>
		</div>
	</div>
</div>