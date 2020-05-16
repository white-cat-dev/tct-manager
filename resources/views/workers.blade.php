<div class="workers-page" ng-init="init()">
	<h1>Работники</h1>

	@include('partials.top-alerts')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> Поиск
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('worker-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить нового работника
			</a>
			@endif
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-lg-6 col-xl-4" ng-repeat="worker in workers | filter: {'name': searchQuery}">
			<div class="worker-block">
				<div class="btn-group">
					<a ng-href="@{{ worker.url }}" class="btn btn-primary btn-sm">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ worker.url + '/edit' }}" class="btn btn-primary btn-sm">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary btn-sm" ng-click="delete(worker.id)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>

				<div class="worker-title">
					@{{ worker.name }}
				</div>

				<div class="row align-items-center">
					<div class="col-4">
						<div class="worker-icon">
							<img src="{{ url('/images/worker.png')}}">
							<img src="{{ url('/images/worker-active.png')}}" ng-if="worker.status == {{ App\Worker::STATUS_ACTIVE }}">
						</div>
						<div class="worker-status">
							@{{ worker.status_text }}
						</div>
						<div class="worker-vacation">
							@{{ worker.vacation_text }}
						</div>
					</div>
					<div class="col-8">
						<div class="worker-param">
							<div class="param-name">
								Полное имя
							</div>
							@{{ worker.surname }} <br> @{{ worker.full_name }} @{{ worker.patronymic }}
						</div>

						<div class="worker-param">
							<div class="param-name">
								Номер телефона
							</div>
							79085234132
						</div>
					</div>
				</div>

				<div class="buttons-block">
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
				</div>
			</div>
		</div>
	</div>

	@include('partials.worker-status-modal')
</div>