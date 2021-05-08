<div class="statuses-page" ng-init="init()">
	<h1>Статусы</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('employments') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к графику работ
			</a>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': showAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('employments') }}" class="btn-link">перейти к графику работ</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<table class="table">
			<tr>
				<th style="width: 15%;">Название</th>
				<th style="width: 18%;">Тип оплаты</th>
				<th style="width: 15%;">Базовое значение</th>
				<th style="width: 15%;">Значение</th>
				<th style="width: 15%;">Значение по умолчанию</th>
				<th style="width: 8%;">Иконка</th>
				<th style="width: 8%;">Цвет иконки</th>
				<th></th>
			</tr>

			<tr ng-repeat="status in statuses">
				<td>
					<input type="text" class="form-control" ng-model="status.name">
				</td>
				<td ng-init="status.type = ('' + status.type)">
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioProduction@{{ $index }}" ng-model="status.type" value="production">
						<label class="custom-control-label" for="radioProduction@{{ $index }}">
							сдельная
						</label>
					</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioHours@{{ $index }}" ng-model="status.type" value="hours">
						<label class="custom-control-label" for="radioHours@{{ $index }}">
							почасовая
						</label>
					</div>
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="radioFixed@{{ $index }}" ng-model="status.type" value="fixed">
						<label class="custom-control-label" for="radioFixed@{{ $index }}">
							фиксированная
						</label>
					</div>
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.base_salary" ng-disabled="status.type != 'hours'">
					<span class="help" ng-if="status.type=='hours'">Оплата за 1 час</span>
					<span class="help" ng-if="status.type=='production'">Зависит от объема производства</span>
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.salary" ng-disabled="status.customable">
					<div class="custom-control custom-checkbox" >
						<input type="checkbox" class="custom-control-input" ng-model="status.customable" id="checkboxManual@{{ $index }}">
						<label class="custom-control-label" for="checkboxManual@{{ $index }}">
							Ручной ввод значения
						</label>
					</div>
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.default_salary" ng-disabled="!status.customable">
				</td>
				<td ng-style="{'color': status.icon_color}">
					<ui-select ng-model="status.icon" skip-focusser="true">
			            <ui-select-match placeholder="...">

				            <span ng-if="$select.selected != 'name'" ng-bind-html="$select.selected"></span>
				        </ui-select-match>
			            <ui-select-choices repeat="statusTemplate in statusTemplates">

			                <span ng-if="statusTemplate != 'name'" ng-bind-html="statusTemplate"></span>
			            </ui-select-choices>
					</ui-select>
				</td>
				<td>
					<color-picker ng-model="status.icon_color">
					</color-picker>
				</td>
				<td>
					<button type="button" class="btn btn-primary" ng-click="deleteStatus($index)">
						<i class="far fa-trash-alt"></i>
					</button>
				</td>
			</tr>
		</table>

		<button type="button" class="btn btn-primary" ng-click="addStatus()">
			<i class="fas fa-plus"></i> Добавить статус	
		</button>

		<div class="buttons-block">
			<button class="btn btn-primary" ng-click="save()" ng-disabled="isSaving">
				<span ng-if="isSaving">
					<i class="fa fa-spinner fa-spin"></i> Сохранение...
				</span>
				<span ng-if="!isSaving">
					<i class="fas fa-save"></i> Сохранить
				</span>
			</button>
		</div>
	</div>
</div>