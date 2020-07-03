<div class="materials-page" ng-init="init()">
	<h1>Материалы</h1>

	@include('partials.loading')

	<div class="top-buttons-block">
		<div class="left-buttons">
			<div class="input-group search-group">
				<input type="text" class="form-control" placeholder="Введите запрос для поиска..." ng-model="tempSearchQuery" ng-keypress="searchInputKeyPressed($event)">
				<div class="input-group-append">
			    	<button class="btn btn-primary" type="button" ng-click="searchQuery = tempSearchQuery">
			    		<i class="fas fa-search"></i> Поиск
			    	</button>
			 	</div>
			</div>
		</div>

		<div class="right-buttons">
			<button type="button" class="btn btn-primary" ng-click="showSupplyModal()">
				<i class="fas fa-truck"></i> Поступление
			</button>

			<button type="button" class="btn btn-primary" ng-click="loadExportFile()">
				<i class="fas fa-file-excel"></i> Скачать
			</button>

			@if (Auth::user() && Auth::user()->type == 'admin')
			<a href="{{ route('material-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать материал
			</a>
			@endif
		</div>
	</div>

	<table class="table main-table table-with-buttons" ng-if="(materialGroups | filter: {'name': searchQuery}).length > 0">
		<tr>
			<th>№</th>
			<th>Название</th>
			<th>Виды</th>
			<th>Цена</th>
			<th>В наличии</th>
			<th></th>
		</tr>

		<tr ng-repeat="(materialGroupNum, materialGroup) in materialGroups | filter: {'name': searchQuery}">
			<td>
				@{{ $index + 1 }}
			</td>
			<td>
				@{{ materialGroup.name }}
			</td>
			<td>
				<div ng-repeat="material in materialGroup.materials">
					<span ng-if="material.variation_text">@{{ material.variation_text }}</span>
					<span ng-if="!material.variation_text">—</span>
				</div>
			</td>
			<td>
				<div ng-repeat="material in materialGroup.materials">
					@{{ material.price }} 
					<span ng-switch on="materialGroup.units">
						<span ng-switch-when="volume_l">руб/л</span>
						<span ng-switch-when="volume_ml">руб/мл</span>
						<span ng-switch-when="weight_kg">руб/кг</span>
						<span ng-switch-when="weight_t">руб/т</span>
						<span ng-switch-default>руб</span>
					</span>
				</div>
			</td>
			<td>
				<div ng-repeat="(materialNum, material) in materialGroup.materials">
					<div class="edit-field" ng-init="material.new_in_stock = material.in_stock">
						<input type="text" class="form-control" ng-model="material.new_in_stock" ng-blur="saveEditField(materialGroupNum, materialNum, 'in_stock')" ng-keypress="inputKeyPressed($event)">
						<span class="units">
							@{{ material.new_in_stock }}
							<span ng-switch on="materialGroup.units">
								<span ng-switch-when="volume_l">л</span>
								<span ng-switch-when="volume_ml">мл</span>
								<span ng-switch-when="weight_kg">кг</span>
								<span ng-switch-when="weight_t">т</span>
							</span>
						</span>
					</div>
				</div>
			</td>
			<td>
				<div class="btn-group">
					<a ng-href="@{{ materialGroup.url }}" class="btn btn-sm btn-primary">
						<i class="fas fa-eye"></i>
					</a>
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a ng-href="@{{ materialGroup.url + '/edit' }}" class="btn btn-sm btn-primary">
						<i class="fas fa-edit"></i>
					</a>
					@endif
				</div>
			</td>
		</tr>
	</table>

	<div class="no-data-block" ng-if="(materialGroups | filter: {'name': searchQuery}).length == 0 && !isLoading">
		<div class="icon">
			<i class="fas fa-th"></i>
		</div>
		Не найдено ни одного материала <br>
		<small ng-if="searchQuery"> по запросу "@{{ searchQuery }}"</small>

		@if (Auth::user() && Auth::user()->type == 'admin')
		<div>
			<a href="{{ route('material-create') }}" class="btn btn-primary">
				<i class="fas fa-plus"></i> Создать новый материал
			</a>
		</div>
		@endif
	</div>

	@include('partials.material-supply-modal')
	@include('partials.delete-modal')
</div>