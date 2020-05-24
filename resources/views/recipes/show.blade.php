<div class="recipes-page" ng-init="initShow()">
	<h1>Просмотр рецепта</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('materials') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку материалов
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ material.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-center">
			
		</div>
	</div>
</div>