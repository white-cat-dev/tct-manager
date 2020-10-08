<div class="recipes-page" ng-init="initShow()">
	<h1>Просмотр рецепта</h1>

	@include('partials.loading')
	
	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('recipes') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку материалов
			</a>
		</div>

		<div class="right-buttons">
			@if (Auth::user() && Auth::user()->type == 'admin')
			<a ng-href="@{{ recipe.url + '/edit' }}" class="btn btn-primary">
				<i class="fas fa-edit"></i> Редактировать
			</a>
			<button type="button" class="btn btn-primary" ng-click="showCopy(recipe)">
				<i class="fas fa-copy"></i> Копировать
			</button>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(recipe)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
			@endif
		</div>
	</div>


	<div class="show-block" ng-show="!isLoading">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-8 col-xl-6">
				<div class="param-block">
					<div class="param-name">
						Название
					</div>
					<div class="param-value">
						@{{ recipe.name }}
					</div>
				</div>

				<div class="param-block">
					<div class="param-name">
						Стоимость рецепта
					</div>
					<div class="param-value">
						@{{ recipe.cost | number }} руб
					</div>
				</div>

				<div class="params-title">
					Состав рецепта
				</div>

				<table class="table">
					<tr>
						<th>№</th>
						<th>Материал</th>
						<th>Количество</th>
					</tr>
					<tr ng-repeat="materialGroup in recipe.material_groups">
						<td>
							@{{ $index + 1 }}
						</td>
						<td style="width: 60%;">
							@{{ materialGroup.name }}
						</td>
						<td>
							@{{ materialGroup.pivot.count | number }}
							<span ng-switch on="materialGroup.units">
								<span ng-switch-when="volume_l">л</span>
								<span ng-switch-when="volume_ml">мл</span>
								<span ng-switch-when="weight_kg">кг</span>
								<span ng-switch-when="weight_t">т</span>
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	@include('partials.delete-modal')
</div>