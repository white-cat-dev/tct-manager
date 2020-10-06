<div class="modal product-stock-modal" ng-show="isProductStockModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Корректировка остатков
				</div>
				<button type="button" class="close" ng-click="hideProductStockModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="product-name">
					@{{ modalProduct.product_group.name }} @{{ modalProduct.product_group.size }}
					<span class="product-color">
						@{{ modalProduct.variation_text }}
					</span>
				</div>

				<table class="table">
					<tr>
						<th>В наличии</th>
						<th>Новое значение</th>
					</tr>

					<tr>
						<td>
							@{{ modalProduct.old_in_stock }} <span ng-bind-html="modalProduct.units_text"></span>
						</td>
						<td>
							<div class="form-group-units">
								<input type="text" class="form-control" ng-model="modalProduct.in_stock" ng-change="inputFloat(modalProduct, 'in_stock')"> 
								<div class="units">
									@{{ modalProduct.in_stock }} <span ng-bind-html="modalProduct.units_text"></span>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveProductStock()" ng-disabled="isModalSaving">
					<span ng-if="isModalSaving">
						<i class="fa fa-spinner fa-spin"></i> Сохранение...
					</span>
					<span ng-if="!isModalSaving">
						<i class="fas fa-save"></i> Сохранить
					</span>
				</button>
			</div>
		</div>
	</div>
</div>