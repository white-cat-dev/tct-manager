angular.module('tctApp').controller('WorkersController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'EmploymentsRepository',
	'FacilitiesRepository',
	'WorkersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		EmploymentsRepository,
		FacilitiesRepository,
		WorkersRepository
	){

	$scope.Object = Object;

	$scope.days = 0;
	$scope.monthes = {};
	$scope.years = {};

	$scope.currentDay = 0;
	$scope.currentMonth = 0;
	$scope.currentYear = 0;

	$scope.worker = {};
	$scope.employmentWorkers = [];
	$scope.statuses = {};

	$scope.facilities = [];
	$scope.workers = [];


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

			$scope.employmentWorkers = response.workers;
			$scope.statuses = response.statuses;
		});

		FacilitiesRepository.query(request, function(response) 
		{
			$scope.facilities = response;
		});

		WorkersRepository.query(request, function(response)
		{
			$scope.workers = response;
		});
	}


	$scope.changeEmploymentStatus = function(worker, day)
	{
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
	}


	$scope.saveEmployments = function()
	{
		var employments = [];
		for (worker of $scope.employmentWorkers)
		{
			if (worker.employments)
			{
				for (i in worker.employments)
				{
					employments.push(worker.employments[i]);
				}
			}
		}

		EmploymentsRepository.save({'employments': employments}, function(response) 
		{
			$scope.successAlert = 'Все изменения успешно сохранены!';
			$scope.showAlert = true;

			$timeout(function() {
				$scope.showAlert = false;
			}, 2000);
		});
	}


	$scope.initEdit = function()
	{
		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			WorkersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.worker = response;
			});
		}

		FacilitiesRepository.query(function(response) 
		{
			$scope.facilities = response;
		});
	}


	$scope.save = function() 
	{
		WorkersRepository.save({id: $scope.id}, $scope.worker, function(response) 
		{
			$scope.workerErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Данные работника успешно сохранены!';
			}
			else
			{
				$scope.successAlert = 'Новый работник успешно создан!';
			}
			$scope.id = response.id;
			$scope.worker.url = response.url;
		}, 
		function(response) 
		{
            $scope.workerErrors = response.data.errors;
        });
	}


	$scope.deleteWorker = function(id)
	{
		WorkersRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}


	$scope.deleteFacility = function(id)
	{
		FacilitiesRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}
}]);