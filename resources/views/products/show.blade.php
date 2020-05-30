<div class="products-page" ng-init="initShow()">
	<h1>Просмотр товара</h1>
	
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
			<button type="button" class="btn btn-primary" ng-click="copy(id)">
				<i class="fas fa-copy"></i> Копировать
			</button>
			<button type="button" class="btn btn-primary" ng-click="showDelete(productGroup)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-around">
			<div class="col-12 col-xl-11">
				<div class="show-block-title m-0">
					Продукт "@{{ productGroup.name }}"
				</div>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-6 col-xl-5">
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
			</div>

			<div class="col-6 col-xl-5">
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
						<th>Свободно</th>
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
						<td>
							@{{ product.free_in_stock }}
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

			<div class="col-6 col-xl-5">
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
						<span ng-if="productGroup.category.variations != 'colors'">Количество форм</span>
						<span ng-if="productGroup.category.variations == 'colors'">Количество серых / красных форм</span>
					</div>
					<div class="param-value">
						@{{ productGroup.forms }} 
						<span ng-if="productGroup.category.variations == 'colors'">/ @{{ productGroup.forms_add }} </span>
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
					</div>
				</div>
			</div>

			<div class="col-6 col-xl-5">
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
</div>