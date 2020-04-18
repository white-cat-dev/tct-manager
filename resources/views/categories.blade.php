<div class="categories-block" ng-init="init()">
	<h1>Категории</h1>

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
			<a href="{{ route('category-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить категорию
			</a>
		</div>
	</div>

	<table class="table table-with-buttons">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Единицы измерения</th>
			<th>Разновидности</th>
			<th></th>
		</tr>

		<tr ng-repeat="category in categories | filter: searchQuery">
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
				@{{ category.has_colors ? 'Разные цвета' : 'Нет разновидностей'}}
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ category.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					<a ng-href="@{{ category.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="delete(category.id)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>
			</td>
		</tr>
	</table>
</div>