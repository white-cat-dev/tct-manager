<div class="facilities-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового цеха</h1>
	<h1 ng-if="id">Редактирование цеха</h1>

	@include('partials.loading')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('facilities') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку цехов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ facility.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(facility)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block" ng-show="!isLoading">
		<div class="row justify-content-around">
			<div class="col-12 col-lg-8 col-xl-6">
				<div class="form-group">
					<div class="param-label">Название цеха</div>
					<input type="text" class="form-control" ng-model="facility.name" ng-class="{'is-invalid': facilityErrors.name}">
				</div>

				<div class="form-group">
					<div class="param-label">Категории</div>

					<div class="custom-control custom-checkbox" ng-repeat="category in categories">
						<input type="checkbox" class="custom-control-input" ng-model="facility.categories[category.id]" id="checkbox@{{ category.id }}">
						<label class="custom-control-label" for="checkbox@{{ category.id }}">
							@{{ category.name }}
						</label>
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">Производительность цеха</div>
					<input type="text" class="form-control" ng-model="facility.performance" ng-change="inputFloat(facility, 'performance')" ng-class="{'is-invalid': facilityErrors.performance}">
					<small class="form-text">
						Информация о количестве замесов в день необходима для планирования производства
					</small>
				</div>

				<div class="form-group">
					<div class="param-label">Цвет цеха</div>
					<color-picker ng-model="facility.icon_color">
					</color-picker>
				</div>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>

	@include('partials.facility-status-modal')
	@include('partials.delete-modal')
</div>