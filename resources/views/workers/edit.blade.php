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
			<button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-cog"></i> Доступные действия
			</button>
			<div class="dropdown-menu" aria-labelledby="actionsButton">
				<button type="button" class="btn-sm dropdown-item" ng-if="worker.status == {{ App\Worker::STATUS_ACTIVE }} && !worker.status_date" ng-click="showStatusModal(worker, {{ App\Worker::STATUS_INACTIVE }})">
					Отстранить от работы
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="worker.status == {{ App\Worker::STATUS_INACTIVE }} && !worker.status_date" ng-click="showStatusModal(worker, {{ App\Worker::STATUS_ACTIVE }})">
					Вернуть на работу
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="worker.status == {{ App\Worker::STATUS_INACTIVE }} && worker.status_date" ng-click="showStatusModal(worker, {{ App\Worker::STATUS_INACTIVE }})">
					Отменить возвращение на работу
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="worker.status == {{ App\Worker::STATUS_INACTIVE }} && worker.status_date" ng-click="showStatusModal(worker, {{ App\Worker::STATUS_ACTIVE }})">
					Изменить дату возвращения на работу
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="worker.status == {{ App\Worker::STATUS_ACTIVE }} && worker.status_date" ng-click="showStatusModal(worker, {{ App\Worker::STATUS_ACTIVE }})">
					Отменить отстранение от работы
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="worker.status == {{ App\Worker::STATUS_ACTIVE }} && worker.status_date" ng-click="showStatusModal(worker, {{ App\Worker::STATUS_INACTIVE }})">
					Изменить дату отстранения от работы
				</button>
			</div>
			<a ng-href="@{{ worker.url }}" class="btn btn-primary" ng-if="worker.id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="worker.id" ng-click="delete(worker.id)">
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
					<div class="param-label">Рабочее имя</div>
					<input type="text" class="form-control" ng-model="worker.name" ng-class="{'is-invalid': workerErrors.name}">
					<small class="form-text">
						Используется в таблицах
					</small>
				</div>

				<div class="form-group">
					<div class="param-label">Цех</div>
					<ui-select theme="bootstrap" ng-model="worker.facility_id">
			            <ui-select-match placeholder="Выберите цех из списка">
				            @{{ $select.selected.name }}
				        </ui-select-match>
			            <ui-select-choices repeat="facility.id as facility in facilities">
			                <span ng-bind-html="facility.name | highlight: $select.search"></span>
			            </ui-select-choices>
					</ui-select>
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
</div>