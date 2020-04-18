<div class="statuses-page" ng-init="init()">
	<h1>Статусы</h1>

	<div class="top-buttons-block">
		<div class="left-buttons">
			<a href="{{ route('workers') }}" class="btn btn-primary">
				<i class="fas fa-chevron-left"></i> Вернуться к списку работников
			</a>
		</div>
	</div>

	<div class="alerts-block" ng-class="{'shown': showAlert}">
		<div class="alert alert-success" role="alert" ng-if="successAlert">
			@{{ successAlert }} <br>
			Вы можете <a href="{{ route('workers') }}" class="btn-link">перейти к списку работников</a>
		</div>
		<div class="alert alert-danger" role="alert" ng-if="errorAlert">
			@{{ errorAlert }}
		</div>
	</div>

	<div class="edit-form-block">
		<table class="table">
			<tr>
				<th>Иконка</th>
				<th>Цвет иконки</th>
				<th>Название</th>
				<th>Влияние на зарплату</th>
				<th></th>
			</tr>

			<tr ng-repeat="status in statuses">
				<td ng-style="{'color': status.icon_color}">
					<ui-select theme="bootstrap" ng-model="status.icon">
			            <ui-select-match placeholder="Выберите иконку">
				            <span ng-bind-html="$select.selected"></span>
				        </ui-select-match>
			            <ui-select-choices repeat="statusTemplate in statusTemplates">
			                <span ng-bind-html="statusTemplate"></span>
			            </ui-select-choices>
					</ui-select>
				</td>
				<td>
					<color-picker ng-model="status.icon_color">
					</color-picker>
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.name">
				</td>
				<td>
					<input type="text" class="form-control" ng-model="status.salary">
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
			<button class="btn btn-primary" ng-click="save()">
				<i class="fas fa-save"></i> Сохранить
			</button>
		</div>
	</div>
</div>