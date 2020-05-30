<div ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового работника</h1>
	<h1 ng-if="id">Редактирование данных работника</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('workers') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку работников
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ worker.url }}" class="btn btn-primary" ng-if="worker.id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="worker.id" ng-click="showDelete(worker)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': successAlert || errorAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('workers') }}" class="btn-link">перейти к списку работников</a> или <a href="{{ route('worker-create') }}" class="btn-link">создать нового работника </a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-5">
				<div class="form-group">
					<div class="param-label">Рабочее имя</div>
					<input type="text" class="form-control" ng-model="worker.name" ng-class="{'is-invalid': workerErrors.name}">
					<small class="form-text">
						Введите имя, которое будет использоваться в панели
					</small>
				</div>

				<div class="form-group">
					<div class="param-label">Фамилия</div>
					<input type="text" class="form-control" ng-model="worker.surname" ng-class="{'is-invalid': workerErrors.surname}">
				</div>

				<div class="form-group">
					<div class="param-label">Имя</div>
					<input type="text" class="form-control" ng-model="worker.full_name" ng-class="{'is-invalid': workerErrors.full_name}">
				</div>

				<div class="form-group">
					<div class="param-label">Отчество</div>
					<input type="text" class="form-control" ng-model="worker.patronymic" ng-class="{'is-invalid': workerErrors.patronymic}">
				</div>

			</div>

			<div class="col-5">
				<div class="form-group">
					<div class="param-label">Номер телефона</div>
					<input type="text" class="form-control" ui-mask="+7 (9999) 99-99-99" ui-mask-placeholder-char="_" ng-model="worker.phone">
				</div>

				<div class="form-group">
					<div class="param-label">Дата рождения</div>
					<input type="text" class="form-control" ui-mask="99.99.9999" ui-mask-placeholder-char="_" ng-model="worker.birthdate_raw">
				</div>

				<div class="form-group">
					<div class="param-label">Паспортные данные</div>
					<textarea class="form-control" rows="3" ng-model="worker.passport" ng-class="{'is-invalid': workerErrors.passport}"></textarea>
				</div>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>

	@include('partials.worker-status-modal')
	@include('partials.delete-modal')
</div>