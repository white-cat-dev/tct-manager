angular.module('tctApp').controller('FacilitiesController', [
	'$scope',
	'$routeParams',
	'$location',
	'$filter',
	'$timeout',
	'FacilitiesRepository',
	'CategoriesRepository',
	'WorkersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$filter,
		$timeout,
		FacilitiesRepository,
		CategoriesRepository,
		WorkersRepository
	){

	$scope.baseUrl = '';

	$scope.facilities = [];

	$scope.facility = {
		'url': '#',
		'workers': []
	};
	$scope.id = 0;

	$scope.facilityErrors = {};

	$scope.categories = [];
	$scope.workers = [];


	$scope.init = function()
	{
		$scope.isLoading = true;
		FacilitiesRepository.query(function(response) 
		{
			$scope.isLoading = false;
			$scope.facilities = response;
			for (var facility of $scope.facilities) 
			{
				facility.status_date_raw = facility.status_date ? new Date(Date.parse(facility.status_date.replace(/-/, '/'))) : null;
			}
		},
		function(response)
		{
			$scope.isLoading = false;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'facilities';

		$scope.id = $routeParams['id'];

		$scope.isLoading = true;
		FacilitiesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.isLoading = false;
			$scope.facility = response;
			$scope.facility.status_date_raw = $scope.facility.status_date ? new Date(Date.parse($scope.facility.status_date.replace(/-/, '/'))) : null;
		},
		function(response)
		{
			$scope.isLoading = false;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'facilities';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.isLoading = true;
			FacilitiesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.isLoading = false;
				$scope.facility = response;
				$scope.facility.status_date_raw = $scope.facility.status_date ? new Date(Date.parse($scope.facility.status_date.replace(/-/, '/'))) : null;

				var categories = $scope.facility.categories;
				$scope.facility.categories = {};
				for (var category of categories)
				{
					$scope.facility.categories[category.id] = true;
				}
			},
			function(response)
			{
				$scope.isLoading = false;
			});
		}

		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});

		WorkersRepository.query(function(response) 
		{
			$scope.workers = response;

			for (var i = 0; i < $scope.workers.length; i++)
			{
				if (($scope.workers[i].facility_id > 0) && 
					($scope.workers[i].facility_id == $scope.id))
				{
					$scope.workers.splice(i, 1);
				}
			}
		});
	}


	$scope.save = function(facility) 
	{
		facility = facility || $scope.facility;

		$scope.isSaving = true;

		FacilitiesRepository.save({id: facility.id}, facility, function(response) 
		{
			$scope.isSaving = false;

			toastr.success($scope.id ? 'Цех успешно обновлена!' : 'Новый цех успешно создан!');

			$scope.facilityErrors = {};
			$scope.id = response.id;
			$scope.facility.url = response.url;
		}, 
		function(response) 
		{
            $scope.isSaving = false;

            switch (response.status) 
            {
            	case 422:
            		$scope.facilityErrors = response.data.errors;
            		break
            }
        });
	}


	$scope.showDelete = function(facility)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'facility';
		$scope.deleteData = facility;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideDelete = function()
	{
		$scope.isDeleteModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
	}


	$scope.delete = function(id)
	{
		$scope.isDeleting = true;

		FacilitiesRepository.delete({id: id}, function(response) 
		{
			$scope.isDeleting = false;

			$scope.hideDelete();

			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				toastr.success('Категория успешно удалена!');

				$scope.init();
			}
		}, 
		function(response) 
		{
        	$scope.isDeleting = false;
        });
	}



	$scope.isAddWorkerShown = false;
	$scope.newWorker = {};
	

	$scope.showAddWorker = function()
	{
		$scope.isAddWorkerShown = true;
	}
	

	$scope.addWorker = function()
	{
		var index = $scope.workers.indexOf($scope.newWorker.data);
		$scope.facility.workers.push($scope.newWorker.data);
		$scope.workers.splice(index, 1);
		$scope.newWorker.data = null;
		$scope.isAddWorkerShown = false;
	}


	$scope.deleteWorker = function(index)
	{
		var worker = $scope.facility.workers[index];
		$scope.facility.workers.splice(index, 1);
		$scope.workers.push(worker);
	}



	$scope.isStatusModalShown = false;
	$scope.modalStatusErrors = {};
	$scope.modalFacility = {};


	$scope.showStatusModal = function(facility, status)
	{
		$scope.facility = facility || $scope.facility;
		$scope.modalFacility = angular.copy($scope.facility);
		$scope.modalFacility.status = status;

		$scope.modalFacility.status_date_raw = new Date();
		
		if ($scope.facility.status == $scope.modalFacility.status)
		{
			$scope.saveStatus();
			return;
		}

		$scope.isStatusModalShown = true;
	}


	$scope.hideStatusModal = function()
	{
		$scope.isStatusModalShown = false;

		$scope.modalStatusErrors = {};
	}



	// $scope.updateStatusNow = function()
	// {
	// 	$scope.facility.status = ($scope.facility.status + 2) % 4;
	// 	$scope.facility.status_date_raw = new Date();
	// }


	$scope.saveStatus = function()
	{
		if ($scope.facility.status == $scope.modalFacility.status)
		{
			$scope.modalFacility.status_date_raw = null;
		}
		else if ($scope.modalFacility.status_date_raw)
		{
			$scope.modalFacility.status_date_raw.setHours(12);
			$scope.modalFacility.status = ($scope.modalFacility.status + 1) % 2;
		}

		$scope.isModalSaving = true;

		FacilitiesRepository.save({id: $scope.modalFacility.id}, $scope.modalFacility, function(response) 
		{
			$scope.isModalSaving = false;

			$scope.hideStatusModal();

			toastr.success('Изменения успешно сохранены!');

			if (!$scope.baseUrl)
			{
				$scope.init();
			}
			else
			{
				$scope.initShow();
			}

		}, 
		function(response) 
		{
            $scope.isModalSaving = false;

            switch (response.status) 
            {
            	case 422:
            		$scope.modalStatusErrors = response.data.errors;
            		break;
        	}
        });
	}
}]);