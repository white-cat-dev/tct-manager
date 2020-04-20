<div class="clients-page" ng-init="init()">
	<h1>Клиенты</h1>

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
			<a href="{{ route('client-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить клиента
			</a>
			@endif
		</div>
	</div>

	<table class="table table-with-buttons" ng-if="clients.length > 0">
		<tr>
			<th>№</th>
			<th>Имя</th>
			<th>Номер телефона</th>
			<th>Электронная почта</th>
			<th></th>
		</tr>

		<tr ng-repeat="client in clients | filter: searchQuery">
			<td>
				@{{ client.id }}
			</td>
			<td>
				@{{ client.name }}
			</td>
			<td>
				@{{ client.phone }}
			</td>
			<td>
				@{{ client.email }}
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ client.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ client.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="delete(client.id)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</td>
		</tr>
	</table>

	<div class="no-data-block" ng-if="clients.length == 0">
		<div>
			<i class="fas fa-th"></i>
		</div>
		Вы еще не добавили ни одного клиента
	</div>
</div>