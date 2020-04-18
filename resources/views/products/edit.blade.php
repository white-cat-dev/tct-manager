<div class="products-page" ng-init="initEdit()">
	<h1 ng-if="!id">Создание нового продукта</h1>
	<h1 ng-if="id">Редактирование продукта</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('products') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку продуктов
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ productGroup.url }}" class="btn btn-primary" ng-if="id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="id" ng-click="delete(id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': showAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('products') }}" class="btn-link">перейти к списку продуктов</a> или <a href="{{ route('product-create') }}" class="btn-link">создать новый продукт</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<div class="row justify-content-around">
			<div class="col-5">
				<div class="params-title">
					Общая информация
				</div>

				<div class="params-block">
					<div class="form-group">
						<div class="param-label">Название продукта</div>
						<input type="text" class="form-control" ng-model="productGroup.name" ng-class="{'is-invalid': productGroupErrors.name}">
					</div>

					<div class="form-group">
						<div class="param-label">Категория</div>
						<ui-select theme="bootstrap" ng-model="productGroup.category_id" ng-class="{'is-invalid': productGroupErrors.category_id}">
				            <ui-select-match placeholder="Выберите из списка...">
					            @{{ $select.selected.name }}
					        </ui-select-match>
				            <ui-select-choices repeat="category.id as category in categories | filter: $select.search">
				                <span ng-bind-html="category.name | highlight: $select.search"></span>
				            </ui-select-choices>
						</ui-select>
					</div>
				</div>

				<div class="params-title">
					Данные для производства
				</div>

				<div class="params-block">	
					<div class="form-group">
						<div class="param-label">
							Количество из одного замеса
						</div>
						<input type="text" class="form-control" ng-model="productGroup.units_from_batch" ng-class="{'is-invalid': productGroupErrors.units_from_batch}">
					</div>

					<div class="form-group">
						<div class="param-label">Количество форм, шт.</div>
						<input type="text" class="form-control" ng-model="productGroup.forms" ng-class="{'is-invalid': productGroupErrors.forms}">
					</div>
				</div>
			</div>

			<div class="col-5">
				<div class="params-title">
					Характеристики
				</div>

				<div class="params-block">	
					<div class="form-group">
						<div class="param-label">Размеры, мм</div>
						<div class="input-group divided-input-group">
							<input type="text" class="form-control" ng-model="productGroup.length" ng-class="{'is-invalid': productGroupErrors.length}">
							<input type="text" class="form-control" ng-model="productGroup.width" ng-class="{'is-invalid': productGroupErrors.width}">
							<input type="text" class="form-control" ng-model="productGroup.depth" ng-class="{'is-invalid': productGroupErrors.depth}">
						</div>
					</div>

					<div class="form-group">
						<div class="param-label">Количество в 1 квадрате</div>
						<input type="text" class="form-control" ng-model="productGroup.unit_in_units" ng-class="{'is-invalid': productGroupErrors.unit_in_units}">
					</div>

					<div class="form-group">
						<div class="param-label">Количество в 1 поддоне</div>
						<div class="input-group divided-input-group">
							<input type="text" class="form-control" ng-model="productGroup.unit_in_pallete" ng-class="{'is-invalid': productGroupErrors.unit_in_pallete}">
							<input type="text" class="form-control" ng-model="productGroup.units_in_pallete" ng-class="{'is-invalid': productGroupErrors.units_in_pallete}">
						</div>
					</div>

					<div class="form-group">
						<div class="param-label">Вес, кг</div>
						<div class="input-group divided-input-group">
							<input type="text" class="form-control" ng-model="productGroup.weight_unit" ng-class="{'is-invalid': productGroupErrors.weight_unit}">
							<input type="text" class="form-control" ng-model="productGroup.weight_units" ng-class="{'is-invalid': productGroupErrors.weight_units}">
							<input type="text" class="form-control" ng-model="productGroup.weight_pallete" ng-class="{'is-invalid': productGroupErrors.weight_pallete}">
						</div>
					</div>
				</div>			
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-11">
				<div class="params-title">
					Разновидности по цветам
				</div>

				<table class="table table-with-buttons products-table" ng-if="productGroup.products.length > 0">
					<tr>
						<th>Цвет</th>
						<th>Цена, руб</th>
						<th>Наличие, шт</th>
						<th></th>
					</tr>

					<tr ng-repeat="product in productGroup.products track by $index">
						<td>
							<ui-select theme="bootstrap" ng-model="product.color">
					            <ui-select-match placeholder="Выберите из списка...">
						            @{{ $select.selected.name }}
						        </ui-select-match>
					            <ui-select-choices repeat="color.key as color in colors | filter: $select.search">
					                <span ng-bind-html="color.name"></span>
					            </ui-select-choices>
							</ui-select>
						</td>
						<td>
							<div class="input-group divided-input-group">
								<input type="text" class="form-control" ng-model="product.price">
								<input type="text" class="form-control" ng-model="product.price_unit">
								<input type="text" class="form-control" ng-model="product.price_pallete">
							</div>
						</td>
						<td>
							<input type="text" class="form-control" ng-model="product.in_stock">
						</td>
						<td>
							<button type="button" class="btn btn-primary" ng-click="deleteProduct($index)">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>
				</table>

				<button type="button" class="btn btn-primary" ng-click="addProduct()">
					<i class="fas fa-plus"></i> Добавить разновидность	
				</button>
			</div>
		</div>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>