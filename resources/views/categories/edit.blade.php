<div class="categories-block" ng-init="initEdit()">
	<h1 ng-if="category.id">@{{ category.name }}</h1>
	<h1 ng-if="!category.id">Новая категория</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('categories') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ category.url }}" class="btn btn-primary" ng-if="category.id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="category.id" ng-click="delete(category.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block">
		<div class="form-group">
			<label>Название</label>
			<input type="text" class="form-control" ng-model="categoryData['name']" ng-class="{'is-invalid': categoryErrors['name']}">
			<div class="invalid-feedback" ng-if="categoryErrors['name']">
				@{{ categoryErrors['name'][0] }}
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save('{{ route('categories', [], false) }}')">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>