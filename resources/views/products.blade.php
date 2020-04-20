<div class="products-page" ng-init="init()">
	<h1>Продукты</h1>

	@include('partials.top-alerts')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> Поиск
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('product-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить продукт
			</a>
			@endif
		</div>
	</div>

	<div class="categories-menu-block">
		<button type="button" class="btn btn-secondary" ng-class="{'active': currentCategory == 0}" ng-click="chooseCategory(0)">
			Все категории
		</button>
		<button type="button" ng-repeat="category in categories" class="btn btn-secondary" ng-class="{'active': currentCategory == category.id}" ng-click="chooseCategory(category.id)">
			@{{ category.name }}
		</button>
	</div>
	
	<table class="table main-table table-with-buttons" ng-if="productGroups.length > 0">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Размер</th>
			<th>Разновидности</th>
			<th>Цена</th>
			<th>В наличии</th>
			<th class="product-in-stock-col">
				<span>К выдаче</span>
				<span>Свободно</span>
			</th>
			<th></th>
		</tr>
	
		<tr ng-repeat="productGroup in productGroups | filter: searchQuery">
			<td>
				@{{ productGroup.id }}
			</td>
			<td>
				@{{ productGroup.name }}
			</td>
			<td>
				@{{ productGroup.size }} мм
			</td>
			<td>
				<div ng-repeat="product in productGroup.products">
					<span ng-if="product.color_text">@{{ product.color_text }} цвет</span>
					<span ng-if="!product.color_text">—</span>
				</div>
			</td>
			<td>
				<div ng-repeat="product in productGroup.products">
					@{{ product.price }} руб./м<sup>2</sup>
				</div>
			</td>
			<td>
				<div ng-repeat="product in productGroup.products">
					@{{ product.in_stock }} м<sup>2</sup>
				</div>
			</td>
			<td>
				<div class="product-in-stock" ng-repeat="product in productGroup.products">
					<div class="realize-in-stock" ng-style="{'width': Math.round(product.realize_in_stock / product.in_stock * 100) + '%'}" ng-if="product.realize_in_stock">
						<div class="in-stock-number">@{{ product.realize_in_stock }} м<sup>2</sup></div>
					</div>
					<div class="free-in-stock" ng-style="{'width': (!product.realize_in_stock) ? '100%' : Math.round(product.free_in_stock / product.in_stock * 100) + '%'}" ng-if="product.free_in_stock || !product.realize_in_stock">
						<div class="in-stock-number">@{{ product.free_in_stock }} м<sup>2</sup></div>
					</div>
				</div>
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ productGroup.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ productGroup.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="delete(productGroup.id)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</td>
		</tr>
	</table>
</div>