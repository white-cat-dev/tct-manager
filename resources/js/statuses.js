angular.module('tctApp').controller('EmploymentStatusesController', [
	'$scope',
	'$routeParams',
	'$location',
	'EmploymentStatusesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		EmploymentStatusesRepository
	){


	$scope.statusTemplates = [
		'<i class="fas fa-check"></i>',
		'<i class="fas fa-times"></i>',
		'<i class="fas fa-question"></i>',
		'<i class="fas fa-user"></i>',
		'<i class="fas fa-user-tie"></i>',
		'<i class="fas fa-coffee"></i>'
	];


	$scope.init = function()
	{
		EmploymentStatusesRepository.query(function(response) 
		{
			$scope.statuses = response;

			for (key in $scope.statuses) 
			{
				$scope.statuses[key].customable = Boolean($scope.statuses[key].customable);
			}
		},
		function(response)
		{

		});
	}


	$scope.addStatus = function()
	{
		$scope.statuses.push({
			'icon': '',
			'icon_color': '#888888',
			'name': '',
			'type': 'fixed',
			'base_salary': 0,
			'salary': 0,
			'customable': false,
			'salary_default': 0
		});
	}


	$scope.deleteStatus = function(index)
	{
		$scope.statuses.splice(index, 1);
	}


	$scope.save = function()
	{
		$scope.isSaving = true;

		EmploymentStatusesRepository.save({'statuses': $scope.statuses}, function(response) 
		{
			$scope.isSaving = false;
			toastr.success('Статусы успешно сохранены!');
		}, 
		function(response)
		{
			scope.isSaving = false;
		});
	}
}]);