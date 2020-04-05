<div class="clients-block" ng-init="initShow()">
	<h1>@{{ client.name }}</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('categories') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ client.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="client.id" ng-click="delete(client.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>


	<div class="show-block">
		<div class="param-block">
			<div class="param-name">
				Имя
			</div>
			<div class="param-value">
				@{{ client.name }}
			</div>
		</div>

		<div class="param-block">
			<div class="param-name">
				Номер телефона
			</div>
			<div class="param-value">
				@{{ client.phone }}
			</div>
		</div>

		<div class="param-block">
			<div class="param-name">
				Электронная почта
			</div>
			<div class="param-value">
				@{{ client.email }}
			</div>
		</div>
	</div>
</div>