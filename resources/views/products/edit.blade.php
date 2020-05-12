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
		<div class="params-section">
			<div class="row justify-content-around">
				<div class="col-5">
					<div class="params-title">
						Общая информация
					</div>

					<div class="params-block">
						<div class="form-group">
							<div class="param-label">Полное название продукта</div>
							<input type="text" class="form-control" ng-model="productGroup.wp_name" ng-class="{'is-invalid': productGroupErrors.wp_name}">
						</div>

						<div class="form-group">
							<div class="param-label">Короткое название продукта</div>
							<input type="text" class="form-control" ng-model="productGroup.name" ng-class="{'is-invalid': productGroupErrors.name}">
						</div>

						<div class="form-group">
							<div class="param-label">Категория</div>
							<ui-select ng-model="productGroup.category_id" ng-class="{'is-invalid': productGroupErrors.category_id}" ng-change="chooseProductCategory()" skip-focusser="true">
					            <ui-select-match placeholder="Выберите из списка...">
						            @{{ $select.selected.name }}
						        </ui-select-match>
					            <ui-select-choices repeat="category.id as category in categories | filter: $select.search">
					                <span ng-bind-html="category.name | highlight: $select.search"></span>
					            </ui-select-choices>
							</ui-select>
						</div>

						<div class="form-group">
							<div class="param-label">Род прилагательных</div>

							<div class="custom-control custom-radio custom-control-inline">
								<input class="custom-control-input" type="radio" ng-model="productGroup.adjectives" id="radioFeminine" value="feminine">
								<label class="custom-control-label" for="radioFeminine">Женский</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input class="custom-control-input" type="radio" ng-model="productGroup.adjectives" id="radioMasculine" value="masculine">
								<label class="custom-control-label" for="radioMasculine">Мужской</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input class="custom-control-input" type="radio" ng-model="productGroup.adjectives" id="radioNeuter" value="neuter">
								<label class="custom-control-label" for="radioNeuter">Средний</label>
							</div>
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
		</div>

		<div class="params-section">
			<div class="row justify-content-around" ng-if="productCategory && productCategory.variations">
				<div class="col-11">
					<div class="params-title">
						<span ng-switch on="productCategory.variations">
							<span ng-switch-when="colors">Разновидности по цветам</span>
							<span ng-switch-when="grades">Разновидности по марке бетона</span>
						</span>
					</div>

					<table class="table table-with-buttons products-table" ng-if="productGroup.products.length > 0">
						<tr>
							<th>
								<span ng-switch on="productCategory.variations">
									<span ng-switch-when="colors">Цвет</span>
									<span ng-switch-when="grades">Марка</span>
								</span>
							</th>
							<th>Цена, руб</th>
							<th>Наличие, шт</th>
							<th></th>
						</tr>

						<tr ng-repeat="product in productGroup.products track by $index">
							<td>
								<ui-select ng-model="product.variation" ng-change="chooseProductVariation(product, $select.selected)" ng-if="productCategory.variations == 'colors'" skip-focusser="true">
						            <ui-select-match placeholder="Выберите из списка...">
							            @{{ $select.selected.name }}
							        </ui-select-match>
						            <ui-select-choices repeat="color.key as color in colors | filter: $select.search">
						                <span ng-bind-html="color.name"></span>
						            </ui-select-choices>
								</ui-select>

								<ui-select ng-model="product.variation" ng-change="chooseProductVariation(product, $select.selected)" ng-if="productCategory.variations == 'grades'" skip-focusser="true">
						            <ui-select-match placeholder="Выберите из списка...">
							            @{{ $select.selected.name }}
							        </ui-select-match>
						            <ui-select-choices repeat="grade.key as grade in grades | filter: $select.search">
						                <span ng-bind-html="grade.name"></span>
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

			<div class="row justify-content-around" ng-if="productCategory && !productCategory.variations">
				<div class="col-5">
					<div class="param-label">Цена, руб</div>
					<div class="input-group divided-input-group">
						<input type="text" class="form-control" ng-model="productGroup.products[0].price">
						<input type="text" class="form-control" ng-model="productGroup.products[0].price_unit">
						<input type="text" class="form-control" ng-model="productGroup.products[0].price_pallete">
					</div>
				</div>

				<div class="col-5">
					<div class="param-label">Наличие</div>
					<input type="text" class="form-control" ng-model="productGroup.products[0].in_stock">
				</div>
			</div>
		</div>

		<div class="row justify-content-around">
			<div class="col-5">
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
					Данные для расчета зарплаты
				</div>

				<div class="params-block">	
					<div class="form-group">
						<div class="param-label">
							Стоимость работы 
							<span ng-if="productCategory" ng-switch on="productCategory.units">
								<span ng-switch-when="area">за 1 м<sup>2</sup></span>
								<span ng-switch-when="volume">за 1 м<sup>3</sup></span>
								<span ng-switch-when="unit">за 1 шт.</span>
							</span>	
						</div>
						<input type="text" class="form-control" ng-model="productGroup.salary_units" ng-class="{'is-invalid': productGroupErrors.salary_units}">
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