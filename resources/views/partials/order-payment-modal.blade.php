<div class="modal payment-modal" ng-show="isPaymentModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Оплата заказа №@{{ modalOrder.number }}
				</div>
				<button type="button" class="close" ng-click="hidePaymentModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="info-block">
					<div class="row">
						<div class="col-6">
							<div class="param-label">Стоимость</div>
							@{{ modalOrder.cost | number }} руб
						</div>
						<div class="col-6">
							<div class="param-label">Оплачено</div>
							@{{ modalOrder.paid | number }} руб
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="param-label">Дата</div>
					<input type="text" class="form-control" ng-model="modalPayment.date_raw" ui-mask="99.99.9999">
				</div>

				<div class="form-group">
					<div class="param-label">Сумма платежа</div>
					<input type="text" class="form-control" ng-model="modalPayment.paid" ng-change="checkFullPayment()">
				</div>

				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" ng-model="isFullPaymentChosen" ng-change="chooseFullPayment()" id="checkboxPayment">
					<label class="custom-control-label" for="checkboxPayment">
						Полная оплата
					</label>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="savePayment()" ng-disabled="isSaving">
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