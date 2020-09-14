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
						<th>Осталось</th>
						<th>В наличии</th>
						<th>Отпустить</th>
					</tr>

					<tr ng-repeat="realization in modalOrder.realizations" ng-class="{'disabled': realization.max_performed == 0}">
						<td style="width: 40%;">
							<div ng-if="realization.product.id">
								@{{ realization.product.product_group.name }}
								@{{ realization.product.product_group.size }}
								<div class="product-color" ng-if="realization.product.variation_noun_text">
									@{{ realization.product.variation_noun_text }}
								</div>		
							</div>
							<div ng-if="!realization.product.id">
								Поддоны
							</div>
						</td>
						<td style="width: 20%;">
							<div ng-if="realization.product.id">
								@{{ realization.planned }} <span ng-bind-html="realization.product.units_text"></span>
							</div>
							<div ng-if="!realization.product.id">
								@{{ modalOrder.pallets_progress.planned }} шт
							</div>
						</td>
						<td style="width: 20%;">
							<div ng-if="realization.product.id">
								@{{ realization.product.in_stock }} <span ng-bind-html="realization.product.units_text"></span>
							</div>
							<div ng-if="!realization.product.id">
								—
							</div>
						</td>
						<td ng-init="realization.old_performed = realization.performed" style="width: 20%;">
							<input type="text" class="form-control" ng-model="realization.performed" ng-change="inputFloat(realization, 'performed'); checkAllRealizations(realization)" ng-disabled="realization.max_performed == 0"> 
						</td>
					</tr>
				</table>

				<div class="custom-control custom-checkbox" ng-show="!modalOrder.disabled_realizations">
					<input type="checkbox" class="custom-control-input" ng-model="isAllRealizationsChosen" ng-change="chooseAllRealizations()" id="checkboxRealizations">
					<label class="custom-control-label" for="checkboxRealizations">
						Отпустить все доступные продукты
					</label>
				</div>

				<div ng-show="modalOrder.disabled_realizations">
					Нет доступных продуктов
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveRealization()" ng-disabled="modalOrder.disabled_realizations || isSaving">
					<span ng-if="isSaving">
						<i class="fa fa-spinner fa-spin"></i> Сохранение
					</span>
					<span ng-if="!isSaving">
						<i class="fas fa-save"></i> Сохранить
					</span>
				</button>
			</div>
		</div>
	</div>
</div>