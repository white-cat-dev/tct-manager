<div class="materials-page" ng-init="init()">
	<h1>Материалы</h1>

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
					@{{ material.price }} руб.
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
					<button type="button" class="btn btn-sm btn-primary" ng-click="showDelete(materialGroup)">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif
				</div>
			</td>
		</tr>
	</table>

	<div class="no-data-block" ng-if="(materialGroups | filter: {'name': searchQuery}).length == 0">
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


	<div class="modal supply-modal" ng-show="isSupplyModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">
						Поступление материалов
					</div>
					<button type="button" class="close" ng-click="hideSupplyModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="param-label">Дата</div>
						<input type="text" class="form-control" ng-model="modalSupply.date_raw" ui-mask="99.99.9999">
					</div>

					<table class="table table-with-buttons" ng-if="modalSupply.supplies.length > 0">
						<tr>
							<th>Материал</th>
							<th>Количество</th>
							<th></th>
						</tr>

						<tr ng-repeat="supply in modalSupply.supplies" ng-if="!realization.date">
							<td style="width: 55%;">
								<ui-select ng-model="supply.material_id" skip-focusser="true">
						            <ui-select-match placeholder="Выберите материал...">
							            @{{ $select.selected.material_group.name }} @{{ $select.selected.variation_text }}
							        </ui-select-match>
						            <ui-select-choices repeat="material.id as material in materials | filter: $select.search">
						                <span ng-bind-html="material.material_group.name + ' ' + material.variation_text | highlight: $select.search"></span>
						            </ui-select-choices>
								</ui-select>
							</td>
							<td>
								<input type="text" class="form-control form-control-sm" ng-model="supply.performed">
							</td>
							<td>
								<button type="button" class="btn btn-sm btn-primary" ng-click="deleteSupplyMaterial($index)">
									<i class="far fa-trash-alt"></i>
								</button>
							</td>
						</tr>
					</table>

					<button type="button" class="btn btn-sm btn-primary" ng-click="addSupplyMaterial()">
						<i class="fas fa-plus"></i> Добавить материал	
					</button>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="saveSupply()">
						<i class="fas fa-save"></i> Сохранить
					</button>
				</div>
			</div>
		</div>
	</div>

	@include('partials.delete-modal')
</div>