<div class="modal copy-modal" ng-show="isCopyModalShown">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<i class="far fa-trash-alt"></i> Создание копии 
					<span ng-switch on="copyType">
						<span ng-switch-when="material"> материала</span>
						<span ng-switch-when="product"> продукта</span>
						<span ng-switch-when="recipe"> рецепта</span>
					</span>
				</div>
				<button type="button" class="close" ng-click="hideCopy()">
					<i class="fas fa-times"></i>
				</button>
			</div>

			<div class="modal-body">
				Вы уверены, что хотите создать копию 
				<span ng-switch on="copyType">
						<span ng-switch-when="material"> материала</span>
						<span ng-switch-when="product"> продукта</span>
						<span ng-switch-when="recipe"> рецепта</span>
					</span>
				«@{{ copyData.name ? copyData.name : copyData.number }}»?
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-success" ng-click="copy(copyData.id)" ng-if=!isCopying>
					<i class="fas fa-check"></i> Да
				</button>
				<button type="button" class="btn btn-primary" ng-click="hideCopy()" ng-if=!isCopying>
					<i class="fas fa-times"></i> Нет
				</button>

				<button type="button" class="btn btn-primary" disabled ng-if=isCopying>
					<i class="fa fa-spinner fa-spin"></i> Создание копии...
				</button>
			</div>
		</div>
	</div>
</div>