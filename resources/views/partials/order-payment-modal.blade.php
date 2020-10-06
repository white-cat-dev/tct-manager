<div class="modal payment-modal" ng-show="isPaymentModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<span ng-if="!isOrderPaymentEditiong">Оплата заказа №@{{ modalOrder.number }}</span>
					<span ng-if="isOrderPaymentEditiong">Редактирование оплаты заказа №@{{ modalOrder.number }}</span>
				</div>
				<button type="button" class="close" ng-click="hidePaymentModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="info-block" ng-if="!isOrderPaymentEditting">
					<table class="table table-sm">
						<tr>
							<th>Стоимость</th>
							<th>Оплачено</th>
							<th>Осталось</th>
						</tr>
						<tr>
							<td>
								@{{ modalOrder.cost | number }} руб
							</td>
							<td>
								@{{ modalOrder.payments_paid | number }} руб
							</td>
							<td>
								@{{ modalOrder.cost -  modalOrder.payments_paid | number }} руб
							</td>
						</tr>
					</table>
				</div>

				<div class="form-group">
					<div class="param-label">Дата</div>
					<input type="text" class="form-control" ng-model="modalPayment.date_raw" ui-mask="99.99.9999">
				</div>

				<div class="form-group">
					<div class="param-label">Сумма платежа</div>
					<div class="form-group-units">
						<input type="text" class="form-control" ng-model="modalPayment.paid" ng-change="inputFloat(modalPayment, 'paid'); checkFullPayment()">
						<div class="units">
							@{{ modalPayment.paid }} <span ng-bind-html="'руб'"></span>
						</div>
					</div>
				</div>

				<div class="custom-control custom-checkbox" ng-if="!isOrderPaymentEditting">
					<input type="checkbox" class="custom-control-input" ng-model="$parent.isFullPaymentChosen" ng-change="chooseFullPayment()" id="checkboxPayment">
					<label class="custom-control-label" for="checkboxPayment">
						Полная оплата
					</label>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="savePayment()" ng-disabled="isModalSaving">
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