angular.module('tctApp').controller('CategoriesController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'CategoriesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		CategoriesRepository
	){

	$scope.baseUrl = '';

	$scope.categories = [];
	$scope.category = {
		'url': '#'
	};
	$scope.id = 0;

	$scope.categoryErrors = {};

	$scope.units = [
		{
			'key': 'area',
			'name': 'Площадь (м<sup>2</sup>)'
		},
		{
			'key': 'volume',
			'name': 'Объём (м<sup>3</sup>)'
		},
		{
			'key': 'unit',
			'name': 'Поштучно (шт.)'
		}
	];


	$scope.init = function()
	{
		$scope.isLoading = true;
		CategoriesRepository.query(function(response) 
		{
			$scope.isLoading = false;
			$scope.categories = response;
		},
		function(response)
		{
			$scope.isLoading = false;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'categories';

		$scope.id = $routeParams['id'];

		$scope.isLoading = true;
		CategoriesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.isLoading = false;
			$scope.category = response;
		},
		function(response)
		{
			$scope.isLoading = false;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'categories';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.isLoading = true;
			CategoriesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.isLoading = false;
				$scope.category = response;
			},
			function(response)
			{
				$scope.isLoading = false;
			});
		}
	}


	$scope.save = function() 
	{
		$scope.isSaving = true;
		CategoriesRepository.save({id: $scope.id}, $scope.category, function(response) 
		{
			$scope.isSaving = false;
			toastr.success($scope.id ? 'Категория успешно обновлена!' : 'Новая категория успешно создана!');

			$scope.categoryErrors = {};
			$scope.id = response.id;
			$scope.category.url = response.url;
		}, 
		function(response) 
		{
            $scope.isSaving = false;
            switch (response.status) 
            {
            	case 422:
            		$scope.categoryErrors = response.data.errors;
            		break;
            }
        });
	}


	$scope.showDelete = function(category)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'category';
		$scope.deleteData = category;

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
		
		CategoriesRepository.delete({id: id}, function(response) 
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
}]);