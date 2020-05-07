angular.module('tctApp').controller('ProductsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'CategoriesRepository',
	'ProductsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		CategoriesRepository,
		ProductsRepository
	){

	$scope.Math = window.Math;

	$scope.baseUrl = '';

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
			'main_key': 'red',
			'name': 'серый'
		},
		{
			'key': 'red',
			'main_key': 'red',
			'name': 'красный'
		},
		{
			'key': 'yellow',
			'main_key': 'color',
			'name': 'желтый'
		},
		{
			'key': 'brown',
			'main_key': 'color',
			'name': 'коричневый'
		},
		{
			'key': 'black',
			'main_key': 'color',
			'name': 'черный'
		}
	];

	$scope.grades = [
		{
			'key': 'd400',
			'main_key': 'd400',
			'name': 'D500'
		},
		{
			'key': 'd500',
			'main_key': 'd500',
			'name': 'D500'
		},
		{
			'key': 'd600',
			'main_key': 'd600',
			'name': 'D600'
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


	$scope.initShow = function()
	{
		$scope.baseUrl = 'products';

		$scope.id = $routeParams['id'];

		ProductsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.productGroup = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'products';

		$scope.id = $routeParams['id'];

		$scope.loadCategories();

		if ($scope.id)
		{
			ProductsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.productGroup = response;
				$scope.productCategory = $scope.productGroup.category;
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
			$scope.productGroup.url = response.url;
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
			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				$scope.successAlert = 'Продукт успешно удален!';
				$scope.showAlert = true;

				$timeout(function() {
					$scope.showAlert = false;
				}, 2000);

				$scope.init();
			}
		}, 
		function(response) 
		{
           
        });
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



	$scope.productCategory = null;

	$scope.chooseProductCategory = function() 
	{
		for (category of $scope.categories)
		{
			if (category.id == $scope.productGroup.category_id)
			{
				$scope.productCategory = category;
				$scope.productGroup.adjectives = category.adjectives;
				break;
			}
		}

		if (!$scope.productCategory.variations) 
		{
			$scope.addProduct();
		}
	}


	$scope.addProduct = function()
	{
		$scope.productGroup.products.push({
			'variation': '',
			'main_variation': '',
			'price': 0,
			'price_unit': 0,
			'price_pallete': 0,
			'in_stock': 0
		});
	}

	$scope.chooseProductVariation = function(product, variation)
	{
		product.main_variation = variation.main_key;
	}


	$scope.deleteProduct = function(index)
	{
		$scope.productGroup.products.splice(index, 1);
	}
}]);