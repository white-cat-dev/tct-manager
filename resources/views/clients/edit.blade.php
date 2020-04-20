<div class="clients-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового клиента</h1>
	<h1 ng-if="id">Редактирование данных клиента</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('clients') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку клиентов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ client.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': showAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('clients') }}" class="btn-link">перейти к списку клиентов</a> или <a href="{{ route('client-create') }}" class="btn-link">создать нового клиента</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>


	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-6">
				<div class="form-group">
					<div class="param-label">Имя</div>
					<input type="text" class="form-control" ng-model="client.name" ng-class="{'is-invalid': clientErrors.name}">
				</div>

				<div class="form-group">
					<div class="param-label">Номер телефона</div>
					<input type="text" class="form-control" ng-model="client.phone" ng-class="{'is-invalid': clientErrors.phone}">
				</div>

				<div class="form-group">
					<div class="param-label">Электронная почта</div>
					<input type="text" class="form-control" ng-model="client.email" ng-class="{'is-invalid': clientErrors.email}">
				</div>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>