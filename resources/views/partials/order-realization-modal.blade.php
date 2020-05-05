<div class="modal realization-modal" ng-show="isRealizationModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Выдача заказа №@{{ modalOrder.id }}
				</div>
				<button type="button" class="close" ng-click="hideRealizationModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<tr>
						<th>Продукт</th>
						<th>Готово к выдаче</th>
						<th>Выдано</th>
					</tr>

					<tr ng-repeat="realization in modalOrder.realizations">
						<td>
							<div class="product-name">
								@{{ realization.product.product_group.name }}
							</div> 
							<div class="product-size">
								@{{ realization.product.product_group.size }} мм
							</div>
							<div class="product-color" ng-if="realization.product.color_text">
								@{{ realization.product.color_text }} цвет
							</div>
						</td>
						<td>
							@{{ realization.planned }} 
							<span ng-switch on="realization.product.category.units">
								<span ng-switch-when="area">м<sup>2</sup></span>
								<span ng-switch-when="volume">м<sup>3</sup></span>
								<span ng-switch-when="unit">шт.</span>
							</span>
						</td>
						<td>
							<input type="text" class="form-control" ng-model="realization.performed"> 
						</td>
					</tr>
				</table>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveRealizations()">
					<i class="fas fa-save"></i> Сохранить
				</button>
			</div>
		</div>
	</div>
</div>