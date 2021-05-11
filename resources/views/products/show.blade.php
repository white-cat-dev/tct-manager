<div class="products-page" ng-init="initShow()">
	<h1>Просмотр товара</h1>

	@include('partials.loading')
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('products') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку продуктов
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ productGroup.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-click="showCopy(productGroup)">
				<i class="fas fa-copy"></i> Копировать
			</button>
			<button type="button" class="btn btn-primary" ng-click="showDelete(productGroup)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block" ng-show="!isLoading && productGroup">
		<div class="row justify-content-around">
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

					<div ng-if="productGroup.category.variations">
						<div class="custom-control custom-radio custom-control-inline" ng-repeat="product in productGroup.products">
						  <input type="radio" id="product-radio-@{{ product.id }}" class="custom-control-input" ng-model="$parent.$parent.stocksCurrentProduct" ng-value="product.id" ng-change="initStocks()">
						  <label class="custom-control-label" for="product-radio-@{{product.id}}">@{{ product.variation_text }}</label>
						</div>
					</div>
					
					<div class="table-block" ng-if="stocks.length > 0">
						<table class="table table-sm">
							<tr>
								<th>Дата</th>
								<th>До</th>
								<th>Изменение</th>
								<th>После</th>
								<th>Основание</th>
							</tr>
							<tr ng-repeat="stock in stocks">
								<td>
									@{{ stock.formatted_date }}
								</td>
								<td>
									<span ng-if="stock.reason != 'create' && stock.reason != 'month_start'">
										@{{ stock.in_stock }} <span ng-bind-html="stock.model.units_text"></span>
									</span>
									<span ng-if="stock.reason == 'create' || stock.reason == 'month_start'">
										—
									</span>
								</td>
								<td>
									<span ng-if="stock.reason != 'create' && stock.reason != 'month_start'">
										<span ng-if="stock.change >= 0">
											+ @{{ stock.change }} <span ng-bind-html="stock.model.units_text"></span>
										</span>
										<span ng-if="stock.change < 0">
											– @{{ -stock.change }} <span ng-bind-html="stock.model.units_text"></span>
										</span>
									</span>
									<span ng-if="stock.reason == 'create' || stock.reason == 'month_start'">
										—
									</span>
								</td>
								<td>
									@{{ stock.new_in_stock }} <span ng-bind-html="stock.model.units_text"></span>
								</td>
								<td>
									@{{ stock.reason_text }}
								</td>
							</tr>
						</table>
					</div>

					<div class="alert alert-secondary" ng-if="stocks.length == 0 && !isLoading">
						<i class="far fa-calendar-times"></i> Нет истории остатков в этом месяце
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-12 col-lg-6 col-xl-5">
				<div class="params-title">
					Общая информация
				</div>
				<div class="param-block">
					<div class="param-name">
						Полное название
					</div>
					<div class="param-value">
						@{{ productGroup.wp_name }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Короткое название
					</div>
					<div class="param-value">
						@{{ productGroup.name }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Категория
					</div>
					<div class="param-value">
						@{{ productGroup.category.name }}
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Род прилагательных
					</div>
					<span ng-switch on="productGroup.adjectives">
						<span ng-switch-when="feminine">Женский</span>
						<span ng-switch-when="masculine">Мужской</span>
						<span ng-switch-when="neuter">Средний</span>
					</span>
				</div>
				<div class="param-block" ng-if="productGroup.guid">
					<div class="param-name">
						Guid
					</div>
					<div class="param-value">
						@{{ productGroup.guid }}
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6 col-xl-5">
				<div class="params-title">
					Характеристики
				</div>

				<div class="param-block">
					<div class="param-name">
						Размеры, 
						<span ng-switch on="productGroup.size_params">
							<span ng-switch-when="lwh">Д×Ш×В</span>
							<span ng-switch-when="lhw">Д×В×Ш</span>
							<span ng-switch-when="lh">Д×В</span>
							<span ng-switch-when="whl">Ш×В×Д</span>
						</span>
					</div>
					<div class="param-value">
						@{{ productGroup.size }} мм
					</div>
				</div>

				<div class="param-block" ng-show="productGroup.category.units != 'unit'">
					<div class="param-name">
						Количество 
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">в м<sup>2</sup></span>
							<span ng-switch-when="volume">в м<sup>3</sup></span>
						</span>
					</div>
					<div class="param-value">
						@{{ productGroup.unit_in_units }} шт.
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Количество на поддоне
					</div>
					<div class="param-value">
						@{{ productGroup.unit_in_pallete }} шт. / @{{ productGroup.units_in_pallete }} 
						<span ng-if="productGroup.category.units == 'area'">м<sup>2</sup></span>
						<span ng-if="productGroup.category.units == 'volume'">м<sup>3</sup></span>
						<span ng-if="productGroup.category.units == 'length'">м</span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Вес шт / поддона
					</div>
					<div class="param-value">
						@{{ productGroup.weight_unit }} кг / @{{ productGroup.weight_pallete }} кг
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-around" ng-if="productGroup.category.variations">
			<div class="col-12 col-xl-11">
				<div class="params-title">
					Разновидности и цены
				</div>

				<table class="table m-0">
					<tr>
						<th ng-if="productGroup.category.variations">Вид</th>
						<th>Цена (наличный / безнал / НДС), руб</th>
						<th>В наличии</th>
						{{-- <th>Свободно</th> --}}
					</tr>
					<tr ng-repeat="product in productGroup.products">
						<td ng-if="productGroup.category.variations">
							@{{ product.variation_text }}
						</td>
						<td>
							<div>
								<span ng-switch on="productGroup.category.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт</span>
								</span>	–
								@{{ product.price }} /
								@{{ product.price_cashless }} /
								@{{ product.price_vat }} руб
							</div>
							<div ng-if="productGroup.category.units != 'unit'">
								шт –
								@{{ product.price_unit }} /
								@{{ product.price_unit_cashless }} /
								@{{ product.price_unit_vat }} руб
							</div>
						</td>
						<td>
							@{{ product.in_stock }} 
							<span ng-switch on="productGroup.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт</span>
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="row justify-content-around">	
			<div class="col-12 col-xl-11">
				<div class="params-title">
					Парный элемент
				</div>
				<div class="param-value">
					<span ng-if="productGroup.set_pair">@{{ productGroup.set_pair.name }} (@{{ productGroup.set_pair_ratio }}:@{{ productGroup.set_pair_ratio_to }})</span>
					<span ng-if="!productGroup.set_pair">Нет парного элемента</span>
				</div>
			</div>
		</div>

		<div class="row justify-content-around">	
			<div class="col-12 col-xl-11">
				<div class="params-title">
					Данные для производства
				</div>
			</div>

			<div class="col-12 col-lg-6 col-xl-5">
				<div class="param-block">
					<div class="param-name">
						Количество из одного замеса
					</div>
					<div class="param-value">
						@{{ productGroup.units_from_batch }}
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Количество форм
					</div>
					<div class="param-value">
						@{{ productGroup.forms }} 
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Максимальная производительность
					</div>
					<div class="param-value">
						@{{ productGroup.performance }} 
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6 col-xl-5">
				<div class="param-block">
					<div class="param-name">
						Стоимость работы 
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">за 1 м<sup>2</sup></span>
							<span ng-switch-when="volume">за 1 м<sup>3</sup></span>
							<span ng-switch-when="unit">за 1 шт.</span>
						</span>	
					</div>
					<div class="param-value">
						@{{ productGroup.salary_units }} руб
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Рецепт
					</div>
					<div class="param-value">
						<span ng-if="productGroup.recipe">@{{ productGroup.recipe.name }}</span>
						<span ng-if="!productGroup.recipe">Рецепт не указан</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('partials.delete-modal')
	@include('partials.copy-modal')
</div>