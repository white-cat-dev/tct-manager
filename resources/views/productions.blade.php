<div class="production-page" ng-init="init()">
	<h1>Производство</h1>

	@include('partials.loading')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group date-group">
				<button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.year = currentDate.year - 1; init()" ng-disabled="currentDate.year == years[0]">
				    <i class="fas fa-chevron-left"></i>
				</button>

				<ui-select ng-model="currentDate.year" ng-change="init()" skip-focusser="true" search-enabled="false">
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

				<ui-select ng-model="currentDate.month" ng-change="init()" skip-focusser="true" search-enabled="false">
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

			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" ng-model="isAllProductionsShown" id="checkboxSalaries">
				<label class="custom-control-label" for="checkboxSalaries">
					Показать полный график
				</label>
			</div>
		</div>

		<div class="right-buttons">
			<button type="button" class="btn btn-primary" ng-click="showModal(currentDate.day)">
				<i class="far fa-calendar-check"></i> План на сегодня
			</button>
		</div>
	</div>


	<div class="production-block" ng-if="isAllProductionsShown && productionProducts.length > 0 || !isAllProductionsShown && productionsPlanned">
		<div class="products-block">
			<table class="table">
				<tr>
					<th>Продукт</th>
					<th class="d-none d-md-table-cell">Склад</th>
					<th class="d-none d-md-table-cell">План</th>
				</tr>
				<tr ng-repeat="product in productionProducts" ng-if="isAllProductionsShown || product.productions[0] && product.productions[0].planned > product.productions[0].performed">
					<td>
						<div class="product-name">
							@{{ product.product_group.name }}<br class="d-block d-md-none">
							@{{ product.product_group.size }}
						</div>
						<div class="product-color" ng-if="product.variation_noun_text">
							@{{ product.variation_noun_text }}
						</div>
					</td>
					<td class="d-none d-md-table-cell">
						@{{ product.in_stock }}
						<span ng-switch on="product.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
					</td>
					<td class="d-none d-md-table-cell">
						@{{ product.productions[0] ? ((product.productions[0].planned > product.productions[0].performed) ? Math.round((product.productions[0].planned - product.productions[0].performed) * 1000) / 1000 : 0) : 0 }}
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
					<th ng-repeat="x in [].constructor(days) track by $index" ng-click="showModal($index + 1)" ng-class="{'hover': $index + 1 == hoverDay, 'current': $index + 1 == currentDate.day}" ng-mouseenter="chooseHoverDay($index + 1)" ng-mouseleave="chooseHoverDay(0)">
						@{{ $index + 1 }}
					</th>
				</tr>
			
				<tr ng-repeat="product in productionProducts" ng-if="(isAllProductionsShown || product.productions[0] && product.productions[0].planned > product.productions[0].performed)">
					<td ng-repeat="x in [].constructor(days) track by $index" 
						ng-class="{'hover': $index + 1 == hoverDay, 'current': $index + 1 == currentDate.day}" 
						ng-click="showModal($index + 1)" ng-mouseenter="chooseHoverDay($index + 1)" ng-mouseleave="chooseHoverDay(0)">

						<div class="production" ng-style="{'background': getOrderMarkColor(product.productions[$index+1])}" ng-class="{'marked': getOrderMarkColor(product.productions[$index+1]) != 'transparent'}">
							<div class="production-performed" ng-if="product.productions[$index+1].performed > 0">
								@{{ product.productions[$index+1] ? product.productions[$index+1].performed : 0 }} 
							</div>
							<div class="production-planned"  ng-if="product.productions[$index+1].performed == 0">
								@{{ product.productions[$index+1] ? product.productions[$index+1].batches : 0 }} 
							</div>
							{{-- <div class="production-facility" ng-style="{'border-bottom-color': product.productions[$index+1] ? facilities[product.productions[$index+1].facility_id].icon_color : ''}">
							</div> --}}
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="no-productions" ng-if="((isAllProductionsShown && productionProducts.length == 0) || (!isAllProductionsShown && !productionsPlanned)) && !isLoading">
		<div class="icon">
			<i class="far fa-calendar-times"></i>
		</div>
		<div>
			Ничего не запланировано на текущий месяц
		</div>
		<div>
			<button type="button" class="btn btn-primary" ng-click="showAllProductions()" ng-if="productionProducts.length > 0 && !shownFull">
				Посмотреть полный график
			</button>
			<button type="button" class="btn btn-primary" ng-click="showModal(currentDate.day)" ng-if="productionProducts.length == 0">
				Начать производство
			</button>
		</div>
	</div>


	<div class="modal production-modal" ng-show="isModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">
						План 
						<span class="d-none d-md-inline">производства</span>
						на @{{ modalDate | date: 'dd.MM.yyyy' }}
					</div>
					<button type="button" class="close" ng-click="hideModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="modal-body">
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<button type="button" class="nav-link" ng-click="chooseModalType('plan')" ng-class="{'active': chosenModalType == 'plan'}">
								Планирование
							</button>
						</li>
						<li class="nav-item">
							<button type="button" class="nav-link" ng-click="chooseModalType('perform')" ng-class="{'active': chosenModalType == 'perform'}">
								Выполнение
							</button>
						</li>
						<li class="nav-item">
							<button type="button" class="nav-link" ng-click="chooseModalType('total')" ng-class="{'active': chosenModalType == 'total'}">
								Итоги
							</button>
						</li>
					</ul>

					<div class="plan-block" ng-if="chosenModalType == 'plan'">	
						<div class="table-responsive-block" ng-if="modalProductionProducts.length > 0">
							<table class="table">
								<tr>
									<th>Продукт</th>
									<th>Цех</th>
									<th>Общий план</th>
									<th>Запланировать</th>
								</tr>

							    <tr ng-repeat="product in modalProductionProducts track by $index">
							        <td style="width: 25%">
							        	<div class="product-name">
							        		@{{ product.product_group.name }}<br class="d-block d-md-none">
											@{{ product.product_group.size }}
							        	</div>
										<div class="product-color" ng-if="product.variation_noun_text">
											@{{ product.variation_noun_text }}
										</div>
							        </td>

							        <td style="width: 25%" class="production-facility-col">
							        	<div class="production-facility" ng-style="{'border-bottom-color': facilities[product.production.facility_id] ? facilities[product.production.facility_id].icon_color : 'transparent'}"></div>

							        	<ui-select ng-model="product.production.facility_id" ng-change="updateOrderProductionsFacility(product)" skip-focusser="true">
								            <ui-select-match placeholder="Выберите цех">
									            <span ng-if="$select.selected.name" ng-bind-html="$select.selected.name"></span>
									            <span ng-if="!$select.selected.name" class="text-danger">Цех не выбран</span>
									        </ui-select-match>
								            <ui-select-choices repeat="facility.id as facility in getCategoryFacilities(product.category_id)">
								                <span ng-bind-html="facility.name"></span>
								            </ui-select-choices>
										</ui-select>
							        </td>

							        <td style="width: 25%;" class="text-center">
							        	@{{ product.base_planned }} <span ng-bind-html="product.units_text"></span>
							        </td>

							        <td style="width: 12%;">
										<input type="text" class="form-control form-control-num" ng-model="product.production.batches" ng-change="updateProductionPlanned(product)"> 
										<small>@{{ product.production.planned }} <span ng-bind-html="product.units_text"></small>
									</td>
							    </tr>
							</table>
						</div>

						<div class="alert alert-secondary" ng-if="modalProductionProducts.length == 0">
							<i class="far fa-calendar-times"></i> Ничего не запланировано на этот день
						</div>

						<button type="button" class="btn btn-primary btn-sm" ng-click="showAddProduct(0)" ng-if="!isAddProductShown[0]">
							<i class="fas fa-plus"></i> Добавить продукт
						</button>

						<div class="add-product-block" ng-if="isAddProductShown[0]">
							<div>
								<ui-select ng-model="newProduct[0].product_group_id" ng-change="chooseProductGroup(0, $select.selected)" skip-focusser="true">
						            <ui-select-match placeholder="Название продукта...">
							            @{{ $select.selected.name }} @{{ $select.selected.size }}
							        </ui-select-match>
						            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
						                <span ng-bind-html="productGroup.name + ' ' + productGroup.size | highlight: $select.search"></span>
						            </ui-select-choices>
								</ui-select>
							</div>
							<div>
								<div ng-if="newProduct[0].category && newProduct[0].category.variations">
									<ui-select ng-model="newProduct[0].product_id" ng-change="chooseProduct(0, $select.selected)" skip-focusser="true">
							            <ui-select-match placeholder="Разновидность...">
								            @{{ $select.selected.variation_text }}
								        </ui-select-match>
							            <ui-select-choices repeat="product.id as product in newProduct[0].products | filter: $select.search">
							                <span ng-bind-html="product.variation_text | highlight: $select.search"></span>
							            </ui-select-choices>
									</ui-select>
								</div>
								<div ng-if="newProduct[0].category && !newProduct[0].category.variations" class="empty-select">
									—
								</div>
								<div ng-if="!newProduct[0].category" class="empty-select">
								</div>
							</div>
							<div>
								<button type="button" class="btn btn-primary btn-sm" ng-click="addProduct(0)">
									<i class="fas fa-plus"></i> Добавить
								</button>
							</div>
						</div>
					</div>


					<div ng-if="chosenModalType == 'perform'">	
						<div class="production-facility-block" ng-repeat="facility in facilities">
							<div class="block-title">
								<span ng-style="{'background': facility.icon_color}"></span> @{{ facility.name }}
							</div>

							<table class="table" ng-if="getFacilityProductionProducts(facility.id).length > 0">
								<tr>
									<th>Продукт</th>
									<th>План</th>
									<th>Готово</th>
								</tr>

							    <tr ng-repeat="product in getFacilityProductionProducts(facility.id) {{-- | orderBy:'production_id' --}} track by $index">
							        <td style="width: 50%;">
							        	<div class="product-name">
							        		@{{ product.product_group.name }}
											@{{ product.product_group.size }}
							        	</div>
										<div class="product-color" ng-if="product.variation_noun_text">
											@{{ product.variation_noun_text }}
										</div>
							        </td>

							        <td style="width: 25%; text-align: center;">
										@{{ product.production.batches }}
									</td>

									<td style="width: 25%;">
										<input type="text" class="form-control form-control-num" ng-model="product.production.performed"{{--  ng-change="updateOrderProductionsPerformed(product)" --}}> 
									</td>
							    </tr>
							</table>

							<div class="alert alert-secondary" ng-if="getFacilityProductionProducts(facility.id).length == 0">
								<i class="far fa-calendar-times"></i> В этом цехе ничего не производится
							</div>

							<button type="button" class="btn btn-primary btn-sm" ng-click="showAddProduct(facility.id)" ng-if="!isAddProductShown[facility.id]">
								<i class="fas fa-plus"></i> Добавить продукт
							</button>

							<div class="add-product-block" ng-if="isAddProductShown[facility.id]">
								<div>
									<ui-select ng-model="newProduct[facility.id].product_group_id" ng-change="chooseProductGroup(facility.id, $select.selected)" skip-focusser="true">
							            <ui-select-match placeholder="Название продукта...">
								            @{{ $select.selected.name }} @{{ $select.selected.size }}
								        </ui-select-match>
							            <ui-select-choices repeat="productGroup.id as productGroup in getFacilityProductGroups(facility) | filter: $select.search">
							                <span ng-bind-html="productGroup.name + ' ' + productGroup.size | highlight: $select.search"></span>
							            </ui-select-choices>
									</ui-select>
								</div>
								<div>
									<div ng-if="newProduct[facility.id].category && newProduct[facility.id].category.variations">
										<ui-select ng-model="newProduct[facility.id].product_id" ng-change="chooseProduct(facility.id, $select.selected)" skip-focusser="true">
								            <ui-select-match placeholder="Разновидность...">
									            @{{ $select.selected.variation_text }}
									        </ui-select-match>
								            <ui-select-choices repeat="product.id as product in newProduct[facility.id].products | filter: $select.search">
								                <span ng-bind-html="product.variation_text | highlight: $select.search"></span>
								            </ui-select-choices>
										</ui-select>
									</div>
									<div ng-if="newProduct[facility.id].category && !newProduct[facility.id].category.variations" class="empty-select">
										—
									</div>
									<div ng-if="!newProduct[facility.id].category" class="empty-select">
									</div>
								</div>
								<div>
									<button type="button" class="btn btn-primary btn-sm" ng-click="addProduct(facility.id)">
										<i class="fas fa-plus"></i> Добавить
									</button>
								</div>
							</div>
						</div>
					</div>


					<div class="total-block" ng-if="chosenModalType == 'total'">
						<div class="alert alert-warning" role="alert">
							Не забудьте сохранить все изменения, прежде чем просматривать итоги
						</div>

						<div class="block-title">
							Оплата
						</div>

						<table class="table" ng-if="modalProductionCategories.length > 0">
							<tr>
								<th>Категория</th>
								<th>Выпуск</th>
								<th>Оплата</th>
							</tr>
							<tr ng-repeat="category in modalProductionCategories">
								<td style="width: 40%;">
									@{{ category.name }}
								</td>
								<td style="width: 30%;" class="text-center">
									@{{ category.production.performed | number }}
									<span ng-switch on="category.units">
										<span ng-switch-when="area">м<sup>2</sup></span>
										<span ng-switch-when="volume">м<sup>3</sup></span>
										<span ng-switch-when="unit">шт.</span>
									</span>
								</td>
								<td style="width: 30%;" class="text-center">
									@{{ category.production.salary | number }} руб
								</td>
							</tr>
						</table>

						<div class="alert alert-secondary" ng-if="modalProductionCategories.length == 0">
							<i class="far fa-calendar-times"></i> За этот день ничего не было произведено
						</div>

						<div class="block-title">
							Расход материалов
						</div>

						<table class="table" ng-if="modalProductionMaterials.length > 0">
							<tr>
								<th>Материал</th>
								<th>Расчетный</th>
								<th>Фактический</th>
							</tr>
							<tr ng-repeat="material in modalProductionMaterials">
								<td style="width: 40%;">
									@{{ material.material_group.name }} @{{ material.variation_text }}
								</td>
								<td style="width: 30%;" class="text-center">
									@{{ material.apply.planned | number }} 
									<span ng-switch on="material.material_group.units">
										<span ng-switch-when="volume_l">л</span>
										<span ng-switch-when="volume_ml">мл</span>
										<span ng-switch-when="weight_kg">кг</span>
										<span ng-switch-when="weight_t">т</span>
									</span>
								</td>
								<td style="width: 30%;" class="text-center">
									<span ng-if="!material.material_group.control">
										@{{ material.apply.performed | number }}
									</span>
									<span ng-if="material.material_group.control">
										<input type="text" class="form-control form-control-num" ng-change="inputFloat(material.apply, 'performed')" ng-model="material.apply.performed"></span>
									</span>
								</td>
							</tr>
						</table>

						<div class="alert alert-secondary" ng-if="modalProductionMaterials.length == 0">
							<i class="far fa-calendar-times"></i> В этот день ни один материал не был использован
						</div>
					</div>
				</div>

				<div class="modal-footer">
					{{-- <button type="button" class="btn btn-primary">
						<i class="fas fa-print"></i> Распечатать
					</button> --}}
					<button type="button" class="btn btn-primary" ng-click="save()" ng-disabled="isSaving">
						<span ng-if="isSaving">
							<i class="fa fa-spinner fa-spin"></i> Сохранение
						</span>
						<span ng-if="!isSaving">
							<i class="fas fa-save"></i> Сохранить
						</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>