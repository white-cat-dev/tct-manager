angular.module('tctApp').controller('EmploymentsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'EmploymentsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		EmploymentsRepository
	){

	$scope.Object = Object;

	$scope.days = 0;
	$scope.monthes = [];
	$scope.years = [];

	$scope.currentDay = 0;
	$scope.currentMonth = 0;
	$scope.currentYear = 0;

	$scope.worker = {};
	$scope.workers = [];
	$scope.statuses = {};


	$scope.init = function()
	{
		var request = {};
		if ($scope.currentYear > 0)
		{
			request.year = $scope.currentYear;
		}
		if ($scope.currentMonth > 0)
		{
			request.month = $scope.currentMonth;
		}

		EmploymentsRepository.get(request, function(response) 
		{
			$scope.days = response.days;
			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDay = response.day;
			$scope.currentMonth = response.month;
			$scope.currentYear = response.year;

			$scope.workers = response.workers;
			$scope.statuses = response.statuses;
		});
	};


	$scope.save = function()
	{
		var employments = [];
		for (worker of $scope.workers)
		{
			if (worker.employments)
			{
				for (i in worker.employments)
				{
					employments.push(worker.employments[i]);
				}
			}
		}

		var request = {
			'employments': employments
		};
		if ($scope.currentYear > 0)
		{
			request.year = $scope.currentYear;
		}
		if ($scope.currentMonth > 0)
		{
			request.month = $scope.currentMonth;
		}

		EmploymentsRepository.save(request, function(response) 
		{
			$scope.successAlert = 'Все изменения успешно сохранены!';
			$scope.showAlert = true;

			$timeout(function() {
				$scope.showAlert = false;
			}, 2000);

			$scope.init();
		});
	};


	$scope.$watch('currentYear', function(newValue, oldValue) 
	{
		$scope.init();
	});

	$scope.$watch('currentMonth', function(newValue, oldValue) 
	{
		$scope.init();
	});


	$scope.changeEmploymentStatus = function(worker, day)
	{
		$scope.changes = true;

		if (!worker.employments)
		{
			worker.employments = {};
		}

		var statusesKeys = Object.keys($scope.statuses);

		if (!worker.employments[day])
		{
			worker.employments[day] = {
				'worker_id': worker.id,
				'day': day,
				'status_id': statusesKeys.shift()
			};
		}
		else
		{
			var statusId = worker.employments[day].status_id;
			var newStatusId = statusId;
			if (statusId == 0)
			{
				newStatusId = statusesKeys.shift();
			}
			else
			{
				var newStatusId = statusesKeys.indexOf(statusId) + 1;
				if (newStatusId >= statusesKeys.length)
				{
					newStatusId = 0
				}
				else
				{
					newStatusId = statusesKeys[newStatusId];
				}
			}
			worker.employments[day].status_id = newStatusId;
		}
	};



	$scope.isSalaryModalShown = false;


	$scope.showSalaryModal = function(worker)
	{
		$scope.modalWorker = worker;
		$scope.isSalaryModalShown = true;
	}


	$scope.hideSalaryModal = function()
	{
		$scope.isSalaryModalShown = false;
	}


	$scope.saveSalary = function()
	{
		EmploymentsRepository.saveSalary({id: $scope.modalWorker.salary.id}, $scope.modalWorker.salary, function(response) 
		{
			$scope.successAlert = 'Все изменения успешно сохранены!';
			$scope.showAlert = true;

			$scope.isSalaryModalShown = false;

			$timeout(function() {
				$scope.showAlert = false;
			}, 2000);

			$scope.init();
		});
	}
}]);