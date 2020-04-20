<div class="facilities-page" ng-init="initShow()">
	<h1>Просмотр цеха</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('facilities') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку цехов
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ facility.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
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