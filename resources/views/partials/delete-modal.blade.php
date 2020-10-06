<div class="modal delete-modal" ng-show="isDeleteModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<i class="far fa-trash-alt"></i> Удаление 
					<span ng-switch on="deleteType">
						<span ng-switch-when="category"> категории</span>
						<span ng-switch-when="facility"> цеха</span>
						<span ng-switch-when="material"> материала</span>
						<span ng-switch-when="order"> заказа</span>
						<span ng-switch-when="product"> продукта</span>
						<span ng-switch-when="recipe"> рецепта</span>
						<span ng-switch-when="worker"> работника</span>
					</span>
				</div>
				<button type="button" class="close" ng-click="hideDelete()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				Вы уверены, что хотите удалить 
				<span ng-switch on="deleteType">
						<span ng-switch-when="category"> категорию</span>
						<span ng-switch-when="facility"> цех</span>
						<span ng-switch-when="material"> материал</span>
						<span ng-switch-when="order"> заказ</span>
						<span ng-switch-when="product"> продукт</span>
						<span ng-switch-when="recipe"> рецепт</span>
						<span ng-switch-when="worker"> работника</span>
					</span>
				«@{{ deleteData.name ? deleteData.name : deleteData.number }}»?
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-success" ng-click="delete(deleteData.id)" ng-if=!isDeleting>
					<i class="fas fa-check"></i> Да
				</button>
				<button type="button" class="btn btn-primary" ng-click="hideDelete()" ng-if=!isDeleting>
					<i class="fas fa-times"></i> Нет
				</button>

				<button type="button" class="btn btn-primary" disabled ng-if="isDeleting">
					<i class="fa fa-spinner fa-spin"></i> Удаление...
				</button>
			</div>
		</div>
	</div>
</div>