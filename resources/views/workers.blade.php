<div class="workers-page" ng-init="init()">
	<h1>Работники</h1>

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
			<a href="{{ route('worker-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить нового работника
			</a>
		</div>
	</div>

	<table class="table" ng-if="workers.length > 0">
		<tr>
			<th>№</th>
			<th>Рабочее имя</th>
			<th>Полное имя</th>
			<th>Текущий статус</th>
			<th>Цех</th>
			<th></th>
		</tr>

		<tr ng-repeat="worker in workers | filter: searchQuery">
			<td>
				@{{ $index + 1 }}
			</td>
			<td>
				@{{ worker.name }}
			</td>
			<td>
				@{{ worker.surname }} @{{ worker.full_name }} @{{ worker.patronymic }}
			</td>
			<td>
				@{{ worker.status_text }}
			</td>
			<td>
				@{{ worker.facility ? worker.facility.name : 'Цех не выбран' }}
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ worker.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					<a ng-href="@{{ worker.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="deleteWorker(worker.id)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>
			</td>
		</tr>
	</table>
</div>