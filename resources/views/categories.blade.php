<div class="categories-block" ng-init="init()">
	<h1>Категории</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery" ng-keypress="searchInputKeyPressed($event)">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> Поиск
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('category-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать категорию
			</a>
			@endif
		</div>
	</div>

	<table class="table table-with-buttons" ng-if="(categories | filter: {'name': searchQuery}).length > 0">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Единицы измерения</th>
			<th>Разновидности</th>
			<th></th>
		</tr>

		<tr ng-repeat="category in categories | filter: {'name': searchQuery}">
			<td>
				@{{ category.id }}
			</td>
			<td>
				@{{ category.name }}
			</td>
			<td>
				<span ng-repeat="unit in units" ng-if="unit.key == category.units" ng-bind-html="unit.name"> 
				</span>
			</td>
			<td>
				<span ng-switch on="category.variations">
					<span ng-switch-when="colors">Разновидности по цветам</span>
					<span ng-switch-when="grades">Разновидности по марке бетона</span>
					<span ng-switch-when="">Нет разновидностей</span>
				</span>
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ category.url }}" class="btn btn-sm btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ category.url + '/edit' }}" class="btn btn-sm btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-sm btn-primary" ng-click="showDelete(category)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</td>
		</tr>
	</table>

	<div class="no-data-block" ng-if="(categories | filter: {'name': searchQuery}).length == 0">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одной категории <br>
		<small ng-if="searchQuery"> по запросу "@{{ searchQuery }}"</small>

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('category-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать новую категорию
			</a>
		</div>
		@endif
	</div>

	@include('partials.delete-modal')
</div>