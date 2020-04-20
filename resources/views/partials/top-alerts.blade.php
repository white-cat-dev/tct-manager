<div class="alerts-block top-alerts-block" ng-class="{'shown': showAlert}">
	<div class="alert alert-success" role="alert" ng-if="successAlert">
		@{{ successAlert }} <br>
	</div>
	<div class="alert alert-danger" role="alert" ng-if="errorAlert">
		@{{ errorAlert }}
	</div>
</div>