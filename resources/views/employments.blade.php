<div class="employments-page" ng-init="init()">
	<h1>График работ</h1>

	@include('partials.loading')

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

			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" ng-model="isSalariesShown" id="checkboxSalaries">
				<label class="custom-control-label" for="checkboxSalaries">
					Показать расчет зарплаты
				</label>
			</div>
		</div>

		<div class="right-buttons">
			<button type="button" class="btn btn-primary" ng-click="save()" ng-disabled="isSaving" style="width: 220px;">
				<span ng-if="isSaving">
					<i class="fa fa-spinner fa-spin"></i> Сохранение изменений
				</span>
				<span ng-if="!isSaving">
					<i class="fas fa-save"></i> Сохранить изменения
				</span>
			</button>
		</div>
	</div>


	<div class="row">
		<div class="col-9">
			<div class="employment-block" ng-if="workers.length > 0">
				<div class="workers-block">
					<table class="table">
						<tr>
							<th>Работник</th>
						</tr>
					</table>

					<div class="workers-block-content">
						<table class="table">
							<tr ng-repeat="worker in workers">
								<td>
									<div class="employment-name">
										@{{ worker.name }}
									</div>
								</td>
							</tr>
						</table>

						<table class="table">
							<tr>
								<td>@{{ manager.name }}</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="employments-block">
					<div class="employments-block-top-table">
						<div>
							<table class="table top-table">
								<tr>
									<th ng-repeat="x in [].constructor(days) track by $index" ng-class="{'current': $index + 1 == currentDate.day}" ng-click="showEmploymentModal($index+1)">
										@{{ $index + 1 }}
										<div class="employments-total" ng-if="employments[$index+1]">
											@{{ employments[$index+1].team }}
										</div>
									</th>
									<th style="min-width: 70px">Итого</th>
								</tr>
							</table>
						</div>
					</div>
					
					<div class="employments-block-content">
						<table class="table">
							<tr ng-repeat="worker in workers">
								<td ng-repeat="x in [].constructor(days) track by $index" ng-click="changeEmploymentStatus(worker, $index+1)" ng-style="{'color': worker.employments[$index+1] ? statuses[worker.employments[$index+1].status_id].icon_color : ''}" ng-class="{'current': $index + 1 == currentDate.day}" ng-mouseenter="chooseHoverDay($index + 1)" ng-mouseleave="chooseHoverDay(0)">
									<div class="employment" ng-if="worker.employments[$index+1]">
										<div ng-if="!isSalariesShown">
											<div ng-bind-html="statuses[worker.employments[$index+1].status_id].icon" ng-if="!statuses[worker.employments[$index+1].status_id].customable"></div>

											<div ng-if="statuses[worker.employments[$index+1].status_id].customable">
												<div ng-if="(currentEmploymentStatus != worker.employments[$index+1].status_id) || (!statuses[currentEmploymentStatus].customable)">
													@{{ worker.employments[$index+1].status_custom }} 
												</div>
												<div ng-if="(currentEmploymentStatus == worker.employments[$index+1].status_id) && (statuses[currentEmploymentStatus].customable)">
													<input type="text" class="form-control" ng-model="worker.employments[$index+1].status_custom" ng-keypress="inputKeyPressed($event)" ng-change="inputFloat(worker.employments[$index+1], 'status_custom')" ng-blur="updateTotalEmployment(worker)">
												</div>
											</div>

											<div class="employment-category" ng-style="{'border-bottom-color': mainCategories[worker.employments[$index+1].main_category].icon_color}"></div>
										</div>

										<div class="employment-salary" ng-if="isSalariesShown">
											@{{ worker.employments[$index+1].salary }}
										</div>
									</div>
								</td>
								<td style="min-width: 70px">
									<span ng-if="isSalariesShown">@{{ worker.salary.employments | number }}</span>
									<span ng-if="!isSalariesShown">@{{ worker.totalEmployment }}</span> 
								</td>
							</tr>
						</table>

						<table class="table">			
							<tr>
								<td ng-repeat="x in [].constructor(days) track by $index" ng-click="changeEmploymentStatus(manager, $index+1)" ng-style="{'color': manager.employments[$index+1] ? statuses[manager.employments[$index+1].status_id].icon_color : ''}" ng-class="{'current': $index + 1 == currentDate.day}" ng-mouseenter="chooseHoverDay($index + 1)" ng-mouseleave="chooseHoverDay(0)">
									<div class="employment" ng-if="manager.employments[$index+1]">
										<div ng-if="!isSalariesShown">
											<div ng-bind-html="statuses[manager.employments[$index+1].status_id].icon" ng-if="!statuses[manager.employments[$index+1].status_id].customable"></div>

											<div ng-if="statuses[manager.employments[$index+1].status_id].customable">
												<div ng-if="(currentEmploymentStatus != manager.employments[$index+1].status_id) || (!statuses[currentEmploymentStatus].customable)">
													@{{ manager.employments[$index+1].status_custom }} 
												</div>
												<div ng-if="(currentEmploymentStatus == manager.employments[$index+1].status_id) && (statuses[currentEmploymentStatus].customable)">
													<input type="text" class="form-control" ng-model="manager.employments[$index+1].status_custom" ng-keypress="inputKeyPressed($event)" ng-change="inputFloat(manager.employments[$index+1], 'status_custom')" ng-blur="updateTotalEmployment(worker)">
												</div>
											</div>

											<div class="employment-category" ng-style="{'border-bottom-color': mainCategories[manager.employments[$index+1].main_category].icon_color}"></div>
										</div>

										<div class="employment-salary" ng-if="isSalariesShown">
											@{{ manager.employments[$index+1].salary }}
										</div>
									</div>
								</td>
								<td style="min-width: 70px">
									<span ng-if="isSalariesShown">@{{ manager.salary.employments | number }}</span>
									<span ng-if="!isSalariesShown">@{{ manager.totalEmployment }}</span> 
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>


			<div class="no-employments" ng-if="(workers.length == 0) && !isLoading">
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

	<div class="salaries-block" ng-show="!isLoading && workers.length > 0">
		<div class="block-title">
			Данные о зарплате за 
			<span ng-repeat="month in monthes" ng-if="currentDate.month == month.id">
				@{{ month.name | lowercase }}
			</span> 
			<span>
				@{{ currentDate.year }} года
			</span>
		</div>

		<div class="alert alert-warning">
			Не забудьте сохранить все изменения в графике работ, прежде чем просматривать расчет зарплаты
		</div>
			
		<div class="table-block">
			<table class="table table-with-buttons">
				<tr>
					<th>Работник</th>
					<th>График</th>
					<th>Дни</th>
					<th>Аванс</th>
					<th>Налоги</th>
					{{-- <th>Обед</th> --}}
					<th>Премия</th>
					<th>Доплата</th>
					<th>Итого</th>
					<th></th>
				</tr>

				<tr ng-repeat="worker in workers">
					<td>
						@{{ worker.name }}
					</td>
					<td>
						@{{ worker.salary.employments | number }} руб
					</td>
					<td>
						@{{ worker.totalEmployment | number }}
					</td>
					<td>
						@{{ worker.salary.advance | number }} руб
					</td>
					<td>
						@{{ worker.salary.tax | number }} руб
					</td>
					{{-- <td>
						@{{ worker.salary.lunch | number }} руб
					</td> --}}
					<td>
						@{{ worker.salary.bonus | number }} руб
					</td>
					<td>
						@{{ worker.salary.surcharge | number }} руб
					</td>
					<td>
						@{{ (worker.salary.employments - worker.salary.advance - worker.salary.tax - worker.salary.lunch + +worker.salary.bonus + +worker.salary.surcharge) | number }} руб
					</td>
					<td>
						@if (Auth::user() && Auth::user()->type == 'admin')
						<button type="button" class="btn btn-sm btn-primary" ng-click="showSalaryModal(worker)">
							<i class="fas fa-edit"></i> Изменить
						</button>
						@endif
					</td>
				</tr>
				<tr>
					<td>
						@{{ manager.name }}
					</td>
					<td>
						@{{ manager.salary.employments | number }} руб
					</td>
					<td>
						@{{ manager.totalEmployment | number }}
					</td>
					<td>
						@{{ manager.salary.advance | number }} руб
					</td>
					<td>
						@{{ manager.salary.tax | number }} руб
					</td>
					{{-- <td>
						@{{ manager.salary.lunch | number }} руб
					</td> --}}
					<td>
						@{{ manager.salary.bonus | number }} руб
					</td>
					<td>
						@{{ manager.salary.surcharge | number }} руб
					</td>
					<td>
						@{{ (manager.salary.employments - manager.salary.advance - manager.salary.tax - manager.salary.lunch + +manager.salary.bonus + +manager.salary.surcharge) | number }} руб
					</td>
					<td>
						@if (Auth::user() && Auth::user()->type == 'admin')
						<button type="button" class="btn btn-sm btn-primary" ng-click="showSalaryModal(manager)">
							<i class="fas fa-edit"></i> Изменить
						</button>
						@endif
					</td>
				</tr>
				<tr>
					<td>
						Итого:
					</td>
					<td>
						@{{ totalSalary.employments | number }} руб
					</td>
					<td>
						@{{ totalSalary.totalEmployment | number }}
					</td>
					<td>
						@{{ totalSalary.advance | number }} руб
					</td>
					<td>
						@{{ totalSalary.tax | number }} руб
					</td>
					{{-- <td>
						@{{ totalSalary.lunch | number }} руб
					</td> --}}
					<td>
						@{{ totalSalary.bonus | number }} руб
					</td>
					<td>
						@{{ totalSalary.surcharge | number }} руб
					</td>
					<td>
						@{{ (totalSalary.employments - totalSalary.advance - totalSalary.tax - totalSalary.lunch + +totalSalary.bonus + +totalSalary.surcharge) | number }} руб
					</td>
					<td></td>
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
					<table class="table">
						<tr>
							<td>Работник</td>
							<td>
								@{{ modalWorker.name }}
							</td>
						</tr>
						<tr>
							<td>Зарплата по графику</td>
							<td>
								@{{ modalWorker.salary.employments | number }} руб
							</td>
						</tr>
						<tr>
							<td>Аванс</td>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control form-control-sm" ng-model="modalWorker.salary.advance" ng-change="inputFloat(modalWorker.salary, 'advance')">
									<div class="units">
										@{{ modalWorker.salary.advance }} <span>руб</span>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>Налоги</td>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control form-control-sm" ng-model="modalWorker.salary.tax" ng-change="inputFloat(modalWorker.salary, 'tax')">
									<div class="units">
										@{{ modalWorker.salary.tax }} <span>руб</span>
									</div>
								</div>
							</td>
						</tr>
						{{-- <tr>
							<td>Обед</td>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control form-control-sm" ng-model="modalWorker.salary.lunch" ng-change="inputFloat(modalWorker.salary, 'lunch')">
									<div class="units">
										@{{ modalWorker.salary.lunch }} <span>руб</span>
									</div>
								</div>
							</td>
						</tr> --}}
						<tr>
							<td>Премия</td>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control form-control-sm" ng-model="modalWorker.salary.bonus" ng-change="inputFloat(modalWorker.salary, 'bonus')">
									<div class="units">
										@{{ modalWorker.salary.bonus }} <span>руб</span>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>Доплата</td>
							<td>
								<div class="form-group-units">
									<input type="text" class="form-control form-control-sm" ng-model="modalWorker.salary.surcharge" ng-change="inputFloat(modalWorker.salary, 'surcharge')">
									<div class="units">
										@{{ modalWorker.salary.surcharge }} <span>руб</span>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>Итого</td>
							<td>
								@{{ (modalWorker.salary.employments - modalWorker.salary.advance - modalWorker.salary.tax - modalWorker.salary.lunch + +modalWorker.salary.bonus + +modalWorker.salary.surcharge) | number }} руб
							</td>
						</tr>
					</table>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="saveSalary()" ng-disabled="isModalSaving">
						<span ng-if="isModalSaving">
							<i class="fa fa-spinner fa-spin"></i> Сохранение
						</span>
						<span ng-if="!isModalSaving">
							<i class="fas fa-save"></i> Сохранить
						</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal employment-modal" ng-show="isEmploymentModalShown" tabindex="0">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-title">
						Расчет зарплаты на @{{ modalDate | date: 'dd.MM.yyyy' }}
					</div>
					<button type="button" class="close" ng-click="hideEmploymentModal()">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="modal-body">
					<div class="table-responsive-block" ng-if="modalEmployment">
						<table class="table">
							<tr>
								<th>Категория</th>
								<th>Оплата</th>
								<th>Команда</th>
								<th>На человека</th>
							</tr>

						    <tr ng-repeat="(category, employment) in modalEmployment">
						        <td>
						        	<span ng-switch on="category">
						        		<span ng-switch-when="tiles">Плитка</span>
						        		<span ng-switch-when="blocks">Блоки</span>
						        	</span>
						        </td>

						        <td class="text-center">
						        	@{{ employment.salary | number }} руб
						        </td>

						        <td class="text-center">
						        	@{{ Math.round(employment.team * 100) / 100 }}
						        </td>

						        <td class="text-center">
									@{{ employment.person_salary  | number }} руб
								</td>
						    </tr>
						</table>
					</div>

					<div class="alert alert-secondary" ng-if="!modalEmployment">
						<i class="far fa-calendar-times"></i> Нет расчетов на данный день
					</div>
				</div>
			</div>
		</div>
	</div>
</div>