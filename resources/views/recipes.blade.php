<div class="recipes-page" ng-init="init()">
	<h1>Рецепты</h1>

	@include('partials.loading')

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
			<a href="{{ route('recipe-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать рецепт
			</a>
			@endif
		</div>
	</div>

	<table class="table main-table table-with-buttons" ng-if="(recipes | filter: {'name': searchQuery}).length > 0">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Материалы</th>
			<th>Стоимость</th>
			<th></th>
		</tr>

		<tr ng-repeat="recipe in recipes | filter: {'name': searchQuery}">
			<td>
				@{{ $index + 1 }}
			</td>
			<td>
				@{{ recipe.name }}
			</td>
			<td ng-init="isMaterialsListShown = false">
				<div class="materials-list" ng-class="{'shown': isMaterialsListShown || recipe.material_groups.length <= 3}">
					<div ng-repeat="materialGroup in recipe.material_groups">
						@{{ materialGroup.name }} –
						@{{ materialGroup.pivot.count | number }}
						<span ng-switch on="materialGroup.units">
							<span ng-switch-when="volume_l">л</span>
							<span ng-switch-when="volume_ml">мл</span>
							<span ng-switch-when="weight_kg">кг</span>
							<span ng-switch-when="weight_t">т</span>
						</span>
					</div>
				</div>
				<button type="button" class="btn btn-link" ng-show="recipe.material_groups.length > 3" ng-click="isMaterialsListShown = !isMaterialsListShown">
					<span ng-if="!isMaterialsListShown">Показать все материалы</span>
					<span ng-if="isMaterialsListShown">Скрыть все материалы</span>
				</button>
				<div ng-if="recipe.material_groups.length == 0">
					Нет материалов
				</div>
			</td>
			<td>
				@{{ recipe.cost | number }} руб
			</td>
			<td>
				<div class="btn-group">
					<a ng-href="@{{ recipe.url }}" class="btn btn-sm btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ recipe.url + '/edit' }}" class="btn btn-sm btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-sm btn-primary" ng-click="showDelete(recipe)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</td>
		</tr>
	</table>

	<div class="no-data-block" ng-if="(recipes | filter: {'name': searchQuery}).length == 0 && !isLoading">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного рецепта <br>
		<small ng-if="searchQuery"> по запросу "@{{ searchQuery }}"</small>

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('recipe-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать новый рецепт
			</a>
		</div>
		@endif
	</div>

	@include('partials.delete-modal')
</div>