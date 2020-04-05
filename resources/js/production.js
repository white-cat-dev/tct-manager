angular.module('tctApp').controller('ProductionController', [
	'$scope',
	'$routeParams',
	'$location',
	'ProductionRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		ProductionRepository
	){

	$scope.days = 0;
	$scope.currentDay = 0;
	$scope.currentMonth = 0;
	$scope.currentYear = 0;
	$scope.products = [];

	$scope.monthes = {
		1: 'Январь',
		2: 'Февраль',
		3: 'Март',
		4: 'Апрель',
		5: 'Май',
		6: 'Июнь',
		7: 'Июль',
		8: 'Август',
		9: 'Сентябрь',
		10: 'Октябрь',
		11: 'Ноябрь',
		12: 'Декабрь'
	};

	$scope.init = function()
	{
		ProductionRepository.products(function(response) 
		{
			$scope.days = response.days;
			$scope.currentDay = response.day;
			$scope.currentMonth = response.month;
			$scope.currentYear = response.year;
			$scope.productionProducts = response.products;
		});
	}


	$scope.isModalShown = false;
	$scope.modalDate = {};

	$scope.showModal = function(currentDay)
	{
		$scope.isModalShown = true;

		var modalData = {
			'year': $scope.currentYear,
			'month': $scope.currentMonth,
			'day': currentDay
		}

		ProductionRepository.get(function(response) 
		{
			$scope.days = response.days;
			$scope.currentDay = response.day;
			$scope.currentMonth = response.month;
			$scope.currentYear = response.year;
			$scope.productionProducts = response.products;
		});
	}

	$scope.hideModal = function()
	{
		$scope.isModalShown = false;
	}
}]);