<div class="recipes-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового рецепта</h1>
	<h1 ng-if="id">Редактирование рецепта</h1>

	@include('partials.loading')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('recipes') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку рецептов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ recipe.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(recipe)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block" ng-show="!isLoading">
		<div class="row justify-content-around">
			<div class="col-12 col-lg-8 col-xl-6">
				<div class="form-group">
					<div class="param-label">Название</div>
					<input type="text" class="form-control" ng-model="recipe.name" ng-class="{'is-invalid': recipeErrors.name}">
				</div>

				<div class="params-title">
					Состав рецепта
				</div>

				<div ng-if="recipe.material_groups">
					<table class="table table-with-buttons" ng-if="recipe.material_groups.length > 0">
						<tr>
							<th>№</th>
							<th>Материал</th>
							<th>Количество</th>
							<th></th>
						</tr>

						<tr ng-repeat="materialGroup in recipe.material_groups track by $index">
							<td>
								@{{ $index + 1 }}
							</td>
							<td style="width: 60%;">
								<ui-select ng-model="materialGroup.id" skip-focusser="true">
						            <ui-select-match placeholder="Выберите...">
							            @{{ $select.selected.name }}
							        </ui-select-match>
						            <ui-select-choices repeat="materialGroup.id as materialGroup in materialGroups | filter: $select.search">
						                <span ng-bind-html="materialGroup.name"></span>
						            </ui-select-choices>
								</ui-select>
							</td>
							<td>
								<input type="text" class="form-control" ng-model="materialGroup.pivot.count" ng-change="inputFloat(materialGroup.pivot, 'count')">
							</td>
							<td>
								<button type="button" class="btn btn-primary" ng-click="deleteMaterialGroup($index)">
									<i class="far fa-trash-alt"></i>
								</button>
							</td>
						</tr>
					</table>

					<button type="button" class="btn btn-primary" ng-click="addMaterialGroup()">
						<i class="fas fa-plus"></i> Добавить материал
					</button>
				</div>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()" ng-disabled="isSaving">
				<span ng-if="isSaving">
					<i class="fa fa-spinner fa-spin"></i> Сохранение...
				</span>
				<span ng-if="!isSaving">
					<i class="fas fa-save"></i> Сохранить
				</span>
			</button>
		</div>
	</div>

	@include('partials.delete-modal')
</div>