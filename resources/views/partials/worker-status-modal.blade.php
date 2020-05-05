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
				<div class="form-group">
					<div class="param-label">
						Выберите дату
						<span ng-if="modalWorker.status == {{ App\Worker::STATUS_ACTIVE }}">
							возвращения на работу
						</span>
						<span ng-if="modalWorker.status == {{ App\Worker::STATUS_INACTIVE }}">
							отстранения от работы
						</span>
					</div>
					<ul class="nav nav-tabs" ng-init="shownTab = 'begin'">
						<li class="nav-item">
							<button type="button" class="nav-link" ng-click="shownTab = 'begin'" ng-class="{'active': shownTab == 'begin'}">Начало</button>
						</li>
						<li class="nav-item">
							<button type="button" class="nav-link" ng-click="shownTab = 'end'" ng-class="{'active': shownTab == 'end'}">Конец</button>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane" ng-class="{'active': shownTab == 'begin'}">
							<div class="datepicker-block">
								<div date-picker view="date" min-view="date" ng-model="modalWorker.status_date_raw"></div>
							</div>
						</div>
						<div class="tab-pane" ng-class="{'active': shownTab == 'end'}">
							<div class="datepicker-block">
								<div date-picker view="date" min-view="date" ng-model="modalWorker.status_date_next_raw"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="date-group">
					<div class="date-label">с</div>
					<input type="text" class="form-control" date-time format="dd.MM.yyyy" ng-model="modalWorker.status_date_raw">
					<div class="date-label">до</div>
					<input type="text" class="form-control" date-time format="dd.MM.yyyy" ng-model="modalWorker.status_date_next_raw">
				</div>

				{{-- <div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" ng-model="isPlanDateNow" ng-change="updateStatusNow()" id="checkbox">
					<label class="custom-control-label" for="checkbox">
						<span ng-if="worker.status == 'plan-active' || worker.status == 'active'">
							Вернуть на работу
						</span>
						<span ng-if="worker.status == 'plan-paused' || worker.status == 'paused'">
							Отстранить от работы
						</span>
						сейчас
					</label>
				</div> --}}
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveStatus()">
					<i class="fas fa-save"></i> Сохранить
				</button>
			</div>
		</div>
	</div>
</div>