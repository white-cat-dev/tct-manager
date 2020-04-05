<div class="products-block" ng-init="initEdit()">
	<h1 ng-if="productGroup.id">@{{ productGroup.name }}</h1>
	<h1 ng-if="!productGroup.id">Новый продукт</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('products') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться
			</a>
		</div>

		<div class="right-buttons">
			<a ng-href="@{{ productGroup.url }}" class="btn btn-primary" ng-if="productGroup.id">
				<i class="fas fa-eye"></i> Просмотреть
			</a>
			<button type="button" class="btn btn-primary" ng-if="productGroup.id" ng-click="delete(productGroup.id)">
				<i class="far fa-trash-alt"></i> Удалить
			</button>
		</div>
	</div>

	<div class="edit-form-block">
		<div class="form-group">
			<label>Название</label>
			<input type="text" class="form-control" ng-model="productGroupData['name']" ng-class="{'is-invalid': productGroupErrors['name']}">
			<div class="invalid-feedback" ng-if="productGroupErrors['name']">
				@{{ productGroupErrors['name'][0] }}
			</div>
		</div>

		<h2>Размеры</h2>

		<div class="row">
			<div class="col-4">
				<div class="form-group">
					<label>Длина</label>
					<input type="text" class="form-control" ng-model="productGroupData['lenght']" ng-class="{'is-invalid': productGroupErrors['lenght']}">
					<div class="invalid-feedback" ng-if="productGroupErrors['lenght']">
						@{{ productGroupErrors['lenght'][0] }}
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="form-group">
					<label>Ширина</label>
					<input type="text" class="form-control" ng-model="productGroupData['width']" ng-class="{'is-invalid': productGroupErrors['width']}">
					<div class="invalid-feedback" ng-if="productGroupErrors['width']">
						@{{ productGroupErrors['width'][0] }}
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="form-group">
					<label>Высота</label>
					<input type="text" class="form-control" ng-model="productGroupData['depth']" ng-class="{'is-invalid': productGroupErrors['depth']}">
					<div class="invalid-feedback" ng-if="productGroupErrors['depth']">
						@{{ productGroupErrors['depth'][0] }}
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label>Квадратов из замеса</label>
			<input type="text" class="form-control" ng-model="productGroupData['squares_from_batch']" ng-class="{'is-invalid': productGroupErrors['squares_from_batch']}">
			<div class="invalid-feedback" ng-if="productGroupErrors['squares_from_batch']">
				@{{ productGroupErrors['squares_from_batch'][0] }}
			</div>
		</div>

		<h2>Цвета</h2>

		<table class="table">
			<tr>
				<th>Цвет</th>
				<th>Цена</th>
				<th>Наличие</th>
			</tr>

			<tr ng-repeat="product in productGroupData['products'] track by $index">
				<td>
					<input type="text" class="form-control" ng-model="product['color']">
				</td>
				<td>
					<div class="input-group">
						<input type="text" class="form-control" ng-model="product['price']">
						<input type="text" class="form-control" ng-model="product['price_unit']">
						<input type="text" class="form-control" ng-model="product['price_pallete']">
					</div>
				</td>
				<td>
					<input type="text" class="form-control" ng-model="product['in_stock']">
				</td>
			</tr>
		</table>

		<button type="button" class="btn btn-primary" ng-click="addProduct()">
			<i class="fas fa-plus"></i> Добавить цвет	
		</button>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save('{{ route('products', [], false) }}')">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>