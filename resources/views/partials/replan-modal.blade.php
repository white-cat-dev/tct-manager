<div class="modal delete-modal" ng-show="isReplanModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<i class="far fa-calendar-check"></i> Перестроение плана производства
				</div>
				<button type="button" class="close" ng-click="hideReplanModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				Вы уверены, что хотите перестроить план производства?
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-success" ng-click="replan()" ng-if="!isReplaning">
					<i class="fas fa-check"></i> Да
				</button>
				<button type="button" class="btn btn-primary" ng-click="hideReplanModal()" ng-if="!isReplaning">
					<i class="fas fa-times"></i> Нет
				</button>

				<button type="button" class="btn btn-primary" disabled ng-if="isReplaning">
					<i class="fa fa-spinner fa-spin"></i> Перестроение плана...
				</button>
			</div>
		</div>
	</div>
</div>