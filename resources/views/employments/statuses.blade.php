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
				<th>Название</th>
				<th>Производство</th>
				<th>Фиксированная</th>
				<th>Иконка</th>
				<th>Цвет иконки</th>
				<th></th>
			</tr>

			<tr ng-repeat="status in statuses">
				<td>
					<input type="text" class="form-control" ng-model="status.name">
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.salary_production" ng-disabled="status.customable">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" ng-model="status.customable" id="checkboxCustomable@{{ $index }}">
						<label class="custom-control-label" for="checkboxCustomable@{{ $index }}">
							Ручной ввод значения
						</label>
					</div>
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.salary_fixed">
				</td>
				<td ng-style="{'color': status.icon_color}">
					<ui-select ng-model="status.icon" skip-focusser="true" ng-disabled="status.customable">
			            <ui-select-match placeholder="Выберите иконку">
				            <span ng-if="$select.selected == 'name'" ng-bind-html="status.name" style="font-weight: 500;"></span>
				            <span ng-if="$select.selected != 'name'" ng-bind-html="$select.selected"></span>
				        </ui-select-match>
			            <ui-select-choices repeat="statusTemplate in statusTemplates">
			            	<span ng-if="statusTemplate == 'name'">Название</span>
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