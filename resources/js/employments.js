angular.module('tctApp').controller('EmploymentsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'toastr',
	'EmploymentsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		toastr,
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
	$scope.manager = [];
	$scope.statuses = {};

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
			$scope.manager = response.manager;
			$scope.statuses = response.statuses;

			if (Object.keys($scope.statuses).length > 0)
			{
				$scope.chooseCurrentEmploymentStatus(Object.keys($scope.statuses)[0]);
			}
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

		for (i in $scope.manager.employments)
		{
			if ($scope.manager.employments[i].status_id >= 0)
			{
				employments.push($scope.manager.employments[i]);
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
			toastr.success('Все изменения успешно сохранены!');

			$scope.init();
		});
	};



	$scope.mainCategories = {
		'tiles': {
			'key': 'tiles',
			'name': 'Плитка',
			'icon_color': '#55d98c'
		},
		'blocks': {
			'key': 'blocks',
			'name': 'Блоки',
			'icon_color': '#5faee3'
		}
	};


	$scope.currentEmploymentStatus = null;

	$scope.chooseCurrentEmploymentStatus = function(status)
	{
		$scope.isSalariesShown = false;

		$scope.currentEmploymentStatus = ($scope.currentEmploymentStatus != status) ? status : null;

		$scope.currentMainCategory = null;
		$scope.cleanCurrent = false;
	}


	$scope.currentMainCategory = null;

	$scope.chooseCurrentMainCategory = function(category)
	{
		$scope.isSalariesShown = false;

		$scope.currentMainCategory = ($scope.currentMainCategory != category) ? category : null;

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
			$scope.currentMainCategory = null;
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
			var mainCategory = ($scope.currentMainCategory !== null) ? $scope.currentMainCategory : 0;

			worker.employments[day] = {
				'worker_id': worker.id,
				'day': day,
				'status_id': statusId,
				'status_custom': 0, 
				'main_category': mainCategory,
				'salary': 0
			};
		}
		else
		{
			var statusId = worker.employments[day].status_id;
			var mainCategory = worker.employments[day].main_category;

			if ($scope.currentEmploymentStatus !== null)
			{
				statusId = $scope.currentEmploymentStatus;
			}

			if ($scope.currentMainCategory !== null)
			{
				mainCategory = $scope.currentMainCategory;
			}
			worker.employments[day].status_id = statusId;
			worker.employments[day].main_category = mainCategory;
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
			toastr.success('Все изменения успешно сохранены!');

			$scope.isSalaryModalShown = false;

			$scope.init();
		});
	}
}]);