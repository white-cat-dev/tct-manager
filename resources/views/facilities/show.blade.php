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
		<div class="row justify-content-around">
			<div class="col-11">
				<div class="show-block-title">
					@{{ facility.name }}
				</div>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-5">
				<div class="params-title">
					Общая информация
				</div>
				<div class="param-block">
					<div class="param-name">
						Название цеха
					</div>
					<div class="param-value">
						@{{ facility.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Категории
					</div>
					<div class="param-value">
						<span ng-repeat="category in facility.categories">
							@{{ category.name }} <br>
						</span>
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Производительность цеха
					</div>
					<div class="param-value">
						@{{ facility.performance }} замесов в день
					</div>
				</div>
				<div class="param-block">
					<div class="param-name">
						Статус цеха
					</div>
					<div class="param-value">
						@{{ facility.status_text }}
					</div>
				</div>
			</div>

			<div class="col-5">
				<div class="params-title">
					Список работников
				</div>

				<table class="table" ng-if="facility.workers.length > 0">
					<tr>
						<th>№</th>
						<th>Имя</th>
						<th>Текущий статус</th>
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
					</tr>
				</table>

				<div ng-if="facility.workers.length == 0">
					Нет работников
				</div>
			</div>
		</div>
	</div>

	@include('partials.facility-status-modal')
</div>