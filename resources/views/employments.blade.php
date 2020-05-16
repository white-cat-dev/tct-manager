<div class="employments-page" ng-init="init()">
	<h1>График работ</h1>

	@include('partials.top-alerts')

	<div class="row">
		<div class="col-12 col-lg-9">
			<div class="top-buttons-block">
				<div class="left-buttons">
					<div class="input-group date-group">
						<button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.year = currentDate.year - 1; init()" ng-disabled="currentDate.year == years[0]">
						    <i class="fas fa-chevron-left"></i>
						</button>

						<ui-select ng-model="currentDate.year" ng-change="init()" skip-focusser="true" search-enabled="false">
				            <ui-select-match placeholder="Год">
					            <span ng-bind-html="$select.selected"></span>
					        </ui-select-match>
				            <ui-select-choices repeat="year in years">
				                <span ng-bind-html="year"></span>
				            </ui-select-choices>
						</ui-select>

						<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.year = currentDate.year + 1; init()" ng-disabled="currentDate.year == years[years.length - 1]">
						    <i class="fas fa-chevron-right"></i>
						</button>
					</div>

					<div class="input-group date-group">
					    <button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.month = currentDate.month - 1; init()" ng-disabled="currentDate.month == monthes[0].id">
						    <i class="fas fa-chevron-left"></i>
						</button>

						<ui-select ng-model="currentDate.month" ng-change="init()" skip-focusser="true" search-enabled="false">
				            <ui-select-match placeholder="Месяц">
					            <span ng-bind-html="$select.selected.name"></span>
					        </ui-select-match>
				            <ui-select-choices repeat="month.id as month in monthes">
				                <span ng-bind-html="month.name"></span>
				            </ui-select-choices>
						</ui-select>

						<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.month = currentDate.month + 1; init()" ng-disabled="currentDate.month == monthes[monthes.length - 1].id">
						    <i class="fas fa-chevron-right"></i>
						</button>
					</div>
				</div>

				<div class="right-buttons">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" ng-model="isSalariesShown" id="checkboxSalaries">
						<label class="custom-control-label" for="checkboxSalaries">
							Показать расчет зарплаты
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-lg-3">
			<div class="top-buttons-block">
				<div class="left-buttons">
					
				</div>
				<div class="right-buttons">
					<button type="button" class="btn btn-primary" ng-click="save()">
						<i class="fas fa-save"></i> Сохранить изменения
					</button>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-9">
			<div class="employments-block" ng-if="workers.length > 0">
				<div class="workers">
					<table class="table">
						<tr>
							<th></th>
						</tr>
						<tr ng-repeat="worker in workers">
							<td>@{{ worker.name }}</td>
						</tr>
					</table>
				</div>

				<div class="employments">
					<table class="table">
						<tr>
							<th ng-repeat="x in [].constructor(days) track by $index">@{{ $index + 1 }}</th>
							<th ng-if="isSalariesShown">Итого</th>
						</tr>
					
						<tr ng-repeat="worker in workers">
							<td ng-repeat="x in [].constructor(days) track by $index" ng-click="changeEmploymentStatus(worker, $index+1)" ng-style="{'color': worker.employments[$index+1] ? statuses[worker.employments[$index+1].status_id].icon_color : ''}">
								<div class="employment" ng-if="worker.employments[$index+1]">
									<div ng-if="!isSalariesShown">
										<div ng-bind-html="statuses[worker.employments[$index+1].status_id].icon" ng-if="!statuses[worker.employments[$index+1].status_id].customable"></div>

										<div ng-if="statuses[worker.employments[$index+1].status_id].customable">
											<div ng-if="!statuses[currentEmploymentStatus].customable">
												@{{ worker.employments[$index+1].status_custom }} 
											</div>
											<div ng-if="statuses[currentEmploymentStatus].customable">
												<div class="edit-field" ng-show="!isEditFieldShown" ng-click="isEditFieldShown = true; focusNextInput($event);">
													@{{ worker.employments[$index+1].status_custom }} 
												</div>
												<input type="text" class="form-control" ng-model="worker.employments[$index+1].status_custom" ng-show="isEditFieldShown" ng-blur="isEditFieldShown = false" ng-keypress="inputKeyPressed($event)">
											</div>
										</div>

										<div class="employment-category" ng-style="{'border-bottom-color': mainCategories[worker.employments[$index+1].main_category].icon_color}"></div>
									</div>

									<div class="employment-salary" ng-if="isSalariesShown">
										@{{ worker.employments[$index+1].salary }}
									</div>
								</div>
							</td>
							<td ng-if="isSalariesShown">
								@{{ worker.salary.employments }}
							</td>
						</tr>
					</table>
				</div>
			</div>


			<div class="no-employments" ng-if="workers.length == 0">
				<div>
					<i class="far fa-calendar-times"></i>
				</div>
				Нет данных на текущий месяц
			</div>
		</div>
		
		<div class="col-3">
			<div class="employment-statuses">
				<div class="statuses-title">
					Инструменты
					@if (Auth::user() && Auth::user()->type == 'admin')
					<a href="{{ route('employment-statuses') }}" class="btn btn-primary btn-sm">
						<i class="fas fa-edit"></i> <span class="d-none d-xl-inline">Изменить</span>
					</a>
					@endif
				</div>

				<div class="statuses-label">
					Выберите статус
				</div>

				<table class="table" ng-if="Object.keys(statuses).length > 0">
					<tr ng-repeat="status in statuses" ng-if="status.icon != 'name'" ng-class="{'active': currentEmploymentStatus == status.id}" ng-click="chooseCurrentEmploymentStatus(status.id)">
						<td>
							<span ng-bind-html="status.icon" ng-style="{'color': status.icon_color}"></span>
						</td>
						<td>
							@{{ status.name }}
						</td>
					</tr>
				</table>

				<div class="statuses-label">
					Выберите категорию
				</div>

				<table class="table" ng-if="Object.keys(mainCategories).length > 0">
					<tr ng-repeat="mainCategory in mainCategories" ng-class="{'active': currentMainCategory == mainCategory.key}" ng-click="chooseCurrentMainCategory(mainCategory.key)">
						<td>
							<div ng-style="{'background': mainCategory.icon_color}"></div>
						</td>
						<td>
							@{{ mainCategory.name }}
						</td>
					</tr>
				</table>

				<button type="button" class="btn btn-sm" ng-click="chooseCleanCurrent()" ng-class="{'active': cleanCurrent}">
					<i class="fas fa-eraser"></i> Очистить
				</button>
			</div>
		</div>
	</div>

	<div class="salaries-block">
		<div class="block-title">
			Данные о зарплате за 
			<span ng-repeat="month in monthes" ng-if="currentDate.month == month.id">
				@{{ month.name | lowercase }}
			</span> 
			<span>
				@{{ currentDate.year }} года
			</span>
		</div>
			
		<div class="table-block">
			<table class="table table-with-buttons">
				<tr>
					<th>Работник</th>
					<th>Зарплата по графику</th>
					<th>Аванс</th>
					<th>Премия</th>
					<th>Итого</th>
					<th></th>
				</tr>

				<tr ng-repeat="worker in workers">
					<td>
						@{{ worker.name }}
					</td>
					<td>
						@{{ worker.salary.employments | number }} руб.
					</td>
					<td>
						@{{ worker.salary.advance | number }} руб.
					</td>
					<td>
						@{{ worker.salary.bonus | number }} руб.
					</td>
					<td>
						@{{ (worker.salary.employments - worker.salary.advance + +worker.salary.bonus) | number }} руб.
					</td>
					<td>
						@if (Auth::user() && Auth::user()->type == 'admin')
						<button type="button" class="btn btn-sm btn-primary" ng-click="showSalaryModal(worker)">
							<i class="fas fa-edit"></i> Изменить
						</button>
						@endif
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="modal salary-modal" ng-show="isSalaryModalShown">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">
						Редактирование зарплаты
					</div>
					<button type="button" class="close" ng-click="hideSalaryModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="param-label">Работник</div>
						<div class="param-value">
							@{{ modalWorker.name }}
						</div>
					</div>
					<div class="form-group">
						<div class="param-label">Зарплата по графику</div>
						<div class="param-value">
							@{{ modalWorker.salary.employments | number }} руб.
						</div>
					</div>
					<div class="form-group">
						<div class="param-label">Аванс</div>
						<input type="text" class="form-control" ng-model="modalWorker.salary.advance"> руб
					</div>
					<div class="form-group">
						<div class="param-label">Премия</div>
						<input type="text" class="form-control" ng-model="modalWorker.salary.bonus"> руб
					</div>
					<div class="form-group">
						<div class="param-label">Итого</div>
						<div class="param-value">
							@{{ (modalWorker.salary.employments - modalWorker.salary.advance + +modalWorker.salary.bonus) | number }} руб.
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="saveSalary()">
						<i class="fas fa-save"></i> Сохранить
					</button>
				</div>
			</div>
		</div>
	</div>
</div>