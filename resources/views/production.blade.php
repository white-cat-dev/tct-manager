<div class="production-page" ng-init="init()">
	<h1>Производство</h1>

	@include('partials.top-alerts')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group date-group">
				<button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.year = currentDate.year - 1; init()" ng-disabled="currentDate.year == years[0]">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<ui-select theme="bootstrap" ng-model="currentDate.year" ng-change="init()">
		            <ui-select-match placeholder="Год">
			            <span ng-bind-html="$select.selected"></span>
			        </ui-select-match>
		            <ui-select-choices repeat="year in years">
		                <span ng-bind-html="year"></span>
		            </ui-select-choices>
				</ui-select>

				<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.year = currentDate.year + 1; init()" ng-disabled="currentDate.year == years[years.length - 1]">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>

			<div class="input-group date-group">
			    <button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.month = currentDate.month - 1; init()" ng-disabled="currentDate.month == monthes[0].id">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<ui-select theme="bootstrap" ng-model="currentDate.month" ng-change="init()">
		            <ui-select-match placeholder="Месяц">
			            <span ng-bind-html="$select.selected.name"></span>
			        </ui-select-match>
		            <ui-select-choices repeat="month.id as month in monthes">
		                <span ng-bind-html="month.name"></span>
		            </ui-select-choices>
				</ui-select>

				<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.month = currentDate.month + 1; init()" ng-disabled="currentDate.month == monthes[monthes.length - 1].id">
				    <i class="fas fa-chevron-right"></i>
				</button>
			</div>
		</div>

		<div class="right-buttons">
			{{-- <button type="button" class="btn btn-primary" ng-click="showModal(currentDay)">
				<i class="far fa-calendar-check"></i> План на сегодня
			</button> --}}

			<button class="btn btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-cog"></i> Инструменты
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionsButton">
				<button type="button" class="dropdown-item" ng-click="choosePlanTool()">
					Изменить план производства
				</button>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-9">
			<div class="production-block" ng-if="productionProducts.length > 0">
				<div class="products-block">
					<table class="table">
						<tr>
							<th>Продукт</th>
							<th>Склад</th>
							<th>План</th>
						</tr>
						<tr ng-repeat="product in productionProducts">
							<td>
								<div class="product-name">
									@{{ product.product_group.name }}
									@{{ product.product_group.size }}
								</div>
								<div class="product-color" ng-if="product.color_noun_text">
									@{{ product.color_noun_text }}
								</div>
							</td>
							<td>
								@{{ product.in_stock }}
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</td>
							<td>
								@{{ product.productions[0] ? product.productions[0].planned : 0 }}
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</td>
						</tr>
					</table>
				</div>

				<div class="productions-block">
					<table class="table">
						<tr>
							<th ng-repeat="x in [].constructor(days) track by $index" ng-click="showModal($index + 1)" ng-class="{'hover': $index + 1 == hoverDay, 'current': $index + 1 == currentDay}" ng-mouseenter="chooseHoverDay($index + 1)" ng-mouseleave="chooseHoverDay(0)">
								@{{ $index + 1 }}
							</th>
						</tr>
					
						<tr ng-repeat="product in productionProducts">
							<td ng-repeat="x in [].constructor(days) track by $index" 
								ng-class="{'hover': $index + 1 == hoverDay, 'current': $index + 1 == currentDay}" 
								ng-click="showModal($index + 1, product.id)" ng-mouseenter="chooseHoverDay($index + 1)" ng-mouseleave="chooseHoverDay(0)">

								<div class="production" ng-style="{'background': getOrderMarkColor(product.productions[$index+1])}" ng-class="{'marked': getOrderMarkColor(product.productions[$index+1]) != 'transparent'}">
									<div class="production-performed" ng-if="product.productions[$index+1].performed > 0">
										@{{ product.productions[$index+1] ? product.productions[$index+1].performed : 0 }} 
									</div>
									<div class="production-planned"  ng-if="product.productions[$index+1].performed == 0">
										@{{ product.productions[$index+1] ? product.productions[$index+1].planned : 0 }} 
									</div>
									<div ng-style="{'border-bottom-color': product.productions[$index+1] ? facilities[worker.productions[$index+1].facility_id].icon_color : ''}"></div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="no-productions" ng-if="productionProducts.length == 0">
				<div>
					<i class="far fa-calendar-times"></i>
				</div>
				Нет данных на текущий месяц
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
						<a href="@{{ order.url }}">
							Заказ №@{{ order.number }}
						</a> 
						<span class="order-date">@{{ order.formatted_date }}</span>
					</div>

					<table class="table order-products">
						<tr ng-repeat="product in order.products">
							<td>
								@{{ product.product_group.name }}
								@{{ product.product_group.size }}
								<div class="product-color" ng-if ="product.color_noun_text">
									@{{ product.color_noun_text }}
								</div>
							</td>
							<td>
								@{{ product.pivot.count }} 
								<span ng-switch on="product.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт.</span>
								</span>
							</td>
						</tr>
					</table>
				</div>

				<div ng-if="productionOrders.length == 0">
					<div class="no-production-orders" ng-if="productionProducts.length == 0">
						Нет заказов <br>
						на текущий месяц
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal production-modal" ng-show="isModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">
						План производства на @{{ modalDate | date: 'dd.MM.yyyy' }}
					</div>
					<button type="button" class="close" ng-click="hideModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="modal-body">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" ng-model="isOrdersShown" id="checkbox">
						<label class="custom-control-label" for="checkbox">
							Разделить по заказам
						</label>
					</div>

					<table class="table" ng-if="!isOrdersShown">
						<tr>
							<th>Продукт</th>
							<th>Цех</th>
							<th>План</th>
							<th>Готово</th>
						</tr>

					    <tr ng-repeat="product in modalProductionProducts track by $index">
					        <td style="width: 25%;">
					        	<div class="product-name">
					        		@{{ product.product_group.name }}
									@{{ product.product_group.size }}
					        	</div>
								<div class="product-color" ng-if="product.color_noun_text">
									@{{ product.color_noun_text }}
								</div>
					        </td>

					        <td style="width: 26%">
					        	{{-- <div class="custom-control custom-radio" ng-repeat="facility in getCategoryFacilities(product.category_id)">
									<input class="custom-control-input" ng-model="product.orders[0].productions[0].facility_id" id="radio@{{ product.id }}@{{ facility.id }}" name="radio@{{ product.id }}" value="@{{ facility.id }}">
									<label class="custom-control-label" for="radio@{{ product.id }}@{{ facility.id }}">@{{ facility.name }}</label>
								</div> --}}

								<ui-select theme="bootstrap" ng-model="product.production.facility_id">
						            <ui-select-match placeholder="Выберите цех">
							            <span ng-if="$select.selected.name" ng-bind-html="$select.selected.name"></span>
							            <span ng-if="!$select.selected.name">Цех не выбран</span>
							        </ui-select-match>
						            <ui-select-choices repeat="facility.id as facility in getCategoryFacilities(product.category_id)">
						                <span ng-bind-html="facility.name"></span>
						            </ui-select-choices>
								</ui-select>
					        </td>

					        <td style="width: 15%">
								<input type="text" class="form-control form-control-sm" ng-model="product.production.planned"> 
							</td>

							<td style="width: 12%;">
								<input type="text" class="form-control form-control-sm" ng-model="product.production.performed"> 
							</td>
					    </tr>
					</table>

					<table class="table" ng-if="isOrdersShown">
						<tr>
							<th>Продукт</th>
							<th>Цех</th>
							<th>Заказ</th>
							<th>План</th>
							<th>Готово</th>
						</tr>

					    <tr ng-repeat-start="product in modalProductionProducts track by $index">
					        <td rowspan="@{{ product.orders.length}}" style="width: 25%; padding-left: 0px;">
					        	<div class="product-name">
					        		@{{ product.product_group.name }}
									@{{ product.product_group.size }}
					        	</div>
								<div class="product-color" ng-if="product.color_noun_text">
									@{{ product.color_noun_text }}
								</div>
					        </td>

					        <td rowspan="@{{ product.orders.length}}" style="width: 26%">
					        	{{-- <div class="custom-control custom-radio" ng-repeat="facility in getCategoryFacilities(product.category_id)">
									<input class="custom-control-input" ng-model="product.orders[0].productions[0].facility_id" id="radio@{{ product.id }}@{{ facility.id }}" name="radio@{{ product.id }}" value="@{{ facility.id }}">
									<label class="custom-control-label" for="radio@{{ product.id }}@{{ facility.id }}">@{{ facility.name }}</label>
								</div> --}}

								<ui-select theme="bootstrap" ng-model="product.orders[0].production.facility_id">
						            <ui-select-match placeholder="Выберите цех">
							            <span ng-if="$select.selected.name" ng-bind-html="$select.selected.name"></span>
							            <span ng-if="!$select.selected.name">Цех не выбран</span>
							        </ui-select-match>
						            <ui-select-choices repeat="facility.id as facility in getCategoryFacilities(product.category_id)">
						                <span ng-bind-html="facility.name"></span>
						            </ui-select-choices>
								</ui-select>
					        </td>

					        <td style="width: 25%">
					        	<div class="order-name">
					        		Заказ №@{{ product.orders[0].number }}
					        	</div>
					        	<div class="order-date">
									@{{ product.orders[0].formatted_date }}
								</div>
					        </td>

					        <td style="width: 15%">
								<input type="text" class="form-control form-control-sm" ng-model="product.orders[0].production.planned"> 
							</td>

							<td style="width: 12%;">
								<input type="text" class="form-control form-control-sm" ng-model="product.orders[0].production.performed"> 
							</td>
					    </tr>
					    <tr ng-repeat-end ng-repeat="order in product.orders.slice(1)">
					        <td>
						        <div class="order-name">
						        	Заказ №@{{ order.number }}
						        </div>
					        	<div class="order-date">
									@{{ order.formatted_date }}
								</div>
						    </td>

						    <td>
								<input type="text" class="form-control form-control-sm" ng-model="order.production.planned"> 
							</td>

							<td>
								<input type="text" class="form-control form-control-sm" ng-model="order.production.performed"> 
							</td>
					    </tr>
					</table>

					<button type="button" class="btn btn-primary btn-sm" ng-click="showAddProduct()" ng-if="!isAddProductShown">
						<i class="fas fa-plus"></i> Добавить продукт
					</button>

					<div class="add-product-block" ng-if="isAddProductShown">
						<div>
							<ui-select theme="bootstrap" ng-model="newProduct.product_group_id" ng-change="chooseProductGroup($select.selected)">
					            <ui-select-match placeholder="Выберите продукт...">
						            @{{ $select.selected.name }}
						        </ui-select-match>
					            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
					                <span ng-bind-html="productGroup.name | highlight: $select.search"></span>
					            </ui-select-choices>
							</ui-select>
						</div>
						<div>
							<span ng-if="newProduct.category && newProduct.category.has_colors">
								<ui-select theme="bootstrap" ng-model="newProduct.product_id" ng-change="chooseProduct($select.selected)">
						            <ui-select-match placeholder="Выберите цвет...">
							            @{{ $select.selected.color_text }}
							        </ui-select-match>
						            <ui-select-choices repeat="product.id as product in newProduct.products | filter: $select.search">
						                <span ng-bind-html="product.color_text | highlight: $select.search"></span>
						            </ui-select-choices>
								</ui-select>
							</span>
							<span ng-if="newProduct.category && !newProduct.category.has_colors">
								—
							</span>
						</div>
						<div>
							<button type="button" class="btn btn-primary btn-sm" ng-click="addProduct()">
								<i class="fas fa-plus"></i> Добавить
							</button>
						</div>
					</div>
					

					{{-- <div class="order-block" ng-if="modalNoOrderProductions.length > 0">
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
									<div class="product-color" ng-if="production.product.color_text">
										@{{ production.product.color_text }} цвет
									</div>
								</td>
								<td>
									@{{ production.planned }}
									<span ng-switch on="production.product.category.units">
										<span ng-switch-when="area">м<sup>2</sup></span>
										<span ng-switch-when="volume">м<sup>3</sup></span>
										<span ng-switch-when="unit">шт.</span>
									</span>
								</td>
								<td>
									<input type="text" class="form-control" ng-model="production.performed"> 
									<span ng-switch on="production.product.category.units">
										<span ng-switch-when="area">м<sup>2</sup></span>
										<span ng-switch-when="volume">м<sup>3</sup></span>
										<span ng-switch-when="unit">шт.</span>
									</span>
								</td>
							</tr>
						</table>
					</div> --}}

					{{-- <button type="button" class="btn btn-primary btn-sm">
						<i class="fas fa-plus"></i> Производство без заказов
					</button> --}}
				</div>

				<div class="modal-footer">
					{{-- <button type="button" class="btn btn-primary">
						<i class="fas fa-print"></i> Распечатать
					</button> --}}
					<button type="button" class="btn btn-primary" ng-click="save()">
						<i class="fas fa-save"></i> Сохранить
					</button>
				</div>
			</div>
		</div>
	</div>
</div>