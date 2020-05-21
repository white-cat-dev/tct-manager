<div class="categories-block" ng-init="initEdit()">
	<h1 ng-if="!id">Создание новой категори</h1>
	<h1 ng-if="id">Редактирование категории</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('categories') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку категорий
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ category.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-8">
				<div class="form-group">
					<div class="param-label">Название</div>
					<input type="text" class="form-control" ng-model="category.name" ng-class="{'is-invalid': categoryErrors.name}">
				</div>

				<div class="form-group">
					<div class="param-label">Главная категория</div>

					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioTiles" ng-model="category.main_category" value="tiles">
						<label class="custom-control-label" for="radioTiles">Плитка</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioBlocks" ng-model="category.main_category" value="blocks">
						<label class="custom-control-label" for="radioBlocks">Блоки</label>
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">Единицы измерения</div>
					<ui-select theme="bootstrap" ng-model="category.units" ng-class="{'is-invalid': categoryErrors.units}">
			            <ui-select-match placeholder="Выберите из списка...">
				            <span ng-bind-html="$select.selected.name"></span>
				        </ui-select-match>
			            <ui-select-choices repeat="unit.key as unit in units">
			                <span ng-bind-html="unit.name"></span>
			            </ui-select-choices>
					</ui-select>
				</div>

				<div class="form-group">
					<div class="param-label">Род прилагательных</div>

					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioFeminine" ng-model="category.adjectives" value="feminine">
						<label class="custom-control-label" for="radioFeminine">Женский</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioMasculine" ng-model="category.adjectives" value="masculine">
						<label class="custom-control-label" for="radioMasculine">Мужской</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioNeuter" ng-model="category.adjectives" value="neuter">
						<label class="custom-control-label" for="radioNeuter">Средний</label>
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">Разновидности</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioColors" ng-model="category.variations" value="colors">
						<label class="custom-control-label" for="radioColors">
							У товаров категории есть разновидности по цветам
						</label>
					</div>
					{{-- <div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioGrades" ng-model="category.variations" value="grades">
						<label class="custom-control-label" for="radioGrades">
							У товаров категории есть разновидности по марке бетона
						</label>
					</div> --}}
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioNone" ng-model="category.variations" value="">
						<label class="custom-control-label" for="radioNone">
							У товаров категории нет разновидностей
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>