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

	$scope.Math = window.Math;

	$scope.Object = Object;

	$scope.days = 0;
	$scope.monthes = [];
	$scope.years = [];

	$scope.currentDate = {};

	$scope.worker = {};
	$scope.workers = [];
	$scope.manager = [];
	$scope.statuses = {};
	$scope.employments = {};

	$scope.dayEmployments = {};
	$scope.totalSalary = {};

	$scope.isSalariesShown = false;


	$scope.init = function()
	{
		$scope.isLoading = true;

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
			$scope.employments = response.employments;

			if (Object.keys($scope.statuses).length > 0)
			{
				$scope.chooseCurrentEmploymentStatus(Object.keys($scope.statuses)[0]);
			}

			$scope.updateTotalSalary();
			$scope.updateTotalEmployment();

			$scope.initScroll();
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

		$scope.isSaving = true;
		EmploymentsRepository.save(request, function(response) 
		{
			$scope.isSaving = false;

			toastr.success('Все изменения успешно сохранены!');

			$scope.init();
		}, 
		function(response) 
		{
            $scope.isSaving = false;
            toastr.error('Произошла ошибка на сервере');
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
			worker.employments[day].status_id = 0;
			worker.employments[day].main_category = '';
			worker.employments[day].status_custom = 1;
			// delete worker.employments[day]; 
			return;
		}

		if (!worker.employments[day])
		{
			var statusId = ($scope.currentEmploymentStatus !== null) ? $scope.currentEmploymentStatus : -1;
			var mainCategory = ($scope.currentMainCategory !== null) ? $scope.currentMainCategory : 0;
			var statusCustom = 0; 

			if (statusId)
			{
				if ($scope.statuses[statusId].customable > 0)
				{
					statusCustom = (worker.id > 0) ? 1 : 9;
					mainCategory = 'tiles';
				}
			}

			worker.employments[day] = {
				'worker_id': worker.id,
				'day': day,
				'status_id': statusId,
				'status_custom': statusCustom, 
				'main_category': mainCategory,
				'salary': 0
			};
		}
		else
		{
			var statusId = worker.employments[day].status_id;
			var mainCategory = worker.employments[day].main_category;
			var statusCustom = worker.employments[day].status_custom; 

			if ($scope.currentEmploymentStatus !== null)
			{
				statusId = $scope.currentEmploymentStatus;

				if ($scope.statuses[$scope.currentEmploymentStatus].customable > 0)
				{
					statusCustom = (worker.id > 0) ? 1 : 9;
					// mainCategory = 'tiles';
				}
			}

			if ($scope.currentMainCategory !== null)
			{
				mainCategory = $scope.currentMainCategory;
			}

			worker.employments[day].status_id = statusId;
			worker.employments[day].main_category = mainCategory;
			worker.employments[day].status_custom = statusCustom;
		}

		$scope.updateTotalEmployment(worker);
	};



	$scope.isSalaryModalShown = false;
	$scope.modalWorker = {};


	$scope.showSalaryModal = function(worker)
	{
		$scope.modalWorker = angular.copy(worker);
		$scope.isSalaryModalShown = true;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideSalaryModal = function()
	{
		$scope.isSalaryModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
	}


	$scope.saveSalary = function()
	{
		EmploymentsRepository.saveSalary({id: $scope.modalWorker.salary.id}, $scope.modalWorker.salary, function(response) 
		{
			toastr.success('Все изменения успешно сохранены!');

			$scope.hideSalaryModal();

			$scope.init();
		});
	}


	$scope.isEmploymentModalShown = false;

	$scope.showEmploymentModal = function(day)
	{
		$scope.modalEmployment = $scope.employments[day];
		$scope.modalDate = new Date($scope.currentDate.year, $scope.currentDate.month - 1, day);
		$scope.modalDay = day;
		$scope.isEmploymentModalShown = true;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideEmploymentModal = function()
	{
		$scope.isEmploymentModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
	}



	$scope.updateTotalSalary = function()
	{
		$scope.totalSalary = {
			'employments': 0,
			'advance': 0, 
			'tax': 0, 
			'lunch': 0, 
			'bonus': 0, 
			'surcharge': 0
		};

		var workers = angular.copy($scope.workers);
		workers.push($scope.manager);

		for (var i = 0; i < workers.length; i++)
		{
			for (key in $scope.totalSalary)
			{
				$scope.totalSalary[key] += workers[i].salary[key];
			}
		}
	}


	$scope.updateTotalEmployment = function(worker) 
	{
		var workers = worker ? [worker] : $scope.workers;

		for (var i = 0; i < workers.length; i++)
		{
			workers[i].totalEmployment = 0;

			for (key in workers[i].employments)
			{
				var status = $scope.statuses[workers[i].employments[key].status_id];
				if (!status)
				{
					continue;
				}
				if (status.customable)
				{
					workers[i].totalEmployment += +workers[i].employments[key].status_custom;
				}
				else
				{
					workers[i].totalEmployment += status.salary_production;
				}
			}

			workers[i].totalEmployment = Math.round(workers[i].totalEmployment * 100) / 100;
		}

		if (!worker)
		{
			$scope.manager.totalEmployment = 0;

			for (key in $scope.manager.employments)
			{
				var status = $scope.statuses[$scope.manager.employments[key].status_id];
				if (!status)
				{
					continue;
				}
				if (status.customable)
				{
					$scope.manager.totalEmployment += +$scope.manager.employments[key].status_custom;
				}
				else
				{
					$scope.manager.totalEmployment += status.salary_production;
				}
			}

			$scope.manager.totalEmployment = Math.round($scope.manager.totalEmployment * 100) / 100;
		}
	}


	$scope.initScroll = function()
	{
		setTimeout(function()
		{
			var employmentBlock = document.querySelector('.employment-block');
			var mainBlock = employmentBlock.querySelector('.employments-block-content');
			var leftBlock = employmentBlock.querySelector('.workers-block-content');
			var topBlock = employmentBlock.querySelector('.employments-block-top-table > div');

			var scrollLeft = mainBlock.querySelector('.table').clientWidth / $scope.days * ($scope.currentDate.day - 1);
			scrollLeft = scrollLeft - mainBlock.clientWidth / 2 + 20;
			if (scrollLeft < 0)
			{
				scrollLeft = 0;
			}

			mainBlock.scrollLeft = scrollLeft;

			mainBlock.focus();

			mainBlock.addEventListener('scroll', function(event) 
			{
				var scrollTop = mainBlock.scrollTop;
				var scrollLeft = mainBlock.scrollLeft;

				leftBlock.scrollTop = scrollTop;
				topBlock.scrollLeft = scrollLeft;
			});

			$scope.isLoading = false;
			$scope.$apply();
		}, 100);
	}
}]);