<div class="workers-page" ng-init="initShow()">
	<h1>Просмотр данных работника</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('workers') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку работников
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
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
			<a ng-href="@{{ worker.url + '/edit' }}" class="btn btn-primary">
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
					Работник "@{{ worker.name }}"
				</div>
			</div>
		</div>
		<div class="row justify-content-around">
			<div class="col-5">
				<div class="param-block">
					<div class="param-name">
						Фамилия
					</div>
					<div class="param-value">
						@{{ worker.surname }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Имя
					</div>
					<div class="param-value">
						@{{ worker.full_name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Отчество
					</div>
					<div class="param-value">
						@{{ worker.patronymic }}
					</div>
				</div>
			</div>

			<div class="col-5">
				<div class="param-block">
					<div class="param-name">
						Рабочее имя
					</div>
					<div class="param-value">
						@{{ worker.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Цех
					</div>
					<div class="param-value">
						@{{ worker.facility.name }}
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('partials.worker-status-modal')
</div>