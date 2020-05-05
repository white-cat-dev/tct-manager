<div class="modal facility-modal" ng-show="isStatusModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<span ng-if="modalFacility.status == {{ App\Facility::STATUS_ACTIVE }}">
						Возобновление работы цеха
					</span>
					<span ng-if="modalFacility.status == {{ App\Facility::STATUS_INACTIVE }}">
						Приостановка работы цеха
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
						<span ng-if="modalFacility.status == {{ App\Facility::STATUS_ACTIVE }}">
							возобновления работы цеха
						</span>
						<span ng-if="modalFacility.status == {{ App\Facility::STATUS_INACTIVE }}">
							приостановки работы цеха
						</span>
					</div>
					<div class="datepicker-block" ng-class="{'disabled': isPlanDateNow}">
						<div class="disabled-block" ng-if="isPlanDateNow"></div>
						<div date-picker view="date" min-view="date" ng-model="modalFacility.status_date_raw"></div>
					</div>
				</div>

				{{-- <div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" ng-model="isPlanDateNow" ng-change="updateStatusNow()" id="checkbox">
					<label class="custom-control-label" for="checkbox">
						<span ng-if="modalFacility.status == {{ App\Facility::STATUS_PLAN_ACTIVE }} || modalFacility.status == {{ App\Facility::STATUS_ACTIVE }}">
							Возобновить
						</span>
						<span ng-if="modalFacility.status == {{ App\Facility::STATUS_PLAN_INACTIVE }} || modalFacility.status == {{ App\Facility::STATUS_INACTIVE }}">
							Приостановить
						</span>
						работу сейчас
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