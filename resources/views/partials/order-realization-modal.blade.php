<div class="modal realization-modal" ng-show="isRealizationModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Выдача заказа №@{{ modalOrder.number }}
				</div>
				<button type="button" class="close" ng-click="hideRealizationModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<table class="table">
					<tr>
						<th>Продукт</th>
						<th>Готово</th>
						<th>Выдано</th>
					</tr>

					<tr ng-repeat="realization in modalOrder.realizations" ng-if="!realization.date">
						<td>
							@{{ realization.product.product_group.name }}
							@{{ realization.product.product_group.size }}
							<div class="product-color" ng-if="realization.product.variation_noun_text">
								@{{ realization.product.variation_noun_text }}
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
							<input type="text" class="form-control" ng-model="realization.performed" ng-change="checkAllRealizations(realization)"> 
						</td>
					</tr>
				</table>

				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" ng-model="isAllRealizationsChosen" ng-change="chooseAllRealizations()" id="checkboxRealizations">
					<label class="custom-control-label" for="checkboxRealizations">
						Отпустить все готовые продукты
					</label>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveRealizations()">
					<i class="fas fa-save"></i> Сохранить
				</button>
			</div>
		</div>
	</div>
</div>