<div class="facilities-page" ng-init="init()">
	<h1>Цехи</h1>

	@include('partials.top-alerts')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> 
			    		<span class="d-none d-inline-md">Поиск</span>
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('facility-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить новый цех
			</a>
			@endif
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-lg-6 col-xl-4" ng-repeat="facility in facilities | filter: searchQuery">
			<div class="facility-block" ng-class="{'paused': facility.status == 'paused'}">
				<div class="btn-group" role="group">
					<a ng-href="@{{ facility.url }}" class="btn btn-primary btn-sm">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ facility.url + '/edit' }}" class="btn btn-primary btn-sm">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary btn-sm" ng-click="delete(facility.id)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>

				<div class="facility-title">
					@{{ facility.name }}
				</div>
				<div class="row align-items-center">
					<div class="col-5">
						<div class="facility-icon">
							<img src="{{ url('/images/facility.png')}}">
							<img src="{{ url('/images/facility-active.png')}}" ng-style="{'height': facility.current_performance / facility.performance * 100 + '%'}">
						</div>
						<div class="facility-status">
							@{{ facility.status_text }}
						</div>
					</div>
					<div class="col-7">
						<div class="facility-params">
							<div class="param-name">
								Категории
							</div>
							<ul>
								<li ng-repeat="category in facility.categories">
									@{{ category.name }}
								</li>
							</ul>

							<div class="param-name">
								Производительность
							</div>
							<div class="param-value">
								@{{ facility.performance }} замесов в день
							</div>
						</div>
					</div>
				</div>

				@if (Auth::user() && Auth::user()->type == 'admin')
				<div class="buttons-block">
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
				</div>
				@endif
			</div>
		</div>
	</div>

	@include('partials.facility-status-modal')
</div>