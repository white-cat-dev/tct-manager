<div class="materials-page" ng-init="initShow()">
	<h1>Просмотр материала</h1>

	@include('partials.loading')
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('materials') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку материалов
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ materialGroup.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-click="showCopy(materialGroup)">
				<i class="fas fa-copy"></i> Копировать
			</button>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(materialGroup)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block" ng-show="!isLoading">
		<div class="row justify-content-center">
			<div class="col-12 col-xl-11">
				<div class="product-stocks-block">
					<div class="params-title mt-0">История остатков</div>	

					<div class="modal-loading-block" ng-if="isStocksLoading">
						<i class="fa fa-cog fa-spin"></i>
					</div>

					<div class="date-groups">
						<div class="input-group date-group">
							<button class="btn btn-primary input-group-prepend" type="button" ng-click="stocksCurrentDate.year = stocksCurrentDate.year - 1; initStocks()" ng-disabled="stocksCurrentDate.year == stocksYears[0]">
							    <i class="fas fa-chevron-left"></i>
							</button>

							<ui-select ng-model="stocksCurrentDate.year" ng-change="initStocks()" skip-focusser="true" search-enabled="false">
					            <ui-select-match placeholder="Год">
						            <span ng-bind-html="$select.selected"></span>
						        </ui-select-match>
					            <ui-select-choices repeat="year in stocksYears">
					                <span ng-bind-html="year"></span>
					            </ui-select-choices>
							</ui-select>

							<button class="btn btn-primary input-group-append" type="button" ng-click="stocksCurrentDate.year = stocksCurrentDate.year + 1; initStocks()" ng-disabled="stocksCurrentDate.year == stocksYears[stocksYears.length - 1]">
							    <i class="fas fa-chevron-right"></i>
							</button>
						</div>

						<div class="input-group date-group">
						    <button class="btn btn-primary input-group-prepend" type="button" ng-click="stocksCurrentDate.month = stocksCurrentDate.month - 1; initStocks()" ng-disabled="stocksCurrentDate.month == stocksMonthes[0].id">
							    <i class="fas fa-chevron-left"></i>
							</button>

							<ui-select ng-model="stocksCurrentDate.month" ng-change="initStocks()" skip-focusser="true" search-enabled="false">
					            <ui-select-match placeholder="Месяц">
						            <span ng-bind-html="$select.selected.name"></span>
						        </ui-select-match>
					            <ui-select-choices repeat="month.id as month in stocksMonthes">
					                <span ng-bind-html="month.name"></span>
					            </ui-select-choices>
							</ui-select>

							<button class="btn btn-primary input-group-append" type="button" ng-click="stocksCurrentDate.month = stocksCurrentDate.month + 1; initStocks()" ng-disabled="stocksCurrentDate.month == stocksMonthes[stocksMonthes.length - 1].id">
							    <i class="fas fa-chevron-right"></i>
							</button>
						</div>
					</div>

					<div ng-if="materialGroup.variations">
						<div class="custom-control custom-radio custom-control-inline" ng-repeat="material in materialGroup.materials">
						  <input type="radio" id="material-radio-@{{ material.id }}" class="custom-control-input" ng-model="$parent.$parent.stocksCurrentMaterial" ng-value="material.id" ng-change="initStocks()">
						  <label class="custom-control-label" for="material-radio-@{{material.id}}">@{{ material.variation_text }}</label>
						</div>
					</div>
					
					<div class="table-block" ng-if="stocks.length > 0">
						<table class="table table-sm">
							<tr>
								<th>Дата</th>
								<th>До</th>
								<th>Изменение</th>
								<th>После</th>
								<th>Дата изменения</th>
								<th>Основание</th>
							</tr>
							<tr ng-repeat="stock in stocks">
								<td>
									@{{ stock.formatted_process_date }}
								</td>
								<td>
									<span ng-if="stock.reason != 'create' && stock.reason != 'month_start'">
										@{{ stock.in_stock | number }} <span ng-bind-html="stock.model.units_text"></span>
									</span>
									<span ng-if="stock.reason == 'create' || stock.reason == 'month_start'">
										—
									</span>
								</td>
								<td>
									<span ng-if="stock.reason != 'create' && stock.reason != 'month_start'">
										<span ng-if="stock.change >= 0">
											+ @{{ stock.change | number }} <span ng-bind-html="stock.model.units_text"></span>
										</span>
										<span ng-if="stock.change < 0">
											– @{{ -stock.change | number }} <span ng-bind-html="stock.model.units_text"></span>
										</span>
									</span>
									<span ng-if="stock.reason == 'create' || stock.reason == 'month_start'">
										—
									</span>
								</td>
								<td>
									@{{ stock.new_in_stock | number }} <span ng-bind-html="stock.model.units_text"></span>
								</td>
								<td>
									@{{ stock.formatted_date }}
								</td>
								<td>
									@{{ stock.reason_text }}
								</td>
							</tr>
						</table>
					</div>

					<div class="alert alert-secondary" ng-if="stocks.length == 0 && !isStocksLoading">
						<i class="far fa-calendar-times"></i> Нет истории остатков в этом месяце
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-12 col-lg-8 col-xl-6">
				<div class="material-supplies-block mb-5" ng-init="initSupplies()">
					<div class="params-title mt-0">История поступлений</div>	

					<div class="modal-loading-block" ng-if="isSuppliesLoading">
						<i class="fa fa-cog fa-spin"></i>
					</div>

					<div class="date-groups">
						<div class="input-group date-group">
							<button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.year = currentDate.year - 1; initSupplies()" ng-disabled="currentDate.year == years[0]">
							    <i class="fas fa-chevron-left"></i>
							</button>

							<ui-select ng-model="currentDate.year" ng-change="initSupplies()" skip-focusser="true" search-enabled="false">
					            <ui-select-match placeholder="Год">
						            <span ng-bind-html="$select.selected"></span>
						        </ui-select-match>
					            <ui-select-choices repeat="year in years">
					                <span ng-bind-html="year"></span>
					            </ui-select-choices>
							</ui-select>

							<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.year = currentDate.year + 1; initSupplies()" ng-disabled="currentDate.year == years[years.length - 1]">
							    <i class="fas fa-chevron-right"></i>
							</button>
						</div>

						<div class="input-group date-group">
						    <button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.month = currentDate.month - 1; initSupplies()" ng-disabled="currentDate.month == monthes[0].id">
							    <i class="fas fa-chevron-left"></i>
							</button>

							<ui-select ng-model="currentDate.month" ng-change="initSupplies()" skip-focusser="true" search-enabled="false">
					            <ui-select-match placeholder="Месяц">
						            <span ng-bind-html="$select.selected.name"></span>
						        </ui-select-match>
					            <ui-select-choices repeat="month.id as month in monthes">
					                <span ng-bind-html="month.name"></span>
					            </ui-select-choices>
							</ui-select>

							<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.month = currentDate.month + 1; initSupplies()" ng-disabled="currentDate.month == monthes[monthes.length - 1].id">
							    <i class="fas fa-chevron-right"></i>
							</button>
						</div>
					</div>
					
					<div class="table-block" ng-if="supplies.length > 0">
						<table class="table table-sm table-with-buttons">
							<tr>
								<th>Дата</th>
								<th>Материал</th>
								<th>Количество</th>
								<th></th>
							</tr>
							<tr ng-repeat="supply in supplies">
								<td>
									@{{ supply.formatted_date }}
								</td>
								<td>
									@{{ supply.material.material_group.name }} @{{ supply.material.variation_text }}
								</td>
								<td>
									@{{ supply.performed }}
									<span ng-switch on="supply.material.material_group.units">
										<span ng-switch-when="volume_l">л</span>
										<span ng-switch-when="volume_ml">мл</span>
										<span ng-switch-when="weight_kg">кг</span>
										<span ng-switch-when="weight_t">т</span>
									</span>
								</td>
								<td>
									<button type="button" class="btn btn-sm btn-primary" ng-click="showSupplyModal(supply)">
										<i class="fas fa-edit"></i> Изменить
									</button>
								</td>
							</tr>
						</table>
					</div>

					<div class="alert alert-secondary" ng-if="supplies.length == 0 && !isSuppliesLoading">
						<i class="far fa-calendar-times"></i> Поступлений материала в этом месяце не было
					</div>
				</div>


				<div class="params-title mt-0">
					Общая информация
				</div>

				<div class="param-block">
					<div class="param-name">
						Название
					</div>
					<div class="param-value">
						@{{ materialGroup.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Единицы измерения
					</div>
					<div class="param-value" ng-repeat="unit in units" ng-if="unit.key == materialGroup.units">
						<span ng-bind-html="unit.name"></span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Контроль расхода
					</div>
					<div class="param-value">
						<span ng-if="materialGroup.control">Ведется ручной контроль расхода материала</span>
						<span ng-if="!materialGroup.control">Расход материала рассчитывается только автоматически</span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Разновидности
					</div>
					<div class="param-value">
						<span ng-switch on="materialGroup.variations">
							<span ng-switch-when="colors">Есть разновидности по цветам</span>
							<span ng-switch-when="">Нет разновидностей</span>
						</span>
					</div>
				</div>

				<div ng-if="materialGroup.variations">
					<div class="params-title mb-0">
						Разновидности
					</div>

					<table class="table materials-table" ng-if="materialGroup.materials.length > 0">
						<tr>
							<th>Вид</th>
							<th>Цена</th>
							<th>В наличии</th>
						</tr>

						<tr ng-repeat="material in materialGroup.materials track by $index">
							<td>
								@{{ material.variation_text }}
							</td>
							<td>
								@{{ material.price | number }}
								<span ng-switch on="materialGroup.units">
									<span ng-switch-when="volume_l">руб/л</span>
									<span ng-switch-when="volume_ml">руб/мл</span>
									<span ng-switch-when="weight_kg">руб/кг</span>
									<span ng-switch-when="weight_t">руб/т</span>
									<span ng-switch-default>руб</span>
								</span>
							</td>
							<td>
								@{{ material.in_stock | number }}
								<span ng-switch on="materialGroup.units">
									<span ng-switch-when="volume_l">л</span>
									<span ng-switch-when="volume_ml">мл</span>
									<span ng-switch-when="weight_kg">кг</span>
									<span ng-switch-when="weight_t">т</span>
								</span>
							</td>
						</tr>
					</table>
				</div>

				<div ng-if="!materialGroup.variations">
					<div class="row">
						<div class="col-6">
							<div class="param-block">
								<div class="param-name">
									Цена
								</div>
								<div class="param-value">
									@{{ materialGroup.materials[0].price | number }} 
									<span ng-switch on="materialGroup.units">
										<span ng-switch-when="volume_l">руб/л</span>
										<span ng-switch-when="volume_ml">руб/мл</span>
										<span ng-switch-when="weight_kg">руб/кг</span>
										<span ng-switch-when="weight_t">руб/т</span>
										<span ng-switch-default>руб</span>
									</span>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="param-block">
								<div class="param-name">
									В наличии
								</div>
								<div class="param-value">
									@{{ materialGroup.materials[0].in_stock | number }} 
									<span ng-switch on="materialGroup.units">
										<span ng-switch-when="volume_l">л</span>
										<span ng-switch-when="volume_ml">мл</span>
										<span ng-switch-when="weight_kg">кг</span>
										<span ng-switch-when="weight_t">т</span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('partials.material-supply-modal')
	@include('partials.delete-modal')
</div>