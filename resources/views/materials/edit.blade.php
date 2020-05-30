<div class="materials-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового материала</h1>
	<h1 ng-if="id">Редактирование материала</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('materials') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку материалов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ materialGroup.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="showDelete(materialGroup)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': showAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('materials') }}" class="btn-link">перейти к списку материалов</a> или <a href="{{ route('material-create') }}" class="btn-link">создать новый материал</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-12 col-lg-8 col-xl-6">
				<div class="form-group">
					<div class="param-label">Название</div>
					<input type="text" class="form-control" ng-model="materialGroup.name" ng-class="{'is-invalid': materialGroupErrors.name}">
				</div>

				<div class="form-group">
					<div class="param-label">Единицы измерения</div>
					<ui-select theme="bootstrap" ng-model="materialGroup.units" ng-class="{'is-invalid': materialGroupErrors.units}">
			            <ui-select-match placeholder="Выберите из списка...">
				            <span ng-bind-html="$select.selected.name"></span>
				        </ui-select-match>
			            <ui-select-choices repeat="unit.key as unit in units">
			                <span ng-bind-html="unit.name"></span>
			            </ui-select-choices>
					</ui-select>
				</div>

				<div class="form-group">
					<div class="param-label">Разновидности</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioColors" ng-model="materialGroup.variations" value="colors">
						<label class="custom-control-label" for="radioColors">
							Есть разновидности по цветам
						</label>
					</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioNone" ng-model="materialGroup.variations" value="">
						<label class="custom-control-label" for="radioNone">
							Нет разновидностей
						</label>
					</div>
				</div>

				<div ng-if="materialGroup.variations">
					<table class="table materials-table table-with-buttons" ng-if="materialGroup.materials.length > 0">
						<tr>
							<th>Разновидность</th>
							<th>
								<span ng-switch on="materialGroup.units">
									<span ng-switch-when="volume_l">Цена за 1 л, руб.</span>
									<span ng-switch-when="volume_ml">Цена за 1 мл, руб.</span>
									<span ng-switch-when="weight_kg">Цена за 1 кг, руб.</span>
									<span ng-switch-when="weight_t">Цена за 1 т, руб.</span>
									<span ng-switch-default>Цена, руб.</span>
								</span>
							</th>
							<th>
								<span ng-switch on="materialGroup.units">
									<span ng-switch-when="volume_l">В наличии, л</span>
									<span ng-switch-when="volume_ml">В наличии, мл</span>
									<span ng-switch-when="weight_kg">В наличии, кг</span>
									<span ng-switch-when="weight_t">В наличии, т</span>
									<span ng-switch-default>В наличии</span>
								</span>
							</th>
							<th></th>
						</tr>

						<tr ng-repeat="material in materialGroup.materials track by $index">
							<td>
								<ui-select ng-model="material.variation" skip-focusser="true">
						            <ui-select-match placeholder="Выберите...">
							            @{{ $select.selected.name }}
							        </ui-select-match>
						            <ui-select-choices repeat="color.key as color in colors | filter: $select.search">
						                <span ng-bind-html="color.name"></span>
						            </ui-select-choices>
								</ui-select>
							</td>
							<td>
								<input type="text" class="form-control" ng-model="material.price">
							</td>
							<td>
								<input type="text" class="form-control" ng-model="material.in_stock">
							</td>
							<td>
								<button type="button" class="btn btn-primary" ng-click="deleteMaterial($index)">
									<i class="far fa-trash-alt"></i>
								</button>
							</td>
						</tr>
					</table>

					<button type="button" class="btn btn-primary" ng-click="addMaterial()">
						<i class="fas fa-plus"></i> Добавить разновидность	
					</button>
				</div>

				<div ng-if="!materialGroup.variations">
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								<div class="param-label">
									<span ng-switch on="materialGroup.units">
										<span ng-switch-when="volume_l">Цена за 1 л, руб.</span>
										<span ng-switch-when="volume_ml">Цена за 1 мл, руб.</span>
										<span ng-switch-when="weight_kg">Цена за 1 кг, руб.</span>
										<span ng-switch-when="weight_t">Цена за 1 т, руб.</span>
										<span ng-switch-default>Цена, руб.</span>
									</span>
								</div>
								<input type="text" class="form-control" ng-model="materialGroup.materials[0].price">
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<div class="param-label">
									<span ng-switch on="materialGroup.units">
										<span ng-switch-when="volume_l">В наличии, л</span>
										<span ng-switch-when="volume_ml">В наличии, мл</span>
										<span ng-switch-when="weight_kg">В наличии, кг</span>
										<span ng-switch-when="weight_t">В наличии, т</span>
										<span ng-switch-default>В наличии</span>
									</span>
								</div>
								<input type="text" class="form-control" ng-model="materialGroup.materials[0].in_stock">
							</div>
						</div>
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

	@include('partials.delete-modal')
</div>