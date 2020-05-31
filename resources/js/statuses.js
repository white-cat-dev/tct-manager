angular.module('tctApp').controller('EmploymentStatusesController', [
	'$scope',
	'$routeParams',
	'$location',
	'toastr',
	'EmploymentStatusesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		toastr,
		EmploymentStatusesRepository
	){


	$scope.statusTemplates = [
		'name',
		'<i class="fas fa-check"></i>',
		'<i class="fas fa-times"></i>',
		'<i class="fas fa-question"></i>'
	];


	$scope.init = function()
	{
		EmploymentStatusesRepository.query(function(response) 
		{
			$scope.statuses = response;
		});
	}


	$scope.addStatus = function()
	{
		$scope.statuses.push({
			'icon': '',
			'icon_color': '#888888',
			'name': '',
			'salary_production': 0,
			'salary_fixed': 0,
			'salary_team': 0
		});
	}


	$scope.deleteStatus = function(index)
	{
		$scope.statuses.splice(index, 1);
	}


	$scope.save = function()
	{
		EmploymentStatusesRepository.save({'statuses': $scope.statuses}, function(response) 
		{
			toastr.success('Статусы успешно сохранены!');
		});
	}
}]);