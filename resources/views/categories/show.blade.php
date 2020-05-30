<div class="categories-block" ng-init="initShow()">
	<h1>Просмотр категории</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('categories') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку категорий
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ category.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(category)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-8 col-xl-66">
				<div class="show-block-title">
					Категория «@{{ category.name }}»
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
						Главная категория
					</div>
					<div class="param-value">
						<span ng-switch on="category.main_category">
							<span ng-switch-when="tiles">Плитка</span>
							<span ng-switch-when="blocks">Блоки</span>
						</span>
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
						Род прилагательных
					</div>
					<span ng-switch on="category.adjectives">
						<span ng-switch-when="feminine">Женский</span>
						<span ng-switch-when="masculine">Мужской</span>
						<span ng-switch-when="neuter">Средний</span>
					</span>
				</div>

				<div class="param-block">
					<div class="param-name">
						Разновидности
					</div>
					<div class="param-value">
						<span ng-switch on="category.variations">
							<span ng-switch-when="colors">У товаров категории есть разновидности по цветам</span>
							<span ng-switch-when="grades">У товаров категории есть разновидности по марке бетона</span>
							<span ng-switch-when="">У товаров категории нет разновидностей</span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('partials.delete-modal')
</div>