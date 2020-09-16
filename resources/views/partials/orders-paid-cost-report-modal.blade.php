<div class="modal paid-cost-modal" ng-show="isPaidCostReportModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Отчет по оплатам и выдачам
				</div>
				<button type="button" class="close" ng-click="hidePaidCostReportModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="modal-loading-block" ng-if="isModalLoading">
					<i class="fa fa-cog fa-spin"></i>
				</div>

				<table class="table table-sm" ng-if="modalOrders['debt'].length > 0">
					<tr>
						<th>Заказ</th>
						<th>Выдано</th>
						<th>Оплачено</th>
						<th>Итого</th>
					</tr>
					<tr ng-repeat="order in modalOrders['debt']">
						<td>
							<a ng-href="@{{ order.url }}" ng-click="hidePaidCostReportModal()">
								@{{ order.number ? order.number : order.id }}
							</a>
							<span ng-if="order.client.name">(@{{ order.client.name }})</span>
						</td>
						<td>
							@{{ order.realizations_cost | number }} руб
						</td>
						<td>
							@{{ order.payments_paid | number }} руб
						</td>
						<td>
							@{{ order.total | number }} руб
						</td>
					</tr>
					<tr>
						<th colspan="3" class="text-left">Итого:</th>
						<th>@{{ modalOrders['debt_total'] | number }} руб</th>
					</tr>
				</table>

				<table class="table table-sm" ng-if="modalOrders['overpayment'].length > 0">
					<tr>
						<th>Заказ</th>
						<th>Выдано</th>
						<th>Оплачено</th>
						<th>Итого</th>
					</tr>
					<tr ng-repeat="order in modalOrders['overpayment']">
						<td>
							<a ng-href="@{{ order.url }}" ng-click="hidePaidCostReportModal()">
								@{{ order.number ? order.number : order.id }}
							</a>
							<span ng-if="order.client.name">(@{{ order.client.name }})</span>
						</td>
						<td>
							@{{ order.realizations_cost | number }} руб
						</td>
						<td>
							@{{ order.payments_paid | number }} руб
						</td>
						<td>
							@{{ order.total | number }} руб
						</td>
					</tr>
					<tr>
						<th colspan="3" class="text-left">Итого:</th>
						<th>@{{ modalOrders['overpayment_total'] | number }} руб</th>
					</tr>
				</table>

				<table class="table table-sm" ng-if="(modalOrders['debt'].length > 0) || (modalOrders['overpayment'].length > 0)">
					<tr>
						<th>
							@{{ modalOrders['debt_total'] | number }} руб
						</th>
						<th>
							@{{ modalOrders['overpayment_total'] | number }} руб
						</th>
						<th>
							@{{ modalOrders['total'] | number }} руб
						</th>
					</tr>
				</table>

				<div class="no-paid-cost-block" ng-if="(modalOrders['debt'].length == 0) && (modalOrders['overpayment'].length == 0) && !isModalLoading">
					<div>
						<i class="fas fa-paste"></i>
					</div>
					<div>
						Нет данных
					</div>
				</div>
			</div>
		</div>
	</div>
</div>