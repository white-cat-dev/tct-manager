angular.module('tctApp').controller('ProductsController', [
	'$scope',
	'$routeParams',
	'$location',
	'CategoriesRepository',
	'ProductsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		CategoriesRepository,
		ProductsRepository
	){

	$scope.productGroups = [];
	$scope.productGroup = {
		'products': []
	};
	$scope.id = 0;

	$scope.productGroupErrors = {};

	$scope.categories = [];
	$scope.currentCategory = 0;

	$scope.colors = [
		{
			'key': 'grey',
			'name': 'Серый'
		},
		{
			'key': 'red',
			'name': 'Красный'
		},
		{
			'key': 'yellow',
			'name': 'Желтый'
		}
	];


	$scope.init = function()
	{
		if ($location.search().category)
		{
			$scope.currentCategory = $location.search().category;
		}
		
		$scope.loadCategories();
		$scope.loadProducts();
	}


	$scope.loadCategories = function()
	{
		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});
	}


	$scope.loadProducts = function()
	{
		ProductsRepository.query({'category': $scope.currentCategory}, function(response) 
		{
			$scope.productGroups = response;
		});
	}

	$scope.chooseCategory = function(category)
	{
		$scope.currentCategory = category;
		$scope.loadProducts();
	}


	$scope.initShow = function()
	{
		$scope.id = $routeParams['id'];
		ProductsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.productGroup = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.id = $routeParams['id'];

		$scope.loadCategories();

		if ($scope.id)
		{
			ProductsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.productGroup = response;
			});
		}
	}


	$scope.save = function() 
	{
		ProductsRepository.save({id: $scope.id}, $scope.productGroup, function(response) 
		{
			$scope.productGroupErrors = {};
			if ($scope.id)
			{
				$scope.successAlert = 'Продукт успешно обновлен!';
			}
			else
			{
				$scope.successAlert = 'Новый продукт успешно создан!';
			}
			$scope.showAlert = true;
			$scope.id = response.id;
			$scope.category.url = response.url;
		}, 
		function(response) 
		{
            $scope.productGroupErrors = response.data.errors;
        });
	}


	$scope.delete = function(id)
	{
		ProductsRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}


	$scope.addProduct = function()
	{
		$scope.productGroup.products.push({
			'color': '',
			'price': 0,
			'price_unit': 0,
			'price_pallete': 0,
			'in_stock': 0
		});
	}

	$scope.deleteProduct = function(index)
	{
		$scope.productGroup.products.splice(index, 1);
	}
}]);