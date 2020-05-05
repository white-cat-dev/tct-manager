<div class="facilities-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового цеха</h1>
	<h1 ng-if="id">Редактирование цеха</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('facilities') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку цехов
			</a>
		</div>

		<div class="right-buttons">
			<button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-cog"></i> Доступные действия
			</button>
			<div class="dropdown-menu" aria-labelledby="actionsButton">
				<button type="button" class="btn-sm dropdown-item" ng-if="facility.status == {{ App\Worker::STATUS_ACTIVE }} && !facility.status_date" ng-click="showStatusModal(facility, {{ App\Worker::STATUS_INACTIVE }})">
					Приостановить работу цеха
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="facility.status == {{ App\Worker::STATUS_INACTIVE }} && !facility.status_date" ng-click="showStatusModal(facility, {{ App\Worker::STATUS_ACTIVE }})">
					Возобновить работу цеха
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="facility.status == {{ App\Worker::STATUS_INACTIVE }} && facility.status_date" ng-click="showStatusModal(facility, {{ App\Worker::STATUS_INACTIVE }})">
					Отменить возобновление работы цеха
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="facility.status == {{ App\Worker::STATUS_INACTIVE }} && facility.status_date" ng-click="showStatusModal(facility, {{ App\Worker::STATUS_ACTIVE }})">
					Изменить дату возобновления работы цеха
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="facility.status == {{ App\Worker::STATUS_ACTIVE }} && facility.status_date" ng-click="showStatusModal(facility, {{ App\Worker::STATUS_ACTIVE }})">
					Отменить приостановку работы цеха
				</button>
				<button type="button" class="btn-sm dropdown-item" ng-if="facility.status == {{ App\Worker::STATUS_ACTIVE }} && facility.status_date" ng-click="showStatusModal(facility, {{ App\Worker::STATUS_INACTIVE }})">
					Изменить дату приостановки работы цеха
				</button>
			</div>
			<a ng-href="@{{ facility.url }}" class="btn btn-primary" ng-if="id">
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
			Вы можете <a href="{{ route('facilities') }}" class="btn-link">перейти к списку цехов</a> или <a href="{{ route('facility-create') }}" class="btn-link">создать новый цех</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-5">
				<div class="params-title">
					Общая информация
				</div>

				<div class="form-group">
					<div class="param-label">Название цеха</div>
					<input type="text" class="form-control" ng-model="facility.name" ng-class="{'is-invalid': facilityErrors.name}">
				</div>

				<div class="form-group">
					<div class="param-label">Категории</div>

					<div class="custom-control custom-checkbox" ng-repeat="category in categories">
						<input type="checkbox" class="custom-control-input" ng-model="facility.categories[category.id]" id="checkbox@{{ category.id }}">
						<label class="custom-control-label" for="checkbox@{{ category.id }}">
							@{{ category.name }}
						</label>
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">Производительность цеха</div>
					<input type="text" class="form-control" ng-model="facility.performance" ng-class="{'is-invalid': facilityErrors.performance}">
					<small class="form-text">
						Информация о количестве замесов в день необходима для планирования производства
					</small>
				</div>

				<div class="form-group facility-edit-status-block">
					<div class="param-label">Статус цеха</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" ng-model="facility.status" id="radioActive" value="{{ App\Facility::STATUS_ACTIVE }}">
						<label class="custom-control-label" for="radioActive">Работает</label>
					</div>
					{{-- <div class="custom-control custom-radio">
						<div>
							<input class="custom-control-input" type="radio" ng-model="facility.status" id="radioPlanPaused" value="plan-paused">
							<label class="custom-control-label" for="radioPlanPaused">
								Работает до
								<span ng-if="facility.status != 'plan-paused'">...</span>
								<span ng-if="facility.status == 'plan-paused'">
									@{{ facility.status_date_from_raw | date: 'dd.MM.yyyy' }}
								</span>
							</label>
							<button type="button" class="btn btn-primary btn-sm" ng-click="showStatusModal(facility, 'plan-paused')" ng-if="facility.status == 'plan-paused'">
								<i class="far fa-calendar-alt"></i> Выбрать дату
							</button>
						</div>
					</div> --}}
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" ng-model="facility.status" id="radioPaused" value="{{ App\Facility::STATUS_INACTIVE }}">
						<label class="custom-control-label" for="radioPaused">Не работает</label>
					</div>
					{{-- <div class="custom-control custom-radio">
						<div>
							<input class="custom-control-input" type="radio" ng-model="facility.status" id="radioPlanActive" value="plan-active">
							<label class="custom-control-label" for="radioPlanActive">
								Не работает до
								<span ng-if="facility.status != 'plan-active'">...</span>
								<span ng-if="facility.status == 'plan-active'">
									@{{ facility.status_date_from_raw | date: 'dd.MM.yyyy' }}
								</span>
							</label>
							<button type="button" class="btn btn-primary btn-sm" ng-click="showStatusModal(facility, 'plan-active')" ng-if="facility.status == 'plan-active'">
								<i class="far fa-calendar-alt"></i> Выбрать дату
							</button>
						</div>
					</div> --}}
				</div>
			</div>

			<div class="col-6">
				<div class="params-title">
					Список работников
				</div>

				<table class="table table-with-buttons" ng-if="facility.workers.length > 0">
					<tr>
						<th>№</th>
						<th>Имя</th>
						<th>Текущий статус</th>
						<th></th>
					</tr>
					<tr ng-repeat="worker in facility.workers">
						<td>
							@{{ $index + 1 }}
						</td>
						<td>
							@{{ worker.name }}
						</td>
						<td>
							@{{ worker.status_text }}
						</td>
						<td>
							<button type="button" class="btn btn-primary" ng-click="deleteWorker($index)">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>
				</table>

				<div class="add-worker-block" ng-if="isAddWorkerShown">
					<div class="input-group">
						<ui-select theme="bootstrap" ng-model="newWorker.data" ng-change="addWorker()">
				            <ui-select-match placeholder="Выберите из списка">
					            @{{ $select.selected.name }}
					        </ui-select-match>
				            <ui-select-choices repeat="worker in workers | filter: $select.search">
				                <span ng-bind-html="workers[$index].name + ' (' + (workers[$index].facility ? workers[$index].facility.name : 'Цех не выбран') + ')' | highlight: $select.search"></span>
				            </ui-select-choices>
						</ui-select>

						<div class="input-group-append">
					    	<button class="btn btn-primary" type="button" ng-disabled="!newWorker.data" ng-click="addWorker()">
						    	Добавить
						    </button>
					 	</div>
					</div>
					<div class="divider">
						или
					</div>
					<a href="{{ route('worker-create') }}" target="_blank" class="btn btn-primary">
						Создайте нового работника
					</a>
				</div>

				<button type="button" class="btn btn-primary" ng-click="showAddWorker()" ng-if="!isAddWorkerShown">
					<i class="fas fa-plus"></i> Добавить работника в цех	
				</button>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>

	@include('partials.facility-status-modal')
</div>