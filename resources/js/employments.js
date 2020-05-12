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

	$scope.currentDate = {
		'day': 0,
		'month': 0,
		'year': 0
	};

	$scope.worker = {};
	$scope.workers = [];
	$scope.statuses = {};
	$scope.facilities = {};

	$scope.isSalariesShown = false;


	$scope.init = function()
	{
		var request = {};
		if ($scope.currentDate.year > 0)
		{
			request.year = $scope.currentDate.year;
		}
		if ($scope.currentDate.month > 0)
		{
			request.month = $scope.currentDate.month;
		}

		EmploymentsRepository.get(request, function(response) 
		{
			$scope.days = response.days;
			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDate.day = response.day;
			$scope.currentDate.month = response.month;
			$scope.currentDate.year = response.year;

			$scope.workers = response.workers;
			$scope.statuses = response.statuses;
			$scope.facilities = response.facilities;
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
					if (worker.employments[i].status_id >= 0)
					{
						employments.push(worker.employments[i]);
					}
				}
			}
		}

		var request = {
			'employments': employments,
			'year': $scope.currentDate.year,
			'month': $scope.currentDate.month,
			'day': $scope.currentDate.day
		}

		EmploymentsRepository.save(request, function(response) 
		{
			$scope.successTopAlert = 'Все изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);

			$scope.init();
		});
	};


	$scope.currentEmploymentStatus = null;

	$scope.chooseCurrentEmploymentStatus = function(status)
	{
		$scope.isSalariesShown = false;

		if ($scope.currentEmploymentStatus == status)
		{
			$scope.currentEmploymentStatus = null;
		}
		else
		{	
			$scope.currentEmploymentStatus = status;
		}

		$scope.currentFacility = null;
		$scope.cleanCurrent = false;
	}


	$scope.currentFacility = null;

	$scope.chooseCurrentFacility = function(facility)
	{
		$scope.isSalariesShown = false;

		if ($scope.currentFacility == facility)
		{
			$scope.currentFacility = null;
		}
		else
		{	
			$scope.currentFacility = facility;
		}

		$scope.currentEmploymentStatus = null;
		$scope.cleanCurrent = false;
	}


	$scope.cleanCurrent = false;

	$scope.chooseCleanCurrent = function()
	{
		$scope.isSalariesShown = false;

		if ($scope.cleanCurrent)
		{
			$scope.cleanCurrent = false;
		}
		else
		{
			$scope.currentFacility = null;
			$scope.currentEmploymentStatus = null;
			$scope.cleanCurrent = true;
		}
	}


	$scope.changeEmploymentStatus = function(worker, day)
	{
		if ($scope.isSalariesShown) 
		{
			return;
		}

		if (!worker.employments)
		{
			worker.employments = {};
		}

		if ($scope.cleanCurrent)
		{
			delete worker.employments[day]; 
			return;
		}

		if (!worker.employments[day])
		{
			var statusId = ($scope.currentEmploymentStatus !== null) ? $scope.currentEmploymentStatus : -1;
			var facilityId = ($scope.currentFacility !== null) ? $scope.currentFacility : 0;

			worker.employments[day] = {
				'worker_id': worker.id,
				'day': day,
				'status_id':  statusId,
				'facility_id': facilityId,
				'salary': 0
			};
		}
		else
		{
			var statusId = worker.employments[day].status_id;
			var facilityId = worker.employments[day].facility_id;

			if ($scope.currentEmploymentStatus !== null)
			{
				statusId = $scope.currentEmploymentStatus;
			}

			if ($scope.currentFacility !== null)
			{
				facilityId = $scope.currentFacility;
			}
			worker.employments[day].status_id = statusId;
			worker.employments[day].facility_id = facilityId;
		}
	};



	$scope.isSalaryModalShown = false;
	$scope.modalWorker = {};


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
			$scope.successTopAlert = 'Все изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$scope.isSalaryModalShown = false;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);

			$scope.init();
		});
	}
}]);