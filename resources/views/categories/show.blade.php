<div class="categories-block" ng-init="initShow()">
	<h1>Просмотр категории</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('categories') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку категорий
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ category.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="category.id" ng-click="delete(category.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-center">
			<div class="col-6">
				<div class="show-block-title">
					Категория "@{{ category.name }}"
				</div>

				<div class="param-block">
					<div class="param-name">
						Название
					</div>
					<div class="param-value">
						@{{ category.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Единицы измерения
					</div>
					<div class="param-value" ng-repeat="unit in units" ng-if="unit.key == category.units">
						<span ng-bind-html="unit.name"></span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Разновидности
					</div>
					<div class="param-value">
						<span ng-if="category.has_colors">
							У товаров категории есть разновидности по цветам
						</span>
						<span ng-if="!category.has_colors">
							У товаров категории нет разновидностей по цветам
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>