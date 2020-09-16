<div class="modal realization-modal" ng-show="isRealizationModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<span ng-if="!isOrderRealizationEditiong">Выдача заказа №@{{ modalOrder.number }}</span>
					<span ng-if="isOrderRealizationEditiong">Редактирование выдачи заказа №@{{ modalOrder.number }}</span>
				</div>
				<button type="button" class="close" ng-click="hideRealizationModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="form-group" ng-if="!isOrderRealizationEditting">
					<div class="param-label">Дата</div>
					<input type="text" class="form-control" ng-model="modalOrder.realization_date_raw" ui-mask="99.99.9999">
				</div>

				<table class="table table-sm">
					<tr>
						<th ng-if="isOrderRealizationEditting">Дата</th>
						<th>Продукт</th>
						<th ng-if="!isOrderRealizationEditting" class="text-center">Осталось</th>
						<th ng-if="!isOrderRealizationEditting" class="text-center">В наличии</th>
						<th ng-if="!isOrderRealizationEditting">Отпустить</th>
						<th ng-if="isOrderRealizationEditting">Отпущено</th>
					</tr>

					<tr ng-repeat="realization in modalOrder.realizations">
						<td ng-if="isOrderRealizationEditting" style="padding-right: 20px;">
							<input type="text" class="form-control form-control-sm" ng-model="modalOrder.realization_date_raw" ui-mask="99.99.9999">
						</td>
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
						<td ng-if="!isOrderRealizationEditting" class="text-center" ng-class="{'text-success': realization.planned == 0}">
							<div ng-if="realization.product.id">
								@{{ realization.planned }} <span ng-bind-html="realization.product.units_text"></span>
							</div>
							<div ng-if="!realization.product.id">
								@{{ modalOrder.pallets_progress.planned }} шт
							</div>
						</td>
						<td ng-if="!isOrderRealizationEditting" class="text-center">
							<div ng-if="realization.planned > 0">
								<div ng-if="realization.product.id">
									@{{ realization.product.in_stock }} <span ng-bind-html="realization.product.units_text"></span>
								</div>
								<div ng-if="!realization.product.id">
									—
								</div>
							</div>
							<div ng-if="realization.planned == 0">
								—
							</div>
						</td>
						<td ng-init="realization.old_performed = realization.performed">
							<div ng-if="(realization.planned > 0) || isOrderRealizationEditting">
								<div class="form-group-units">
									<input type="text" class="form-control form-control-sm" ng-model="realization.performed" ng-change="inputFloat(realization, 'performed'); checkAllRealizations(realization)" ng-disabled="realization.max_performed == 0"> 
									<div class="units">
										@{{ realization.performed }} <span ng-bind-html="(realization.product.id > 0) ? realization.product.units_text : 'шт'"></span>
									</div>
								</div>
							</div>
							<div ng-if="(realization.planned == 0) && !isOrderRealizationEditting" class="text-center">
								—
							</div>
						</td>
					</tr>
				</table>

				<div ng-if="!isOrderRealizationEditting">
					<div class="custom-control custom-checkbox" ng-show="!modalOrder.disabled_realizations">
						<input type="checkbox" class="custom-control-input" ng-model="$parent.isAllRealizationsChosen" ng-change="chooseAllRealizations()" id="checkboxRealizations">
						<label class="custom-control-label" for="checkboxRealizations">
							Отпустить все доступные продукты
						</label>
					</div>

					<div ng-show="modalOrder.disabled_realizations">
						Нет доступных продуктов
					</div>
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