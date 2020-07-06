<div class="workers-page" ng-init="init()">
	<h1>Работники</h1>

	@include('partials.loading')

	<div ng-if="!isLoading">
		<div class="top-buttons-block">
			<div class="left-buttons">
				<div class="input-group search-group">
					<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery" ng-keypress="searchInputKeyPressed($event)">
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
					<i class="fas fa-plus"></i> Создать работника
				</a>
				@endif
			</div>
		</div>

		<table class="table main-table table-with-buttons" ng-if="(workers | filter: {'name': searchQuery}).length > 0">
			<tr>
				<th><div>№</div></th>
				<th><div>Имя</div></th>
				<th><div>Полное имя</div></th>
				<th><div>Номер телефона</div></th>
				<th><div>Статус</div></th>
				<th><div>&nbsp;</div></th>
			</tr>
			<tr ng-repeat="worker in workers | filter: {'name': searchQuery}">
				<td>
					@{{ $index + 1 }}
				</td>
				<td>
					@{{ worker.name }}
				</td>
				<td>
					@{{ worker.surname_name_patronymic }}
				</td>
				<td>
					<span ng-if="worker.formatted_phone">
						@{{ worker.formatted_phone }}
					</span>
					<span ng-if="!worker.formatted_phone">
						Не указан
					</span>
				</td>
				<td>
					<span class="worker-status" ng-class="{'active': worker.status == {{ App\Worker::STATUS_ACTIVE }} }">
						@{{ worker.status_text }}
					</span>
				</td>
				<td>
					<div class="btn-group">
						<a ng-href="@{{ worker.url }}" class="btn btn-primary btn-sm">
							<i class="fas fa-eye"></i>
						</a>

						@if (Auth::user() && Auth::user()->type == 'admin')
						<a ng-href="@{{ worker.url + '/edit' }}" class="btn btn-primary btn-sm">
							<i class="fas fa-edit"></i>
						</a>				

						<button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="actionsButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-cog"></i> Действия
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionsButton">
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
							@endif
						</div>
					</div>
				</td>
			</tr>
		</table>

		<div class="no-data-block" ng-if="(workers | filter: {'name': searchQuery}).length == 0 && !isLoading">
			<div class="icon">
				<i class="fas fa-th"></i>
			</div>
			Не найдено ни одного работника <br>
			<small ng-if="searchQuery"> по запросу "@{{ searchQuery }}"</small>

			@if (Auth::user() && Auth::user()->type == 'admin')
			<div>
				<a href="{{ route('worker-create') }}" class="btn btn-primary">
					<i class="fas fa-plus"></i> Создать нового работника
				</a>
			</div>
			@endif
		</div>
	</div>

	@include('partials.worker-status-modal')
	@include('partials.delete-modal')
</div>