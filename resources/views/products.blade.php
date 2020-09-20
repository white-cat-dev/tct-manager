<div class="products-page" ng-init="init()">
	<h1>Продукты</h1>

	@include('partials.loading')

	@if (Auth::user() && Auth::user()->type == 'admin')
	<a href="{{ route('product-create') }}" class="btn btn-primary top-right-button">
		<i class="fas fa-plus"></i>
	</a>
	@endif

	<div class="top-buttons-block">
		<div class="left-buttons" ng-init="count=0">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery" ng-keypress="searchInputKeyPressed($event)">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> <span class="d-none d-md-inline">Поиск</span>
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons d-none d-md-flex">
			<button type="button" class="btn btn-primary" ng-click="loadExportFile()">
				<i class="fas fa-file-excel"></i> Скачать
			</button>

			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('product-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать продукт
			</a>
			@endif
		</div>
	</div>

	<div class="categories-menu-block" ng-init="isCategoriesShown = false">	
		<div class="categories-menu" ng-class="{'shown': isCategoriesShown}" ng-click="isCategoriesShown = !isCategoriesShown">
			<div class="categories-title btn d-block d-md-none">
				<span ng-if="isCategoriesShown">Выберите категорию...</span>
				<span ng-if="currentCategory == 0 && !isCategoriesShown">Все категории</span>
				<span ng-repeat="category in categories" ng-if="currentCategory == category.id && !isCategoriesShown">@{{ category.name }}</span>

				<div class="icon">
					<i class="fas fa-caret-down"></i>
				</div>
			</div>

			<button type="button" class="btn" ng-class="{'active': currentCategory == 0}" ng-click="chooseCategory(0)">
				Все категории
			</button>
			<button type="button" ng-repeat="category in categories" class="btn" ng-class="{'active': currentCategory == category.id}" ng-click="chooseCategory(category.id)">
				@{{ category.name }}
			</button>
		</div>

		<div class="in-stock-category-block">
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="checkbox" class="custom-control-input" ng-model="isStockProductsShown" id="checkboxStock" ng-change="chooseInStock(false)">
				<label class="custom-control-label" for="checkboxStock">
					В наличии
				</label>
			</div>
			<div class="custom-control custom-checkbox custom-control-inline">
				<input type="checkbox" class="custom-control-input" ng-model="isFreeStockProductsShown" id="checkboxFreeStock" ng-change="chooseInStock(true)">
				<label class="custom-control-label" for="checkboxFreeStock">
					Свободно
				</label>
			</div>
		</div>
	</div>
	
	<div>
		<table class="table main-table table-with-buttons d-none d-lg-table" ng-if="(productGroups | filter: {'name': searchQuery}).length > 0">
			<tr>
				<th><div>№</div></th>
				<th><div>Название</div></th>
				<th><div>Виды</div></th>
				<th><div>Цена</div></th>
				<th><div>В наличии</div></th>
				<th><div>Заказано</div></th>
				<th><div>Свободно</div></th>
				<th><div>&nbsp;</div></th>
			</tr>
		
			<tr ng-repeat="(productGroupNum, productGroup) in productGroups | filter: {'name': searchQuery}"  ng-init="isProductsListShown = true">
				<td>
					@{{ $index + 1 }}
				</td>
				<td>
					@{{ productGroup.name }} 
					@{{ productGroup.size }}
				</td>
				<td style="width: 12%;">
					<div class="products-list" ng-class="{'shown': isProductsListShown || productGroup.products.length <= 3}">
						<div ng-repeat="product in productGroup.products">
							<span ng-if="product.variation_text">@{{ product.variation_text }}</span>
							<span ng-if="!product.variation_text">—</span>
						</div>
					</div>

					<button type="button" class="btn btn-link" ng-show="productGroup.products.length > 3" ng-click="isProductsListShown = !isProductsListShown">
						<span ng-if="!isProductsListShown">все виды</span>
						<span ng-if="isProductsListShown">скрыть</span>
					</button>
				</td>
				<td style="width: 12%;">
					<div class="products-list" ng-class="{'shown': isProductsListShown || productGroup.products.length <= 3}">
						<div ng-repeat="product in productGroup.products">
							@{{ product.price }} руб.
						</div>
					</div>
				</td>
				<td style="width: 12%;">
					<div class="products-list" ng-class="{'shown': isProductsListShown || productGroup.products.length <= 3}">
						<div ng-repeat="(productNum, product) in productGroup.products" class="btn-link-block" ng-click="showProductStockModal(productGroup, productNum)">
							<span>
								@{{ product.in_stock }}
								<span ng-bind-html="product.units_text"></span>
							</span>
							<button type="button" class="btn btn-link">
								<i class="fas fa-pencil-alt"></i>
							</button>
						</div>
					</div>
				</td>
				<td style="width: 12%;">
					<div class="products-list" ng-class="{'shown': isProductsListShown || productGroup.products.length <= 3}">
						<div ng-repeat="product in productGroup.products" class="btn-link-block" ng-click="showProductOrdersModal(product)">
							<span>
								@{{ product.planned }} 
								<span ng-bind-html="product.units_text"></span>
							</span>
							<button type="button" class="btn btn-link">
								<i class="far fa-question-circle"></i>
							</button>
						</div>
					</div>
				</td>
				<td style="width: 12%;">
					<div class="products-list" ng-class="{'shown': isProductsListShown || productGroup.products.length <= 3}">
						<div ng-repeat="product in productGroup.products">
							@{{ product.free_in_stock }} 
							<span ng-bind-html="product.units_text"></span>
						</div>
					</div>
				</td>
				<td>
					<div class="btn-group">
						<a ng-href="@{{ productGroup.url }}" class="btn btn-sm btn-primary">
							<i class="fas fa-eye"></i>
						</a>
						@if (Auth::user() && Auth::user()->type == 'admin')
						<a ng-href="@{{ productGroup.url + '/edit' }}" class="btn btn-sm btn-primary">
							<i class="fas fa-edit"></i>
						</a>
						<button type="button" class="btn btn-sm btn-primary" ng-click="copy(productGroup.id)">
							<i class="fas fa-copy"></i>
						</button>
						{{-- <button type="button" class="btn btn-sm btn-primary" ng-click="showDelete(productGroup)">
							<i class="far fa-trash-alt"></i>
						</button> --}}
						@endif
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<div class="product-groups-block d-block d-lg-none" ng-if="productGroups.length > 0">
		<div class="product-group-block" ng-repeat="(productGroupNum, productGroup) in productGroups | filter: {'name': searchQuery}">
			<div class="product-group-title">
				<div>
					@{{ productGroup.name }}
					@{{ productGroup.size }}
				</div>

				<div class="btn-group" role="group">
					<a ng-href="@{{ productGroup.url }}" class="btn btn-sm btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ productGroup.url + '/edit' }}" class="btn btn-sm btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-sm btn-primary" ng-click="copy(productGroup.id)">
						<i class="fas fa-copy"></i>
					</button>
					{{-- <button type="button" class="btn btn-sm btn-primary" ng-click="delete(productGroup.id)">
						<i class="far fa-trash-alt"></i>
					</button> --}}
					@endif
				</div>
			</div>

			<table class="table table-sm main-table">
				<tr ng-repeat="(productNum, product) in productGroup.products">
					<td>
						<span ng-if="product.variation_text">@{{ product.variation_text }}</span>
						<span ng-if="!product.variation_text">—</span>
					</td>
					<td>
						<div class="edit-field" ng-init="product.new_in_stock = product.in_stock">
							<input type="text" class="form-control" ng-model="product.new_in_stock" ng-blur="saveEditField(productGroupNum, productNum, 'in_stock')" ng-keypress="inputKeyPressed($event)" ng-change="inputFloat(product, 'new_in_stock')">
							<span class="units">
								@{{ product.new_in_stock }}
								<span ng-bind-html="product.units_text"></span>
							</span>
						</div>
					</td>
					<td>
						Свободно: @{{ product.free_in_stock }} 
						<span ng-bind-html="product.units_text"></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="no-data-block" ng-if="(productGroups | filter: {'name': searchQuery}).length == 0 && !isLoading">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного продукта <br>
		<small ng-if="isStockProductsShown">в наличии</small>
		<small ng-if="searchQuery"> по запросу «@{{ searchQuery }}»</small>
		<small ng-if="currentCategory != 0">в категории «<span ng-repeat="category in categories" ng-if="category.id == currentCategory">@{{ category.name }}</span>»</small>

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('product-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать новый продукт
			</a>
		</div>
		@endif
	</div>

	@include('partials.delete-modal')
	@include('partials/product-orders-modal')
	@include('partials/product-stock-modal')
</div>