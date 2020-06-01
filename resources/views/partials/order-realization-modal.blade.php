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
				<div class="form-group">
					<input type="text" class="form-control" ng-model="modalOrder.realization_date_raw" ui-mask="99.99.9999">
				</div>

				<table class="table">
					<tr>
						<th>Продукт</th>
						<th>Готово</th>
						<th>В наличии</th>
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
						<td ng-init="realization.current_ready = realization.ready - realization.performed">
							@{{ realization.current_ready }} <span ng-bind-html="realization.product.units_text"></span>
						</td>
						<td>
							@{{ realization.product.in_stock }} <span ng-bind-html="realization.product.units_text"></span>
						</td>
						<td ng-init="realization.old_performed = realization.performed">
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
				<button type="button" class="btn btn-primary" ng-click="saveRealization()">
					<i class="fas fa-save"></i> Сохранить
				</button>
			</div>
		</div>
	</div>
</div>