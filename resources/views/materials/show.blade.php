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
			<a ng-href="@{{ materialGroup.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(materialGroup)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block">
		<div class="row justify-content-center">
			<div class="col-6">
				<div class="show-block-title">
					Материал "@{{ materialGroup.name }}"
				</div>

				<div class="param-block">
					<div class="param-name">
						Название
					</div>
					<div class="param-value">
						@{{ materialGroup.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Единицы измерения
					</div>
					<div class="param-value" ng-repeat="unit in units" ng-if="unit.key == materialGroup.units">
						<span ng-bind-html="unit.name"></span>
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Цена
					</div>
					<div class="param-value">
						@{{ materialGroup.price }} руб.
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						В наличии
					</div>
					<div class="param-value">
						@{{ materialGroup.in_stock }} 
						<span ng-switch on="materialGroup.units">
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

	@include('partials.delete-modal')
</div>