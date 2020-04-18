<div class="facilities-page" ng-init="init()">
	<h1>Цехи</h1>

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
			<a href="{{ route('facility-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить новый цех
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-lg-6 col-xl-4" ng-repeat="facility in facilities | filter: searchQuery">
			<div class="facility-block" ng-class="{'paused': facility.status == 'paused'}">
				<div class="facility-content">
					<div class="btn-group" role="group">
						<a ng-href="@{{ facility.url }}" class="btn btn-primary btn-sm">
							<i class="fas fa-eye"></i>
						</a>
						<a ng-href="@{{ facility.url + '/edit' }}" class="btn btn-primary btn-sm">
							<i class="fas fa-edit"></i>
						</a>
						<button type="button" class="btn btn-primary btn-sm" ng-click="deleteFacility(facility.id)">
							<i class="far fa-trash-alt"></i>
						</button>
					</div>

					<div class="facility-title">
						@{{ facility.name }}
					</div>
					<div class="row align-items-center">
						<div class="col-5">
							<div class="facility-icon">
								<img src="{{ url('/images/facility.png')}}">
								<img src="{{ url('/images/facility-works.png')}}" style="height: 30%">
							</div>
							<div class="facility-performance">
								@{{ facility.icon }}
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

					<div class="facility-params">
						<div class="param-name">
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

				<div class="buttons-block">
					<button type="button" class="btn btn-primary btn-sm" ng-click="facility.status = 'paused'; save(facility)" ng-if="facility.status == 'active'">
						Приостановить работу цеха
					</button>
					<button type="button" class="btn btn-primary btn-sm" ng-click="facility.status = 'active'; save(facility)" ng-if="facility.status == 'paused'">
						Восстановить работу цеха
					</button>
				</div>
			</div>
		</div>
	</div>
</div>