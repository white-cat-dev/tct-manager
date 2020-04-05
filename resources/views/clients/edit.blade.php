<div class="clients-block" ng-init="initEdit()">
	<h1 ng-if="client.id">@{{ client.name }}</h1>
	<h1 ng-if="!client.id">Новый клиент</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('clients') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ client.url }}" class="btn btn-primary" ng-if="client.id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="client.id" ng-click="delete(client.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block">
		<div class="form-group">
			<label>Имя</label>
			<input type="text" class="form-control" ng-model="clientData['name']" ng-class="{'is-invalid': clientErrors['name']}">
			<div class="invalid-feedback" ng-if="clientErrors['name']">
				@{{ clientErrors['name'][0] }}
			</div>
		</div>

		<div class="form-group">
			<label>Номер телефона</label>
			<input type="text" class="form-control" ng-model="clientData['phone']" ng-class="{'is-invalid': clientErrors['phone']}">
			<div class="invalid-feedback" ng-if="clientErrors['phone']">
				@{{ clientErrors['phone'][0] }}
			</div>
		</div>

		<div class="form-group">
			<label>Электронная почта</label>
			<input type="text" class="form-control" ng-model="clientData['email']" ng-class="{'is-invalid': clientErrors['email']}">
			<div class="invalid-feedback" ng-if="clientErrors['email']">
				@{{ clientErrors['email'][0] }}
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save('{{ route('clients', [], false) }}')">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>