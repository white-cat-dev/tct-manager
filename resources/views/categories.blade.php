<div class="categories-block" ng-init="init()">
	<h1>Категории</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Поиск...">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button">Поиск</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			<a href="{{ route('category-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить категорию
			</a>
		</div>
	</div>

	<table class="table">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th></th>
			<th></th>
		</tr>

		<tr ng-repeat="category in categories">
			<td>
				@{{ category.id }}
			</td>
			<td>
				@{{ category.name }}
			</td>
			<td>
				<a href="">Продукты</a>
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