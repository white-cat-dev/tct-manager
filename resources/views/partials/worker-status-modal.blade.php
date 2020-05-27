<div class="modal worker-modal" ng-show="isStatusModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<span ng-if="modalWorker.status == {{ App\Worker::STATUS_ACTIVE }}">
						Возвращение на работу
					</span>
					<span ng-if="modalWorker.status == {{ App\Worker::STATUS_INACTIVE }}">
						Отстранение от работы
					</span>
				</div>
				<button type="button" class="close" ng-click="hideStatusModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="form-group" ng-init="shownTab = 'begin'">
					<div class="param-label">
						Выберите дату
						<span ng-if="modalWorker.status == {{ App\Worker::STATUS_ACTIVE }}">
							возвращения на работу
						</span>
						<span ng-if="modalWorker.status == {{ App\Worker::STATUS_INACTIVE }}">
							отстранения от работы
						</span>
					</div>

					<div class="datepicker-block">
						<div date-picker view="date" min-view="date" ng-model="modalWorker.status_date_raw" watch-direct-changes="true"></div>
					</div>
				</div>

				<small class="form-text">
					Если хотите 
					<span ng-if="modalWorker.status == {{ App\Worker::STATUS_ACTIVE }}">
						вернуть работника на работу
					</span>
					<span ng-if="modalWorker.status == {{ App\Worker::STATUS_INACTIVE }}">
						отстранить работника от работы
					</span>
					сейчас, выберите текущую дату
				</small>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveStatus()">
					<i class="fas fa-save"></i> Сохранить
				</button>
			</div>
		</div>
	</div>
</div>