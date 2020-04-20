<div class="clients-block" ng-init="initShow()">
	<h1>Просмотр данных клиента</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('clients') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку клиентов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ client.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-around">
			<div class="col-6">
				<div class="show-block-title">
					Клиент "@{{ client.name }}"
				</div>

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
	</div>
</div>