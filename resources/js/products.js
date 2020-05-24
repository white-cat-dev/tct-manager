angular.module('tctApp').controller('ProductsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'toastr',
	'CategoriesRepository',
	'ProductsRepository',
	'ExportsRepository',
	'MaterialsRepository',
	'RecipesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		toastr,
		CategoriesRepository,
		ProductsRepository,
		ExportsRepository,
		MaterialsRepository,
		RecipesRepository
	){

	$scope.Math = window.Math;

	$scope.baseUrl = '';

	$scope.productGroups = [];
	$scope.productGroup = {
		'products': [
			{
				'variation': '',
				'main_variation': '',
				'price': 0,
				'price_vat': 0,
				'price_cashless': 0,
				'price_unit': 0,
				'price_unit_vat': 0,
				'price_unit_cashless': 0,
				'in_stock': 0
			}
		]
	};
	$scope.id = 0;

	$scope.productGroupErrors = {};

	$scope.categories = [];
	$scope.currentCategory = 0;
	$scope.isStockProductsShown = true;

	$scope.materials = [];
	$scope.recipes = [];


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

	$scope.units = [
		{
			'key': 'volume_l',
			'main_key': 'volume_l',
			'name': 'Объем в литрах (л)'
		},
		{
			'key': 'volume_ml',
			'main_key': 'volume_ml',
			'name': 'Объем в миллилитрах (мл)'
		},
		{
			'key': 'weight_kg',
			'main_key': 'weight_kg',
			'name': 'Вес в килограммах (кг)'
		},
		{
			'key': 'weight_t',
			'main_key': 'weight_t',
			'name': 'Вес в тоннах (т)'
		},
	];


	$scope.init = function()
	{
		if ($location.search().category)
		{
			$scope.currentCategory = $location.search().category;
		}
		
		$scope.loadCategories();
		$scope.loadProducts();
		$scope.loadMaterials();
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
				$scope.chooseProductCategory($scope.productGroup.category);

				if ($scope.productGroup.set_pair_id) 
				{
					$scope.showSetPair();
				}
			});
		}

		RecipesRepository.query(function(response) 
		{
			$scope.recipes = response;
		});
	}


	$scope.save = function() 
	{
		ProductsRepository.save({id: $scope.id}, $scope.productGroup, function(response) 
		{
			toastr.success($scope.id ? 'Продукт успешно обновлен!' : 'Новый продукт успешно создан!');

			$scope.productGroupErrors = {};
			$scope.id = response.id;
			$scope.productGroup.url = response.url;
		}, 
		function(response) 
		{
            switch (response.status) 
            {
            	case 422:
            		toastr.error('Проверьте введенные данные');
            		$scope.productGroupErrors = response.data.errors;
            		break

            	default:
            		toastr.error('Произошла ошибка на сервере');
            		break;
            }
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
				toastr.success('Продукт успешно удален!');

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

			for (productGroup of $scope.productGroups) 
			{
				productGroup.in_stock = 0;
				for (product of productGroup.products)
				{
					productGroup.in_stock += product.in_stock;
				}
			}
		});
	}


	$scope.loadMaterials = function()
	{
		MaterialsRepository.query(function(response) 
		{
			$scope.materials = response;
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

		// if (!$scope.productCategory.variations) 
		// {
		// 	$scope.addProduct();
		// }

		$scope.loadProducts();
	}


	$scope.addProduct = function()
	{
		$scope.productGroup.products.push({
			'variation': '',
			'main_variation': '',
			'price': 0,
			'price_vat': 0,
			'price_cashless': 0,
			'price_unit': 0,
			'price_unit_vat': 0,
			'price_unit_cashless': 0,
			'in_stock': 0
		});
	}

	$scope.chooseProductVariation = function(product, variation)
	{
		product.main_variation = variation.main_key;

		if ((product.main_variation == 'color') && ($scope.mainVariation))
		{
			product.price = $scope.mainVariation.price;
			product.price_vat = $scope.mainVariation.price_vat;
			product.price_cashless = $scope.mainVariation.price_cashless;

			product.price_unit = $scope.mainVariation.price_unit;
			product.price_unit_vat = $scope.mainVariation.price_unit_vat;
			product.price_unit_cashless = $scope.mainVariation.price_unit_cashless;
		}
	}


	$scope.deleteProduct = function(index)
	{
		$scope.productGroup.products.splice(index, 1);
	}


	$scope.changePrice = function(currentProduct, key)
	{
		if (currentProduct.main_variation == 'color')
		{
			$scope.mainVariation = currentProduct;

			for (product of $scope.productGroup.products)
			{
				if (product.main_variation == 'color')
				{
					product[key] = currentProduct[key];
				}
			}
		}
	}


	$scope.isSetPairShown = false;

	$scope.showSetPair = function()
	{
		$scope.isSetPairShown = !$scope.isSetPairShown;
		
		if (!$scope.isSetPairShown)
		{
			$scope.productGroup.set_pair_id = null;
			$scope.productGroup.set_pair_ratio = 0;
			$scope.productGroup.set_pair_ratio_to = 0;
		}
	}


	$scope.saveEditField = function(key, groupNum, num) 
	{
		if (key == 'products')
		{
			var productGroup = $scope.productGroups[groupNum];

			ProductsRepository.save({id: productGroup.id}, productGroup, function(response) 
			{
				toastr.success('Изменения успешно сохранены!');
	
				if (num != undefined)
				{
					productGroup.products[num].free_in_stock = response.products[num].free_in_stock;

					productGroup.in_stock = 0;
					for (product of productGroup.products)
					{
						productGroup.in_stock += product.in_stock;
					}
				}
			}, 
			function(response) 
			{
	        });
		}
		else if (key == 'materials')
		{
			var material = $scope.materials[groupNum];

			MaterialsRepository.save({id: material.id}, material, function(response) 
			{
				toastr.success('Изменения успешно сохранены!');
			}, 
			function(response) 
			{
	        });
		}
	}


	$scope.loadExportFile = function() 
	{
		var request = {
			'category': $scope.currentCategory,
			'stock': $scope.isStockProductsShown,
			'materials': false
		};

		ExportsRepository.products(request, function(response) 
		{
			document.location.href = response.file;
		}, 
		function(response) 
		{
        });
	}
}]);