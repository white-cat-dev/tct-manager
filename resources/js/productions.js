angular.module('tctApp').controller('ProductionsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'$filter',
	'toastr',
	'ProductionsRepository',
	'OrdersRepository',
	'ProductsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		$filter,
		toastr,
		ProductionsRepository,
		OrdersRepository,
		ProductsRepository
	){

	$scope.Math = window.Math;

	$scope.days = 0;
	$scope.monthes = [];
	$scope.years = [];

	$scope.currentDate = {
		'day': 0,
		'month': 0,
		'year': 0
	};

	$scope.productionProducts = [];
	$scope.productionOrders = [];
	$scope.productionCategories = [];
	$scope.productionMaterials = [];
	
	$scope.facilities = {};

	$scope.productionsPlanned = false;
	$scope.isAllProductionsShown = false;


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

		ProductionsRepository.get(request, function(response) 
		{
			$scope.days = response.days;
			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDate.day = response.day;
			$scope.currentDate.month = response.month;
			$scope.currentDate.year = response.year;

			$scope.productionProducts = response.products;
			$scope.productionOrders = response.orders;
			$scope.productionCategories = response.categories;
			$scope.productionMaterials = response.materials;

			$scope.facilities = response.facilities;

			$scope.productionsPlanned = false;
			console.log($scope.productionProducts);
			for (product of $scope.productionProducts)
			{
				if (product.productions[0] && product.productions[0].planned > 0)
				{
					$scope.productionsPlanned = true;
					break;
				}
			}
			$scope.isLoading = false;
		});
	}


	$scope.markColors = [
		'#e67e22',
		'#9b59b6',
		'#2ecc71',
		'#1abc9c',
		'#d35400',
		'#f1c40f',
		'#3498db',
	];

	$scope.ordersMarkColors = {};

	$scope.markOrder = function(orderId)
	{
		if ($scope.ordersMarkColors[orderId])
		{
			$scope.markColors.push($scope.ordersMarkColors[orderId]);
			delete $scope.ordersMarkColors[orderId];
		}
		else
		{
			$scope.ordersMarkColors[orderId] = $scope.markColors.pop();
		}
	}

	
	$scope.getOrderMarkColor = function(production)
	{
		if ((production) && ($scope.ordersMarkColors[production.order_id]))
		{
			return $scope.ordersMarkColors[production.order_id];
		}
		else
		{
			return 'transparent';
		}
	}


	$scope.showAllProductions = function()
	{
		$scope.isAllProductionsShown = true
	}



	$scope.isModalShown = false;

	$scope.modalDate = new Date();
	$scope.modalProductionProducts = [];
	$scope.modalProductionCategories = [];

	$scope.isOrdersShown = false;


	$scope.showModal = function(day)
	{
		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});


		$scope.modalDate = new Date($scope.currentDate.year, $scope.currentDate.month - 1, day);

		$scope.modalProductionProducts = [];

		for (product of $scope.productionProducts)
		{
			if (product.productions[day])
			{
				var newProduct = angular.copy(product);
				newProduct.orders = [];
				newProduct.productions = [];
				newProduct.production = product.productions[day];
				newProduct.production_id = newProduct.production.id;

				if (product.productions[0])
				{
					newProduct.base_planned = (product.productions[0].planned > product.productions[0].performed) ? 
						Math.round((product.productions[0].planned - product.productions[0].performed) * 1000) / 1000 : 0;
				}
				else
				{
					newProduct.base_planned = 0;
				}

				$scope.modalProductionProducts.push(newProduct);
			}
			else if (product.productions[0])
			{
				if (product.productions[0].planned > product.productions[0].performed)
				{
					var facilities = $scope.getCategoryFacilities(product.category_id);

					console.log(product.product_group.name, product.variation);
					if (facilities.length > 0)
					{
						$scope.newProduct[facilities[0].id] = {
							'id': product.id,
							'production_id': Infinity,
							'category_id': product.category_id,
							'product_group_id': product.product_group_id,
							'product_id': product.id,
							'product_group': product.product_group,
							'category': product.category,
							'variation': product.variation,
							'units_text': product.units_text,
							'variation_noun_text': product.variation_noun_text,
							'base_planned': Math.round((product.productions[0].planned - product.productions[0].performed) * 1000) / 1000
						};

						$scope.addProduct(facilities[0].id);
					}
				}
			}


			console.log($scope.modalProductionProducts);

			// for (order of product.orders)
			// {
			// 	if (order.productions[day])
			// 	{
			// 		var newOrder = angular.copy(order);

			// 		newOrder.production = newOrder.productions[day];
			// 		newOrder.productions = [];

			// 		newProduct.orders.push(newOrder);
			// 	}
			// }

			// if (newProduct.orders.length > 0)
			// {
			// 	$scope.modalProductionProducts.push(newProduct);
			// }
		}


		$scope.modalProductionCategories = [];

		for (category of $scope.productionCategories)
		{
			if (category.productions[day])
			{
				var newCategory = angular.copy(category);
				newCategory.production = newCategory.productions[day];
				newCategory.productions = [];

				$scope.modalProductionCategories.push(newCategory);
			}
		}

		$scope.modalProductionMaterials = [];

		for (material of $scope.productionMaterials)
		{
			if (material.applies[day])
			{
				var newMaterial = angular.copy(material);
				newMaterial.apply = newMaterial.applies[day];
				newMaterial.applies = [];

				$scope.modalProductionMaterials.push(newMaterial);
			}
		}

		document.querySelector('body').classList.add('modal-open');
		$scope.isModalShown = true;
	}


	$scope.hideModal = function()
	{
		document.querySelector('body').classList.remove('modal-open');
		$scope.isModalShown = false;

		$scope.modalDate = '';
		$scope.modalProductionProducts = [];
	}

	


	$scope.save = function()
	{
		var request = {
			'materials': $scope.modalProductionMaterials,
			'products': $scope.modalProductionProducts,
			'year': $scope.currentDate.year,
			'month': $scope.currentDate.month,
			'day': $scope.currentDate.day
		}

		$scope.isSaving = true;
		ProductionsRepository.save(request, function(response) 
		{
			$scope.isSaving = false;
			toastr.success('Все изменения успешно сохранены!');

			$scope.hideModal();
			$scope.init();
		}, 
		function(response) 
		{
            $scope.isSaving = false;
            toastr.error('Произошла ошибка на сервере');
        });
	}



	$scope.getCategoryFacilities = function(category)
	{
		var categoryFacilities = [];
		for (key in $scope.facilities)
		{
			if ($scope.facilities[key].categories_list.indexOf(category) != -1)
			{
				categoryFacilities.push($scope.facilities[key]);
			}
		}

		return categoryFacilities;
	}


	$scope.getFacilityProductGroups = function(facility)
	{
		var facilityProductGroups = [];
		for (key in $scope.productGroups)
		{
			var category = $scope.productGroups[key].category_id;
			if (facility.categories_list.indexOf(category) != -1)
			{
				facilityProductGroups.push($scope.productGroups[key]);
			}
		}

		return facilityProductGroups;
	}


	$scope.getFacilityProductionProducts = function(facility)
	{
		var facilityProductionProducts = [];
		for (key in $scope.modalProductionProducts)
		{
			if ($scope.modalProductionProducts[key].production.facility_id == facility)
			{
				facilityProductionProducts.push($scope.modalProductionProducts[key]);
			}
		}

		return facilityProductionProducts;
	}



	$scope.hoverDay = 0;

	$scope.chooseHoverDay = function(day)
	{
		$scope.hoverDay = day;
	}


	$scope.isAddProductShown = {};
	$scope.newProduct = {};
	

	$scope.showAddProduct = function(facility)
	{
		$scope.isAddProductShown[facility] = true;
	}


	$scope.chooseProductGroup = function(facility, productGroup)
	{
		$scope.newProduct[facility] = {};
		$scope.newProduct[facility].id = null;
		$scope.newProduct[facility].product_group = productGroup;
		$scope.newProduct[facility].product_group_id = productGroup.id;
		$scope.newProduct[facility].category = productGroup.category;
		$scope.newProduct[facility].category_id = productGroup.category_id;

		for (key in $scope.productGroups)
		{
			if ($scope.productGroups[key].id == productGroup.id)
			{
				$scope.newProduct[facility].products = $scope.productGroups[key].products;
				break;
			}
		}

		if (!productGroup.category.variations)
		{
			var product = $scope.newProduct[facility].products[0];
			$scope.chooseProduct(facility, product);
		}
	}


	$scope.chooseProduct = function(facility, product)
	{
		$scope.newProduct[facility].id = product.id;
		$scope.newProduct[facility].units_text = product.units_text;
		$scope.newProduct[facility].variation = product.variation;
		$scope.newProduct[facility].variation_noun_text = product.variation_noun_text;
	}
	

	$scope.addProduct = function(facility)
	{
		var facilityId = facility;

		if (facility == 0)
		{
			var facilities = $scope.getCategoryFacilities($scope.newProduct[facility].category_id);

			if (facilities.length > 0)
			{
				facilityId = facilities[0].id;
			}
		}


		var production = {
			'day': $scope.modalDate.getDate(),
			'date': $filter('date')($scope.modalDate, 'yyyy-MM-dd'),
			'category_id': $scope.newProduct[facility].category_id,
			'product_group_id': $scope.newProduct[facility].product_group_id,
			'facility_id': facilityId,
			'product_id': $scope.newProduct[facility].id,
			'order_id': 0,
			'planned': 0,
			'performed': 0,
			'batches': 0
		};

		$scope.newProduct[facility].production = production;
		$scope.newProduct[facility].base_planned = 0;

		$scope.modalProductionProducts.push($scope.newProduct[facility]);
		$scope.newProduct[facility] = {};
		$scope.isAddProductShown[facility] = false;
	}


	
	$scope.chosenModalType = 'perform';


	$scope.chooseModalType = function(type)
	{
		$scope.chosenModalType = type;
		$scope.chosenModalFacility = 0;
	}


	$scope.updateProductionPlanned = function(product)
	{
		product.production.planned = product.production.batches * product.product_group.units_from_batch;
	}


	$scope.updateOrderProductionsPerformed = function(product)
	{
		performed = product.production.performed;

		for (order of product.orders)
		{
			if (order.production.planned > performed)
			{
				order.production.performed = performed;
			}
			else
			{
				order.production.performed = order.production.planned;
			}
			
			performed -= order.production.performed;
		}

		if (performed > 0)
		{
			if (product.orders[product.orders.length - 1].id == 0)
			{
				product.orders[product.orders.length - 1].production.performed = performed;
			}
			else
			{
				product.orders.push({
					'id': 0,
					'production': {
						'day': $scope.modalDate.getDate(),
						'date': $filter('date')($scope.modalDate, 'yyyy-MM-dd'),
						'facility_id': product.production.facility_id,
						'order_id': 0,
						'category_id': product.category_id,
						'product_group_id': product.product_group_id,
						'product_id': product.id,
						'planned': 0,
						'performed': performed
					}
				});
			}
		}
	}

	$scope.updateOrderProductionsFacility = function(product)
	{
		for (order of product.orders)
		{
			order.production.facility_id = product.production.facility_id;
		}
	}
}]);