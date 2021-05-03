angular.module('tctApp').controller('ProductsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
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
		'url': '#',
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
	$scope.isFreeStockProductsShown = false;

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
		},
		{
			'key': 'light-brown',
			'main_key': 'color',
			'name': 'светло-коричневый'
		},
		{
			'key': 'orange',
			'main_key': 'color',
			'name': 'оранжевый'
		},
		{
			'key': 'white',
			'main_key': 'color',
			'name': 'белый'
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
		// $scope.loadMaterials();
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'products';

		$scope.id = $routeParams['id'];

		$scope.isLoading = true;
		ProductsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.isLoading = false;
			$scope.productGroup = response;

			$scope.initStocks();
		},
		function(response)
		{
			$scope.isLoading = false;
			$scope.productGroup = null;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'products';

		$scope.id = $routeParams['id'];

		$scope.isStockProductsShown = false;

		if ($scope.id)
		{
			$scope.isLoading = true;
			ProductsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.isLoading = false;
				$scope.productGroup = response;

				if ($scope.productGroup.set_pair_id) 
				{
					$scope.showSetPair();
				}

				$scope.loadCategories();
			},
			function(response)
			{
				$scope.isLoading = false;
				$scope.productGroup = null;
			});
		}
		else
		{
			$scope.loadCategories();
		}

		RecipesRepository.query(function(response) 
		{
			$scope.recipes = response;
		});
	}


	$scope.save = function() 
	{
		$scope.isSaving = true;
		ProductsRepository.save({id: $scope.id}, $scope.productGroup, function(response) 
		{
			$scope.isSaving = false;

			toastr.success($scope.id ? 'Продукт успешно обновлен!' : 'Новый продукт успешно создан!');

			$scope.productGroupErrors = {};
			$scope.id = response.id;
			$scope.productGroup.url = response.url;
		}, 
		function(response)
		{
			$scope.isSaving = false;

			switch (response.status) 
            {
            	case 422:
            		$scope.productGroupErrors = response.data.errors;
            		break;
            }
		});
	}


	$scope.showDelete = function(product)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'product';
		$scope.deleteData = product;

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

		ProductsRepository.delete({id: id}, function(response) 
		{
			$scope.isDeleting = false;
			$scope.hideDelete();

			toastr.success('Продукт успешно удален!');

			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				$scope.init();
			}
		},
		function(response) 
		{
			$scope.isDeleting = false;
		});
	}


	$scope.showCopy = function(product)
	{
		$scope.isCopyModalShown = true;
		$scope.copyType = 'product';
		$scope.copyData = product;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideCopy = function()
	{
		$scope.isCopyModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
	}


	$scope.copy = function(id)
	{
		$scope.isCopying = true;
		ProductsRepository.copy({id: id}, {}, function(response) 
		{
			$scope.isCopying = false;
			$scope.hideCopy();

			toastr.success('Копия успешно создана');

			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl + '/' + response.id + '/edit').replace();
			}
			else
			{
				$scope.init();
			}
		},
		function(response)
		{
			$scope.isCopying = false;
		});
	}


	$scope.loadCategories = function()
	{
		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;

			if ($scope.productGroup && $scope.productGroup.category)
			{
				$scope.chooseProductCategory($scope.productGroup.category);
			}
		});
	}


	$scope.chooseInStock = function(isFreeStock)
	{
		if (isFreeStock)
		{
			if ($scope.isFreeStockProductsShown && !$scope.isStockProductsShown)
			{
				$scope.isStockProductsShown = true;
			}
		}
		else
		{
			if ($scope.isFreeStockProductsShown && !$scope.isStockProductsShown)
			{
				$scope.isFreeStockProductsShown = false;
			}
		}

		$scope.loadProducts();
	}


	$scope.loadProducts = function()
	{
		var request = {};

		if ($scope.currentCategory)
		{
			request['category'] = $scope.currentCategory;
		}
		if ($scope.isFreeStockProductsShown)
		{
			request['stock'] = 'free';
		}
		else if ($scope.isStockProductsShown)
		{
			request['stock'] = 'all';
		}

		$scope.isLoading = true;

		ProductsRepository.query(request, function(response) 
		{
			$scope.isLoading = false;
			$scope.productGroups = response;
		},
		function(response)
		{
			$scope.isLoading = false;
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
				$scope.productGroup.category = category;
				if (!$scope.productGroup.adjectives)
				{
					$scope.productGroup.adjectives = category.adjectives;
				}
				break;
			}
		}

		var request = {
			'category': $scope.productGroup.category_id
		};

		ProductsRepository.query(request, function(response) 
		{
			$scope.productGroups = response;
		});
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
		nextKey = (key.indexOf('unit') !== -1) ? key.replace('price_unit', 'price') : key.replace('price', 'price_unit');

		if (currentProduct.main_variation == 'color')
		{
			$scope.mainVariation = currentProduct;

			for (product of $scope.productGroup.products)
			{
				if (product.main_variation == 'color')
				{
					product[key] = currentProduct[key];
					product[nextKey] = currentProduct[nextKey];
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


	$scope.showProductStockModal = function(productGroup, productNum)
	{
		$scope.isModalLoading = true;
		$scope.isProductStockModalShown = true;
		$scope.modalProductGroup = angular.copy(productGroup);
		$scope.modalProduct = $scope.modalProductGroup.products[productNum];
		$scope.modalProduct.old_in_stock = $scope.modalProduct.in_stock;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideProductStockModal = function()
	{
		$scope.isProductStockModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
	}


	$scope.saveProductStock = function()
	{
		$scope.isModalSaving = true;

		ProductsRepository.save({id: $scope.modalProductGroup.id}, $scope.modalProductGroup, function(response) 
		{
			$scope.isModalSaving = false;

			toastr.success('Изменения успешно сохранены!');

			$scope.hideProductStockModal();

			if ($scope.baseUrl)
			{
				$scope.initShow();
			}
			else
			{
				$scope.init();
			}
		}, 
		function(response) 
		{
			$scope.isModalSaving = false;
        });
	}


	// $scope.saveEditField = function(groupNum, num, key) 
	// {
	// 	var productGroup = $scope.productGroups[groupNum];
	// 	var product = productGroup.products[num];
		
	// 	if (product[key] != product['new_' + key])
	// 	{
	// 		product[key] = product['new_' + key];
	// 	}
	// 	else
	// 	{
	// 		return;
	// 	}

	// 	ProductsRepository.save({id: productGroup.id}, productGroup, function(response) 
	// 	{
	// 		toastr.success('Изменения успешно сохранены!');

	// 		productGroup.products[num].free_in_stock = response.products[num].free_in_stock;

	// 		productGroup.in_stock = 0;
	// 		for (product of productGroup.products)
	// 		{
	// 			productGroup.in_stock += product.in_stock;
	// 		}
	// 	},
	// 	function(response)
	// 	{
	// 		$scope.isLoading = false;
	// 	});
	// }


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


	$scope.modalProductOrders = [];

	$scope.showProductOrdersModal = function(product)
	{
		$scope.isModalLoading = true;
		$scope.isProductOrdersModalShown = true;
		$scope.modalProduct = product;

		ProductsRepository.orders({'id': product.id}, function(response) 
		{
			$scope.isModalLoading = false;

			$scope.modalProductOrders = response;
		},
		function(response)
		{
			$scope.isModalLoading = false;
		});

        document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideProductOrdersModal = function()
	{
		$scope.isProductOrdersModalShown = false;
		$scope.modalProductOrders = [];

		document.querySelector('body').classList.remove('modal-open');
	}


	$scope.stocksMonthes = [];
	$scope.stocksYears = [];

    $scope.stocksCurrentDate = {};
    $scope.stocksCurrentProduct = 0;
    
    $scope.stocks = [];

    $scope.initStocks = function()
    {
		if ($scope.stocksCurrentProduct == 0)
		{
			$scope.stocksCurrentProduct = $scope.productGroup.products[0].id;
		}

		var request = {
			'id': $scope.stocksCurrentProduct
		};
		if ($scope.stocksCurrentDate.year)
		{
			request.year = $scope.stocksCurrentDate.year;
		}
		if ($scope.stocksCurrentDate.month)
		{
			request.month = $scope.stocksCurrentDate.month;
		}

    	$scope.isStocksLoading = true;

    	ProductsRepository.stocks(request, function(response) 
		{
			$scope.isStocksLoading = false;

			$scope.stocksMonthes = response.monthes;
			$scope.stocksYears = response.years;

			$scope.stocksCurrentDate.month = response.month;
			$scope.stocksCurrentDate.year = response.year;

			$scope.stocks = response.stocks;
		},
		function(response)
		{
			$scope.isStocksLoading = false;
		});
    }
}]);