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
			<div class="col-6 col-xl-5">
				<div class="params-title">
					Общая информация
				</div>

				<div class="form-group">
					<div class="param-label">Полное название продукта</div>
					<input type="text" class="form-control" ng-model="productGroup.wp_name" ng-class="{'is-invalid': productGroupErrors.wp_name}">
					<small class="form-text">
						Введите название, которое используется в WordPress
					</small>
				</div>

				<div class="form-group">
					<div class="param-label">Короткое название продукта</div>
					<input type="text" class="form-control" ng-model="productGroup.name" ng-class="{'is-invalid': productGroupErrors.name}">
					<small class="form-text">
						Введите название, которое будет использоваться в панели
					</small>
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

				<div class="alert alert-warning" ng-show="!productCategory">
					Выберите категорию продукта, чтобы продолжить заполнение формы
				</div>
			</div>

			<div class="col-6 col-xl-5" ng-show="productCategory">
				<div class="params-title">
					Характеристики
				</div>

				<div class="form-group" ng-init="productGroup.size_params = productGroup.size_params ? productGroup.size_params : 'lwh'">
					<div class="param-label">Размеры, мм</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioLwh" ng-model="productGroup.size_params" value="lwh">
						<label class="custom-control-label" for="radioLwh">Д×Ш×В</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioLhw" ng-model="productGroup.size_params" value="lhw">
						<label class="custom-control-label" for="radioLhw">Д×В×Ш</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioLh" ng-model="productGroup.size_params" value="lh">
						<label class="custom-control-label" for="radioLh">Д×В</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input class="custom-control-input" type="radio" id="radioWhl" ng-model="productGroup.size_params" value="whl">
						<label class="custom-control-label" for="radioWhl">Ш×В×Д</label>
					</div>

					<div class="input-group divided-input-group">
						<input type="text" class="form-control" ng-model="productGroup.length" ng-class="{'is-invalid': productGroupErrors.length}">
						<span>×</span>
						<input type="text" class="form-control" ng-model="productGroup.width" ng-class="{'is-invalid': productGroupErrors.width}" ng-if="productGroup.size_params != 'lh'">
						<span ng-if="productGroup.size_params != 'lh'">×</span>
						<input type="text" class="form-control" ng-model="productGroup.height" ng-class="{'is-invalid': productGroupErrors.height}">
					</div>
				</div>

				<div class="form-group" ng-show="productCategory && productCategory.units != 'unit'">
					<div class="param-label">
						Количество
						<span ng-switch on="productCategory.units">
							<span ng-switch-when="area">в м<sup>2</sup>, шт</span>
							<span ng-switch-when="volume">в м<sup>3</sup>, шт</span>
						</span>	
					</div>
					<input type="text" class="form-control" ng-model="productGroup.unit_in_units" ng-class="{'is-invalid': productGroupErrors.unit_in_units}">
				</div>

				<div class="form-group">
					<div class="param-label">
						Количество на поддоне,
						<span ng-switch on="productCategory.units">
							<span ng-switch-when="area">шт / м<sup>2</sup></span>
							<span ng-switch-when="volume">шт / м<sup>3</sup></span>
							<span ng-switch-when="unit">шт</span>
						</span>	
					</div>

					<div class="input-group divided-input-group">
						<input type="text" class="form-control" ng-model="productGroup.unit_in_pallete" ng-class="{'is-invalid': productGroupErrors.unit_in_pallete}" ng-if="productCategory.units != 'unit'">
						<span ng-if="productCategory.units != 'unit'">/</span>
						<input type="text" class="form-control" ng-model="productGroup.units_in_pallete" ng-class="{'is-invalid': productGroupErrors.units_in_pallete}">
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">
						Вес шт / поддона, кг</span>
					</div>
					<div class="input-group divided-input-group">
						<input type="text" class="form-control" ng-model="productGroup.weight_unit" ng-class="{'is-invalid': productGroupErrors.weight_unit}">
						<span>/</span>
						<input type="text" class="form-control" ng-model="productGroup.weight_pallete" ng-class="{'is-invalid': productGroupErrors.weight_pallete}">
					</div>
				</div>
			</div>		
		</div>

		<div class="params-section" ng-show="productCategory">
			<div class="row justify-content-around">
				<div class="col-12 col-xl-11">
					<div class="params-title">
						<span ng-switch on="productCategory.variations">
							Разновидности и цены
						</span>
					</div>
				</div>

				<div class="col-12 col-xl-11" ng-show="productCategory.variations">
					<table class="table table-with-buttons" ng-if="productGroup.products.length > 0">
						<tr>
							<th>
								<span ng-switch on="productCategory.variations">
									<span ng-switch-when="colors">Цвет</span>
									<span ng-switch-when="grades">Марка</span>
								</span>
							</th>
							<th>Цена (наличный / безнал / НДС), руб</th>
							<th>Наличие, 
								<span ng-switch on="productCategory.units">
									<span ng-switch-when="area">м<sup>2</sup></span>
									<span ng-switch-when="volume">м<sup>3</sup></span>
									<span ng-switch-when="unit">шт</span>
								</span>	
							</th>
							<th></th>
						</tr>

						<tr ng-repeat="product in productGroup.products track by $index">
							<td style="width: 22%;">
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
									<span style="width: 25px;" ng-switch on="productCategory.units">
										<span ng-switch-when="area">м<sup>2</sup></span>
										<span ng-switch-when="volume">м<sup>3</sup></span>
										<span ng-switch-when="unit">шт</span>
									</span>	
									<input type="text" class="form-control" ng-model="product.price" ng-change="changePrice(product, 'price'); inputFloat(product, 'price')">
									<span>/</span>
									<input type="text" class="form-control" ng-model="product.price_cashless" ng-change="changePrice(product, 'price_cashless'); inputFloat(product, 'price_cashless')">
									<span>/</span>
									<input type="text" class="form-control" ng-model="product.price_vat" ng-change="changePrice(product, 'price_vat'); inputFloat(product, 'price_vat')">
								</div>

								<div class="input-group divided-input-group" ng-if="productCategory.units != 'unit'">
									<span style="width: 25px;">шт</span>
									<input type="text" class="form-control" ng-model="product.price_unit" ng-change="changePrice(product, 'price_unit'); inputFloat(product, 'price_unit')">
									<span>/</span>
									<input type="text" class="form-control" ng-model="product.price_unit_cashless" ng-change="changePrice(product, 'price_unit_cashless'); inputFloat(product, 'price_unit_cashless')">
									<span>/</span>
									<input type="text" class="form-control" ng-model="product.price_unit_vat" ng-change="changePrice(product, 'price_unit_vat'); inputFloat(product, 'price_unit_vat')">
								</div>
							</td>
							<td style="width: 22%;">
								<input type="text" class="form-control" ng-model="product.in_stock">
							</td>
							<td>
								<button type="button" class="btn btn-primary" ng-click="deleteProduct($index)" ng-change="inputFloat(product, 'in_stock')">
									<i class="far fa-trash-alt"></i>
								</button>
							</td>
						</tr>
					</table>

					<div class="form-group">
						<button type="button" class="btn btn-primary" ng-click="addProduct()">
							<i class="fas fa-plus"></i> Добавить разновидность	
						</button>
					</div>
				</div>

				<div class="col-8 col-xl-6" ng-show="!productCategory.variations">
					<div class="form-group">
						<div class="param-label">Цена (наличный / безнал / НДС), руб</div>
						<div class="input-group divided-input-group">
							<span style="width: 25px;" ng-switch on="productCategory.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт</span>
							</span>	
							<input type="text" class="form-control" ng-model="productGroup.products[0].price" ng-change="inputFloat(productGroup.products[0], 'price')">
							<span>/</span>
							<input type="text" class="form-control" ng-model="productGroup.products[0].price_cashless" ng-change="inputFloat(productGroup.products[0], 'price_cashless')">
							<span>/</span>
							<input type="text" class="form-control" ng-model="productGroup.products[0].price_vat" ng-change="inputFloat(productGroup.products[0], 'price_vat')">
						</div>

						<div class="input-group divided-input-group" ng-if="productCategory.units != 'unit'">
							<span style="width: 25px;">шт</span>
							<input type="text" class="form-control" ng-model="productGroup.products[0].price_unit" ng-change="inputFloat(productGroup.products[0], 'price_unit')">
							<span>/</span>
							<input type="text" class="form-control" ng-model="productGroup.products[0].price_unit_cashless" ng-change="inputFloat(productGroup.products[0], 'price_unit_cashless')">
							<span>/</span>
							<input type="text" class="form-control" ng-model="productGroup.products[0].price_unit_vat" ng-change="inputFloat(productGroup.products[0], 'price_unit_vat')">
						</div>
					</div>
				</div>

				<div class="col-4 col-xl-4" ng-show="!productCategory.variations">
					<div class="form-group">
						<div class="param-label">Наличие</div>
						<input type="text" class="form-control" ng-model="productGroup.products[0].in_stock" ng-change="inputFloat(productGroup.products[0], 'in_stock')">
					</div>
				</div>
			</div>
		</div>

		<div class="params-section" ng-show="productCategory">
			<div class="row justify-content-around">
				<div class="col-12 col-xl-11">
					<div class="params-title">
						Парный элемент
					</div>

					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="checkboxSetPair" ng-checked="isSetPairShown" ng-click="showSetPair()">
							<label class="custom-control-label" for="checkboxSetPair">Есть парный элемент</label>
						</div>
					</div>
				</div>

				<div class="col-6 col-xl-5" ng-if="isSetPairShown">
					<div class="form-group">
						<div class="param-label">Название</div>

						<ui-select ng-model="productGroup.set_pair_id" skip-focusser="true">
				            <ui-select-match placeholder="Выберите из списка...">
					            @{{ $select.selected.name }} @{{ $select.selected.size }}
					        </ui-select-match>
				            <ui-select-choices repeat="productGroup.id as productGroup in productGroups | filter: $select.search">
				                <span ng-bind-html="productGroup.name + ' ' + productGroup.size"></span>
				            </ui-select-choices>
						</ui-select>
					</div>
				</div>
				
				<div class="col-6 col-xl-5" ng-show="isSetPairShown">
					<div class="form-group">
						<div class="param-label">Соотношение</div>
						<div class="input-group divided-input-group">
							<input type="text" class="form-control" ng-model="productGroup.set_pair_ratio">
							<span>:</span>
							<input type="text" class="form-control" ng-model="productGroup.set_pair_ratio_to">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="params-section" ng-show="productCategory">
			<div class="row justify-content-around">
				<div class="col-12 col-xl-11">
					<div class="params-title">
						Данные для производства
					</div>
				</div>

				<div class="col-6 col-xl-5">	
					<div class="form-group">
						<div class="param-label">
							Количество из одного замеса,
							<span ng-switch on="productCategory.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт</span>
							</span>	
						</div>
						<input type="text" class="form-control" ng-model="productGroup.units_from_batch" ng-class="{'is-invalid': productGroupErrors.units_from_batch}">
					</div>

					<div class="form-group">
						<div class="param-label">
							<span ng-if="productCategory.variations != 'colors'">Количество форм,</span>
							<span ng-if="productCategory.variations == 'colors'">Количество серых / красных форм,</span>
							<span ng-switch on="productCategory.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт</span>
							</span>	
						</div>
						<div class="input-group divided-input-group">
							<input type="text" class="form-control" ng-model="productGroup.forms" ng-class="{'is-invalid': productGroupErrors.forms}">
							<span ng-if="productCategory.variations == 'colors'">/</span>
							<input type="text" class="form-control" ng-model="productGroup.forms_add" ng-class="{'is-invalid': productGroupErrors.forms_add}" ng-if="productCategory.variations == 'colors'">
						</div>
					</div>
				</div>

				<div class="col-6 col-xl-5">
					<div class="form-group">
						<div class="param-label">
							Стоимость работы 
							<span ng-switch on="productCategory.units">
								<span ng-switch-when="area">за м<sup>2</sup>, руб</span>
								<span ng-switch-when="volume">за м<sup>3</sup>, руб</span>
								<span ng-switch-when="unit">за шт, руб</span>
							</span>	
						</div>
						<input type="text" class="form-control" ng-model="productGroup.salary_units" ng-class="{'is-invalid': productGroupErrors.salary_units}">
					</div>

					<div class="form-group">
						<div class="param-label">Рецепт</div>
						<ui-select ng-model="productGroup.recipe_id" ng-class="{'is-invalid': productGroupErrors.recipe_id}" skip-focusser="true">
				            <ui-select-match placeholder="Выберите из списка...">
					            @{{ $select.selected.name }}
					        </ui-select-match>
				            <ui-select-choices repeat="recipe.id as recipe in recipes | filter: {'name': $select.search}">
				                <span ng-bind-html="recipe.name | highlight: $select.search"></span>
				            </ui-select-choices>
						</ui-select>
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