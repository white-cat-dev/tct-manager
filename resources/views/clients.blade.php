<div class="clients-block" ng-init="init()">
	<h1>Клиенты</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Поиск...">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button">Поиск</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			<a href="{{ route('client-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Добавить клиента
			</a>
		</div>
	</div>

	<table class="table">
		<tr>
			<th>№</th>
			<th>Имя</th>
			<th>Номер телефона</th>
			<th>Электронная почта</th>
			<th></th>
			<th></th>
		</tr>

		<tr ng-repeat="client in clients">
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
				<a href="">Заказы</a>
			</td>
			<td>
				<div class="btn-group" role="group">
					<a ng-href="@{{ client.url }}" class="btn btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					<a ng-href="@{{ client.url + '/edit' }}" class="btn btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					<button type="button" class="btn btn-primary" ng-click="delete(client.id)">
						<i class="far fa-trash-alt"></i>
					</button>
				</div>
			</td>
		</tr>
	</table>
</div>