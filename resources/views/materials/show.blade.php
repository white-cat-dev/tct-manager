<div class="materials-page" ng-init="initShow()">
	<h1>Просмотр материала</h1>
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('materials') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку материалов
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ material.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-center">
			<div class="col-6">
				<div class="show-block-title">
					Материал "@{{ material.name }}"
				</div>

				<div class="param-block">
					<div class="param-name">
						Название
					</div>
					<div class="param-value">
						@{{ material.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Единицы измерения
					</div>
					<div class="param-value" ng-repeat="unit in units" ng-if="unit.key == material.units">
						<span ng-bind-html="unit.name"></span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Цена
					</div>
					<div class="param-value">
						@{{ material.price }} руб.
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						В наличии
					</div>
					<div class="param-value">
						@{{ material.in_stock }} 
						<span ng-switch on="material.units">
							<span ng-switch-when="volume_l">л</span>
							<span ng-switch-when="volume_ml">мл</span>
							<span ng-switch-when="weight_kg">кг</span>
							<span ng-switch-when="weight_t">т</span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>