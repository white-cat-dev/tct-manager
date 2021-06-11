<div class="modal product-orders-modal" ng-show="isProductOrdersModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Просмотр заказов
				</div>
				<button type="button" class="close" ng-click="hideProductOrdersModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="modal-loading-block" ng-if="isModalLoading">
					<i class="fa fa-cog fa-spin"></i>
				</div>

				<div class="product-name">
					@{{ modalProduct.product_group.name }} @{{ modalProduct.product_group.size }}
					<span class="product-color">
						@{{ modalProduct.variation_text }}
					</span>
				</div>
				<table class="table" ng-if="modalProductOrders.length > 0">
					<tr>
						<th>Заказ</th>
						<th>До</th>
						<th>Количество</th>
						<th>Отпущено</th>
						<th>Осталось</th>
					</tr>

					<tr ng-repeat="order in modalProductOrders" ng-if="order.progress.planned > 0">
						<td style="width: 20%;">
							<a ng-href="@{{ order.url }}" ng-click="hideProductOrdersModal()">@{{ order.number }}</a>
						</td>
						<td style="width: 20%;">
							@{{ order.formatted_date_to }}
						</td>
						<td style="width: 20%;">
							@{{ order.progress.total }} <span ng-bind-html="modalProduct.units_text"></span>
						</td>
						<td style="width: 20%;">
							@{{ order.progress.realization }} <span ng-bind-html="modalProduct.units_text"></span>
						</td>
						<td style="width: 20%;">
							@{{ order.progress.planned }} <span ng-bind-html="modalProduct.units_text"></span>
						</td>
					</tr>
{{-- 					<tr>
						<td colspan="3">
							Итого:
						</td>
						<td>
							@{{ modalProduct.productions[0].planned }}
						</td>
					</tr> --}}
				</table>

				<div class="no-product-orders-block" ng-if="modalProductOrders.length == 0 && !isModalLoading">
					<div>
						<i class="fas fa-shopping-cart"></i>
					</div>
					<div>
						Нет активных заказов
					</div>
				</div>
			</div>
		</div>
	</div>
</div>