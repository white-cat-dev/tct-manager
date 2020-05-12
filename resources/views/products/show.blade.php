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
			<button type="button" class="btn btn-primary" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-around">
			<div class="col-11">
				<div class="show-block-title m-0">
					Продукт "@{{ productGroup.name }}"
				</div>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-5">
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

			<div class="col-5">
				<div class="params-title">
					Характеристики
				</div>

				<div class="param-block">
					<div class="param-name">
						Размеры
					</div>
					<div class="param-value">
						@{{ productGroup.size }} мм
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Количество в 1 квадрате
					</div>
					<div class="param-value">
						@{{ productGroup.unit_in_units }} шт.
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Количество в 1 поддоне
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
						Вес
					</div>
					<div class="param-value">
						@{{ productGroup.weight_unit }} кг / @{{ productGroup.weight_units }} кг / @{{ productGroup.weight_pallete }} кг
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-around" ng-if="productGroup.category.variations">
			<div class="col-11">
				<div class="params-title">
					<span ng-switch on="productGroup.category.variations">
						<span ng-switch-when="colors">Разновидности по цветам</span>
						<span ng-switch-when="grades">Разновидности по марке бетона</span>
					</span>
				</div>

				<table class="table">
					<tr>
						<th>
							<span ng-switch on="productGroup.category.variations">
								<span ng-switch-when="colors">Цвет</span>
								<span ng-switch-when="grades">Марка</span>
							</span>
						</th>
						<th>Цена</th>
						<th>В наличии</th>
						<th class="product-in-stock-col">
							<span>К выдаче</span>
							<span>Свободно</span>
						</th>
					</tr>
					<tr ng-repeat="product in productGroup.products">
						<td>
							@{{ product.variation_text }}
						</td>
						<td>
							@{{ product.price_unit }} руб. /
							@{{ product.price }} руб. /
							@{{ product.price_pallete }} руб.
						</td>
						<td>
							@{{ product.in_stock }} шт.
						</td>
						<td>
							<div class="product-in-stock">
								<div class="realize-in-stock" ng-style="{'width': Math.round(product.realize_in_stock / product.in_stock * 100) + '%'}" ng-if="product.realize_in_stock">
									<div class="in-stock-number">@{{ product.realize_in_stock }} м<sup>2</sup></div>
								</div>
								<div class="free-in-stock" ng-style="{'width': (!product.realize_in_stock) ? '100%' : Math.round(product.free_in_stock / product.in_stock * 100) + '%'}" ng-if="product.free_in_stock || !product.realize_in_stock">
									<div class="in-stock-number">@{{ product.free_in_stock }} м<sup>2</sup></div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="row" ng-if="!productGroup.category.variations">
			<div class="col-3">
				<div class="param-block">
					<div class="param-name">
						Цена
					</div>
					<div class="param-value">
						@{{ productGroup.products[0].price_unit }} руб / 
						@{{ productGroup.products[0].price }} руб / 
						@{{ productGroup.products[0].price_pallete }} руб
					</div>
				</div>
			</div>

			<div class="col-3">
				<div class="param-block">
					<div class="param-name">
						В наличии
					</div>
					<div class="param-value">
						@{{ productGroup.products[0].in_stock }}
					</div>
				</div>
			</div>

			<div class="col-5">
				<div class="product-in-stock">
					<div class="realize-in-stock" ng-style="{'width': Math.round(productGroup.products[0].realize_in_stock / productGroup.products[0].in_stock * 100) + '%'}" ng-if="productGroup.products[0].realize_in_stock">
						<div class="in-stock-number">@{{ productGroup.products[0].realize_in_stock }} м<sup>2</sup></div>
					</div>
					<div class="free-in-stock" ng-style="{'width': (!productGroup.products[0].realize_in_stock) ? '100%' : Math.round(productGroup.products[0].free_in_stock / productGroup.products[0].in_stock * 100) + '%'}" ng-if="productGroup.products[0].free_in_stock || !productGroup.products[0].realize_in_stock">
						<div class="in-stock-number">@{{ productGroup.products[0].free_in_stock }} м<sup>2</sup></div>
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-around">	
			<div class="col-5">
				<div class="params-title">
					Данные для производства
				</div>

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
			</div>
			<div class="col-5">
				<div class="params-title">
					Данные для расчета зарплаты
				</div>

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
						@{{ productGroup.salary_units }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>