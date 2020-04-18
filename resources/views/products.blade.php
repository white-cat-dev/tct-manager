<div class="products-page" ng-init="init()">
	<h1>Продукты</h1>

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
			<a href="{{ route('product-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить продукт
			</a>
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
	
	<table class="table table-with-buttons">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Размер</th>
			<th>Цвета</th>
			<th>Цена</th>
			<th>Наличие</th>
			<th>Выдача</th>
			<th>Свободно</th>
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
				@{{ productGroup.length }}x@{{ productGroup.width }}x@{{ productGroup.depth }}
			</td>
			<td>
				<div ng-repeat="product in productGroup.products">
					@{{ product.color_text }}
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
				<div ng-repeat="product in productGroup.products">
					@{{ product.realize_in_stock }} м<sup>2</sup>
				</div>
			</td>
			<td>
				<div ng-repeat="product in productGroup.products">
					@{{ product.free_in_stock }} м<sup>2</sup>
				</div>
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ productGroup.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					<a ng-href="@{{ productGroup.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="delete(productGroup.id)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>
			</td>
		</tr>
	</table>
</div>