<div class="categories-block" ng-init="initShow()">
	<h1>@{{ category.name }}</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('categories') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ category.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="category.id" ng-click="delete(category.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>


	<div class="show-block">
		<div class="param-block">
			<div class="param-name">
				Название
			</div>
			<div class="param-value">
				@{{ category.name }}
			</div>
		</div>
	</div>
</div>