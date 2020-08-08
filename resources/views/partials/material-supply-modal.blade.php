<div class="modal supply-modal" ng-show="isSupplyModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					Поступление материалов
				</div>
				<button type="button" class="close" ng-click="hideSupplyModal()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="param-label">Дата</div>
					<input type="text" class="form-control" ng-model="modalSupply.date_raw" ui-mask="99.99.9999">
				</div>

				<table class="table table-with-buttons" ng-if="modalSupply.supplies.length > 0">
					<tr>
						<th>Материал</th>
						<th>Количество</th>
						<th></th>
					</tr>

					<tr ng-repeat="supply in modalSupply.supplies" ng-if="!realization.date">
						<td style="width: 60%;">
							<ui-select ng-model="supply.material_id" skip-focusser="true" ng-if="!isSupplyModalEditing">
					            <ui-select-match placeholder="Выберите материал...">
						            @{{ $select.selected.material_group.name }} @{{ $select.selected.variation_text }}
						        </ui-select-match>
					            <ui-select-choices repeat="material.id as material in materials | filter: $select.search">
					                <span ng-bind-html="material.material_group.name + ' ' + material.variation_text | highlight: $select.search"></span>
					            </ui-select-choices>
							</ui-select>
							<span ng-if="isSupplyModalEditing">
								@{{ supply.material.material_group.name }} @{{ supply.material.variation_text }}
							</span>
						</td>
						<td style="width: 40%;">
							<input type="text" class="form-control form-control-sm" ng-model="supply.performed" ng-change="inputFloat(supply, 'performed')">
						</td>
						<td ng-if="!isSupplyModalEditing">
							<button type="button" class="btn btn-sm btn-primary" ng-click="deleteSupplyMaterial($index)">
								<i class="far fa-trash-alt"></i>
							</button>
						</td>
					</tr>
				</table>

				<button type="button" class="btn btn-sm btn-primary" ng-click="addSupplyMaterial()" ng-if="!isSupplyModalEditing">
					<i class="fas fa-plus"></i> Добавить материал	
				</button>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="saveSupply()">
					<i class="fas fa-save"></i> Сохранить
				</button>
			</div>
		</div>
	</div>
</div>