<div class="alerts-block top-alerts-block" ng-class="{'shown': showTopAlert}">
	<div class="alert alert-success" role="alert" ng-if="successTopAlert">
		@{{ successTopAlert }} <br>
	</div>
	<div class="alert alert-danger" role="alert" ng-if="errorTopAlert">
		@{{ errorTopAlert }}
	</div>
</div>