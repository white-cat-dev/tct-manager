<div class="products-page" ng-init="init()">
	<h1>Продукты</h1>

	@include('partials.top-alerts')

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
			    		<i class="fas fa-search"></i> <span class="d-none d-md-inline">Поиск
			    		</span>
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

		<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" ng-model="isStockProductsShown" id="checkboxStock">
			<label class="custom-control-label" for="checkboxStock">
				Только в наличии
			</label>
		</div>
	</div>
	
	<div>
		<table class="table main-table table-with-buttons d-none d-lg-table" ng-if="(productGroups | filter: {'name': searchQuery}).length > 0">
			<tr>
				<th>№</th>
				<th>Название</th>
				<th>Размер</th>
				<th>Виды</th>
				<th>Цена</th>
				<th>В наличии</th>
				<th>Свободно</th>
				<th></th>
			</tr>
		
			<tr ng-repeat="(productGroupNum, productGroup) in productGroups | filter: {'name': searchQuery}" ng-if="!isStockProductsShown || productGroup.in_stock > 0">
				<td>
					@{{ $index + 1 }}
				</td>
				<td>
					@{{ productGroup.name }}
				</td>
				<td>
					@{{ productGroup.size }}
				</td>
				<td style="width: 12%;">
					<div ng-repeat="product in productGroup.products" ng-if="!isStockProductsShown || product.in_stock > 0">
						<span ng-if="product.variation_text">@{{ product.variation_text }}</span>
						<span ng-if="!product.variation_text">—</span>
					</div>
				</td>
				<td style="width: 12%;">
					<div ng-repeat="product in productGroup.products" ng-if="!isStockProductsShown || product.in_stock > 0">
						{{-- <div class="edit-field" ng-show="!isEditFieldShown" ng-click="isEditFieldShown = true; focusNextInput($event);">
							@{{ product.price }} руб.
						</div>
						<input type="text" class="form-control" ng-model="product.price" ng-show="isEditFieldShown" ng-blur="saveEditField('products', productGroupNum); isEditFieldShown = false;" ng-keypress="inputKeyPressed($event)"> --}}
						@{{ product.price }} руб.
					</div>
				</td>
				<td style="width: 12%;">
					<div ng-repeat="(productNum, product) in productGroup.products" ng-if="!isStockProductsShown || product.in_stock > 0">
						<div class="edit-field" ng-show="!isEditFieldShown" ng-click="isEditFieldShown = true; focusNextInput($event);">
							@{{ product.in_stock }} 
							<span ng-switch on="productGroup.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</div>
						<input type="text" class="form-control" ng-model="product.new_in_stock" ng-init="product.new_in_stock = product.in_stock" ng-show="isEditFieldShown" ng-blur="product.in_stock = product.new_in_stock; saveEditField('products', productGroupNum, productNum); isEditFieldShown = false;" ng-keypress="inputKeyPressed($event)">
					</div>
				</td>
				<td style="width: 12%;">
					<div ng-repeat="product in productGroup.products" ng-if="!isStockProductsShown || product.in_stock > 0">
						@{{ product.free_in_stock }} 
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
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
						<button type="button" class="btn btn-sm btn-primary" ng-click="delete(productGroup.id)">
							<i class="far fa-trash-alt"></i>
						</button>
						@endif
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<div class="product-groups-block d-block d-lg-none" ng-if="productGroups.length > 0">
		<div class="product-group-block" ng-repeat="(productGroupNum, productGroup) in productGroups | filter: {'name': searchQuery}" ng-if="!isStockProductsShown || productGroup.in_stock > 0">
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
					<button type="button" class="btn btn-sm btn-primary" ng-click="delete(productGroup.id)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</div>

			<table class="table table-sm main-table">
				<tr ng-repeat="(productNum, product) in productGroup.products" ng-if="!isStockProductsShown || product.in_stock > 0">
					<td>
						<span ng-if="product.variation_text">@{{ product.variation_text }}</span>
						<span ng-if="!product.variation_text">—</span>
					</td>
					<td ng-if="true">
						<div class="edit-field" ng-show="!isEditFieldShown" ng-click="isEditFieldShown = true; focusNextInput($event);">
							@{{ product.price }} руб.
						</div>
						<input type="text" class="form-control" ng-model="product.price" ng-show="isEditFieldShown" ng-blur="saveEditField('products', productGroupNum); isEditFieldShown = false;" ng-keypress="inputKeyPressed($event)">
						</td>
					<td ng-if="true">
						<div class="edit-field" ng-show="!isEditFieldShown" ng-click="isEditFieldShown = true; focusNextInput($event);">
							@{{ product.in_stock }} 
							<span ng-switch on="productGroup.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</div>
						<input type="text" class="form-control" ng-model="product.new_in_stock" ng-init="product.new_in_stock = product.in_stock" ng-show="isEditFieldShown" ng-blur="product.in_stock = product.new_in_stock; saveEditField('products', productGroupNum, productNum); isEditFieldShown = false;" ng-keypress="inputKeyPressed($event)">
					</td>
					<td>
						@{{ product.free_in_stock }} 
						<span ng-switch on="productGroup.category.units">
							<span ng-switch-when="area">м<sup>2</sup></span>
							<span ng-switch-when="volume">м<sup>3</sup></span>
							<span ng-switch-when="unit">шт.</span>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="no-data-block" ng-if="(productGroups | filter: {'name': searchQuery}).length == 0">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного продукта

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('product-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать продукт
			</a>
		</div>
		@endif
	</div>

{{-- 	<div class="top-buttons-block section-buttons-block">
		<div class="left-buttons" ng-init="count=0">
			Материалы
		</div>

		<div class="right-buttons d-none d-md-flex">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('material-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать материал
			</a>
			@endif
		</div>
	</div>

	<table class="table main-table table-with-buttons" ng-if="materials.length > 0">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Единицы измерения</th>
			<th>Цена</th>
			<th>В наличии</th>
			<th></th>
		</tr>

		<tr ng-repeat="(materialNum, material) in materials">
			<td>
				@{{ $index + 1 }}
			</td>
			<td>
				@{{ material.name }}
			</td>
			<td>
				<span ng-repeat="unit in units" ng-if="unit.key == material.units" ng-bind-html="unit.name"> 
				</span>
			</td>
			<td>
				@{{ material.price }} руб.
			</td>
			<td>
				<div class="edit-field" ng-show="!isEditFieldShown" ng-click="isEditFieldShown = true; focusNextInput($event);">
					@{{ material.in_stock }}
					<span ng-switch on="material.units">
						<span ng-switch-when="volume_l">л</span>
						<span ng-switch-when="volume_ml">мл</span>
						<span ng-switch-when="weight_kg">кг</span>
						<span ng-switch-when="weight_t">т</span>
					</span>
				</div>
				<input type="text" class="form-control" ng-model="material.new_in_stock" ng-init="material.new_in_stock = material.in_stock" ng-show="isEditFieldShown" ng-blur="material.in_stock = material.new_in_stock; saveEditField('materials', materialNum); isEditFieldShown = false;" ng-keypress="inputKeyPressed($event)">
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ material.url }}" class="btn btn-sm btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ material.url + '/edit' }}" class="btn btn-sm btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-sm btn-primary" ng-click="delete(material.id)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</td>
		</tr>
	</table>

	<div class="no-data-block" ng-if="materials.length == 0">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного материала

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('material-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать материал
			</a>
		</div>
		@endif
	</div> --}}
</div>