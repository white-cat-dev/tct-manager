angular.module('tctApp').controller('WorkersController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'FacilitiesRepository',
	'WorkersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		FacilitiesRepository,
		WorkersRepository
	){

	$scope.Object = Object;

	$scope.baseUrl = '';

	$scope.worker = {
		'url': '#'
	};

	$scope.facilities = [];
	$scope.workers = [];


	$scope.init = function()
	{
		$scope.isLoading = true;
		WorkersRepository.query(function(response)
		{
			$scope.isLoading = false;
			$scope.workers = response;
		},
		function(response) 
		{
			$scope.isLoading = false;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'workers';

		$scope.id = $routeParams['id'];

		$scope.isLoading = true;
		WorkersRepository.get({id: $scope.id}, function(response) 
		{
			$scope.isLoading = false;
			$scope.worker = response;
		},
		function(response) 
		{
			$scope.isLoading = false;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'workers';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.isLoading = true;
			WorkersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.isLoading = false;
				$scope.worker = response;
				if ($scope.worker.birthdate)
				{
					var birthdate = $scope.worker.birthdate.split("-");
					$scope.worker.birthdate_raw = birthdate[2] + birthdate[1] + birthdate[0];
				}
			},
			function(response) 
			{
				$scope.isLoading = false;
			});
		}

		FacilitiesRepository.query(function(response) 
		{
			$scope.facilities = response;
		});
	}


	$scope.save = function() 
	{
		$scope.isSaving = true;

		WorkersRepository.save({id: $scope.id}, $scope.worker, function(response) 
		{
			$scope.isSaving = false;

			toastr.success($scope.id ? 'Работник успешно обновлен!' : 'Новый работник успешно создан!');

			$scope.workerErrors = {};
			$scope.id = response.id;
			$scope.worker.url = response.url;
		}, 
		function(response) 
		{
            $scope.isSaving = false;

            switch (response.status) 
            {
            	case 422:
            		$scope.workerErrors = response.data.errors;
            		break
            }
        });
	}


	$scope.showDelete = function(worker)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'worker';
		$scope.deleteData = worker;

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

		WorkersRepository.delete({id: id}, function(response) 
		{
			$scope.isDeleting = false;

			$scope.hideDelete();

			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				toastr.success('Работник успешно удален!');

				$scope.init();
			}
		}, 
		function(response) 
		{
        	$scope.isDeleting = false;
        });
	}


	$scope.isStatusModalShown = false;
	$scope.modalStatusErrors = {};
	$scope.modalWorker = {};


	$scope.showStatusModal = function(worker, status)
	{
		$scope.worker = worker || $scope.worker;
		$scope.modalWorker = angular.copy($scope.worker);
		$scope.modalWorker.status = status;

		$scope.modalWorker.status_date_raw = new Date();
		
		if ($scope.worker.status == $scope.modalWorker.status)
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


	$scope.saveStatus = function()
	{
		if ($scope.worker.status == $scope.modalWorker.status)
		{
			$scope.modalWorker.status_date_raw = null;
		}
		else if ($scope.modalWorker.status_date_raw)
		{
			$scope.modalWorker.status_date_raw.setHours(12);
			$scope.modalWorker.status = ($scope.modalWorker.status + 1) % 2;
		}
		
		$scope.isModalSaving = true;

		WorkersRepository.save({id: $scope.modalWorker.id}, $scope.modalWorker, function(response) 
		{
			$scope.isModalSaving = false;

			$scope.modalStatusErrors = {};
			toastr.success('Изменения успешно сохранены!');

			$scope.hideStatusModal();

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