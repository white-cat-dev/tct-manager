var $ = require('jquery');

angular.module('tctApp').controller('ProductionsController', [
	'$rootScope',
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'$filter',
	'ProductionsRepository',
	'OrdersRepository',
	'ProductsRepository',
	'ExportsRepository',
	function(
		$rootScope,
		$scope, 
		$routeParams,
		$location,
		$timeout,
		$filter,
		ProductionsRepository,
		OrdersRepository,
		ProductsRepository,
		ExportsRepository
	){

	$scope.Math = window.Math;
	$scope.Object = window.Object;

	$scope.days = 0;
	$scope.monthes = [];
	$scope.years = [];

	$scope.currentDate = {};
	$scope.currentDatetime = new Date();

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

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});

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
			for (product of $scope.productionProducts)
			{
				for (day = 1; day <= $scope.days; day++)
				{
					if (!product.productions[day])
					{
						product.productions[day] = {
							'day': day,
							'date': $filter('date')(new Date($scope.currentDate.year, $scope.currentDate.month - 1, day), 'yyyy-MM-dd'),
							'category_id': product.category_id,
							'product_group_id': product.product_group_id,
							'facility_id': 0,
							'product_id': product.id,
							'order_id': 0,
							'planned': 0,
							'manual_planned': -1,
							'auto_planned': 0,
							'performed': 0,
							'batches': 0,
							'manual_batches': -1,
							'auto_batches': 0
						};
					}
					else if (product.productions[day].performed == 0)
					{
						// product.productions[day].performed = '';
					}

					if (product.productions[day] && (day >= $scope.currentDate.day) && (product.productions[day].planned > 0))
					{
						product.isPlanned = true;
					}
				}

				if (product.productions[0] && (product.productions[0].planned > product.productions[0].performed))
				{
					$scope.isProductionsPlanned = true;
					product.isPlanned = true;
					// break;
				}
			}
			$scope.initScroll();
		},
		function(response)
		{
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

	// $scope.ordersMarkColors = {};

	// $scope.markOrder = function(orderId)
	// {
	// 	if ($scope.ordersMarkColors[orderId])
	// 	{
	// 		$scope.markColors.push($scope.ordersMarkColors[orderId]);
	// 		delete $scope.ordersMarkColors[orderId];
	// 	}
	// 	else
	// 	{
	// 		$scope.ordersMarkColors[orderId] = $scope.markColors.pop();
	// 	}
	// }

	
	// $scope.getOrderMarkColor = function(production)
	// {
	// 	if ((production) && ($scope.ordersMarkColors[production.order_id]))
	// 	{
	// 		return $scope.ordersMarkColors[production.order_id];
	// 	}
	// 	else
	// 	{
	// 		return 'transparent';
	// 	}
	// }


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
		if (Object.keys($scope.updatedProductions).length > 0)
		{
			return;
		}

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});


		$scope.modalDate = new Date($scope.currentDate.year, $scope.currentDate.month - 1, day);
		$scope.modalDay = day;

		// $scope.modalProductionProducts = [];

		// for (product of $scope.productionProducts)
		// {
		// 	if (product.productions[day])
		// 	{
		// 		var newProduct = angular.copy(product);
		// 		newProduct.orders = [];
		// 		newProduct.productions = [];
		// 		newProduct.production = product.productions[day];
		// 		newProduct.production_id = newProduct.production.id;

		// 		if (product.productions[0])
		// 		{
		// 			newProduct.base_planned = (product.productions[0].planned > product.productions[0].performed) ? 
		// 				Math.round((product.productions[0].planned - product.productions[0].performed) * 1000) / 1000 : 0;
		// 		}
		// 		else
		// 		{
		// 			newProduct.base_planned = 0;
		// 		}

		// 		$scope.modalProductionProducts.push(newProduct);
		// 	}
		// 	else if (product.productions[0])
		// 	{
		// 		if (product.productions[0].planned > product.productions[0].performed)
		// 		{
		// 			var facilities = $scope.getCategoryFacilities(product.category_id);

		// 			if (facilities.length > 0)
		// 			{
		// 				$scope.newProduct[facilities[0].id] = {
		// 					'id': product.id,
		// 					'production_id': Infinity,
		// 					'category_id': product.category_id,
		// 					'product_group_id': product.product_group_id,
		// 					'product_id': product.id,
		// 					'product_group': product.product_group,
		// 					'category': product.category,
		// 					'variation': product.variation,
		// 					'units_text': product.units_text,
		// 					'variation_noun_text': product.variation_noun_text,
		// 					'base_planned': Math.round((product.productions[0].planned - product.productions[0].performed) * 1000) / 1000
		// 				};

		// 				$scope.addProduct(facilities[0].id);
		// 			}
		// 		}
		// 	}
		// }


		$scope.modalProductionCategories = [];

		var mainCategories = {};

		for (category of $scope.productionCategories)
		{
			if (category.productions[day])
			{
				var newCategory = angular.copy(category);
				newCategory.production = newCategory.productions[day];
				newCategory.production.performed = newCategory.production.performed + ' ' + newCategory.units_text;
				newCategory.productions = [];

				var mainCategory = newCategory.main_category;
				if (mainCategories[mainCategory])
				{
					mainCategories[mainCategory].name += ' + ' + newCategory.name;
					mainCategories[mainCategory].production.salary += newCategory.production.salary;
					mainCategories[mainCategory].production.performed +=  ' + ' + newCategory.production.performed;
				}
				else
				{
					mainCategories[mainCategory] = newCategory;
				}
			}
		}

		for (key in mainCategories)
		{
			$scope.modalProductionCategories.push(mainCategories[key]);
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
		$scope.isModalShown = true;

		setTimeout(function()
		{
			document.querySelector('.modal.production-modal').focus();
		}, 150);

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideModal = function()
	{
		$scope.isModalShown = false;

		$scope.modalDate = '';
		$scope.modalProductionProducts = [];

		document.querySelector('body').classList.remove('modal-open');
		document.querySelector('.production-block .productions-block-content').focus();
	}

	

	$scope.save = function()
	{
		var productions = [];

		if ($scope.isModalShown)
		{
			for (product of $scope.productionProducts)
			{
				if (product.productions[$scope.modalDay])
				{
					productions.push(product.productions[$scope.modalDay]);
				}
			}
		}
		else
		{
			for (product of $scope.productionProducts)
			{
				for (day = 1; day <= $scope.days; day++)
				{
					if ($scope.updatedProductions[product.id] && $scope.updatedProductions[product.id].indexOf(day) != -1)
					{
						productions.push(product.productions[day]);
					}
				}
			}
		}

		var request = {
			'productions': productions,
			'year': $scope.currentDate.year,
			'month': $scope.currentDate.month
		}

		$scope.isSaving = true;
		ProductionsRepository.save(request, function(response) 
		{
			$scope.isSaving = false;
			$scope.updatedProductions = {};
			toastr.success('Все изменения успешно сохранены!');

			$scope.hideModal();
			$scope.init();
		}, 
		function(response) 
		{
            $scope.isSaving = false;
        });
	}


	$scope.saveMaterials = function()
	{
		var request = {
			'materials': $scope.modalProductionMaterials,
			'year': $scope.currentDate.year,
			'month': $scope.currentDate.month
		}

		$scope.isModalSaving = true;
		ProductionsRepository.saveMaterials(request, function(response) 
		{
			$scope.isModalSaving = false;

			toastr.success('Все изменения успешно сохранены!');

			$scope.hideModal();
			$scope.init();
		}, 
		function(response) 
		{
            $scope.isModalSaving = false;
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
	$scope.hoverProduct = 0;

	$scope.chooseHoverDay = function(day)
	{
		$scope.hoverDay = day;
	}
	$scope.chooseHoverProduct = function(product)
	{
		$scope.hoverProduct = product;
	}


	$scope.updatedProductions = {};

	$scope.changeProductionPerformed = function(product, day)
	{
		$rootScope.inputFloat(product.productions[day], 'new_performed');

		if (product.productions[day].new_performed == product.productions[day].performed)
		{
			return;
		}

		var performed = product.productions[day].new_performed - product.productions[day].performed;
		product.productions[day].performed = product.productions[day].new_performed;
		product.in_stock = Math.round((product.in_stock + performed) * 1000) / 1000;
		
		if (product.productions[0])
		{
			product.productions[0].performed = Math.round((product.productions[0].performed + performed) * 1000) / 1000;
		}

		$scope.updateProduction(product, day);
	}


	$scope.changeProductionPlanned = function(product, day)
	{
		$rootScope.inputFloat(product.productions[day], 'new_batches');

		if (product.productions[day].manual_batches == product.productions[day].new_batches)
		{
			return;
		}

		product.productions[day].manual_batches = +product.productions[day].new_batches;
		product.productions[day].manual_planned = Math.round((product.productions[day].manual_batches * product.product_group.units_from_batch) * 1000) / 1000;

		$scope.updateProduction(product, day);
	}


	$scope.updateProduction = function(product, day)
	{
		if (!$scope.updatedProductions[product.id])
		{
			$scope.updatedProductions[product.id] = [day];
		}
		else if ($scope.updatedProductions[product.id].indexOf(day) == -1)
		{
			$scope.updatedProductions[product.id].push(day);
		}

		if (product.productions[0])
		{
			var basePlanned = product.productions[0].planned - product.productions[0].performed; 
			var lastBatches = (basePlanned <= 100) ? ((basePlanned <= 50) ? 1 : 2) : 3;
			var productionDateTo = null;

			for (day = 1; day <= $scope.days; day++)
			{
				if (day == $scope.currentDate.day)
				{
					if (product.productions[day].performed <= 0)
					{
						basePlanned -= (product.productions[day].manual_batches < 0) ? product.productions[day].auto_planned : product.productions[day].manual_planned;
						if (product.productions[day].manual_batches > 0)
						{
							lastBatches = product.productions[day].manual_batches;
						}
						continue;
					}
				}
				else if (day < $scope.currentDate.day)
				{
					continue;
				}

				if (basePlanned <= 0)
				{
					if (product.productions[day].manual_batches < 0)
					{
						product.productions[day].auto_batches = 0;
						product.productions[day].auto_planned = 0;
						product.productions[day].new_batches = '';
					}
					continue;
				}

				if (product.productions[day].manual_batches > 0)
				{
					lastBatches = product.productions[day].manual_batches;
				}

				if (product.productions[day].manual_batches < 0)
				{
					if ((lastBatches * product.product_group.units_from_batch > basePlanned) && (lastBatches > 1))
					{
						lastBatches = Math.ceil(basePlanned / product.product_group.units_from_batch);
					}

					product.productions[day].auto_batches = lastBatches;
					product.productions[day].auto_planned = Math.round((product.productions[day].auto_batches * product.product_group.units_from_batch) * 1000) / 1000;
					product.productions[day].new_batches = product.productions[day].auto_batches;
					basePlanned -= product.productions[day].auto_planned;
				}
				else
				{
					basePlanned -= product.productions[day].manual_planned;
				}

				if ((basePlanned <= 0) && !productionDateTo)
				{
					var productionDate = new Date($scope.currentDate.year, $scope.currentDate.month - 1, day);
					productionDateTo = $filter('date')(productionDate, 'yyyy-MM-dd');
					productionFormattedDateTo = $filter('date')(productionDate, 'dd.MM.yyyy');
				}
			}

			if (basePlanned > 0)
			{
				var days = Math.ceil(basePlanned / product.product_group.units_from_batch);
				var productionDate = new Date($scope.currentDate.year, $scope.currentDate.month - 1, day);
				productionDate = productionDate.setDate(productionDate.getDate() + days);
				productionDateTo = $filter('date')(productionDate, 'yyyy-MM-dd');
				productionFormattedDateTo = $filter('date')(productionDate, 'dd.MM.yyyy');
			}

			product.productions[0].date_to = productionDateTo;
			product.productions[0].formatted_date_to = productionFormattedDateTo;
		}
	}


	$scope.isAddProductShown = {};
	$scope.newProduct = {};
	

	$scope.showAddProduct = function(facility)
	{
		$scope.isAddProductShown[facility] = true;
		$scope.newProduct[facility] = {};
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
		$scope.newProduct[facility].in_stock = product.in_stock;
	}
	

	$scope.addProduct = function(facility)
	{
		if (!$scope.newProduct[facility].id)
		{
			return;
		}

		var facilityId = facility;

		if (facility == 0)
		{
			var facilities = $scope.getCategoryFacilities($scope.newProduct[facility].category_id);

			if (facilities.length > 0)
			{
				facilityId = facilities[0].id;
			}
		}

		for (var i = 0; i < $scope.productionProducts.length; i++)
		{
			if ($scope.newProduct[facility].id == $scope.productionProducts[i].id)
			{
				$scope.newProduct[facility] = $scope.productionProducts[i];
				$scope.productionProducts.splice(i, 1);
				break;
			}
		}

		if (!$scope.newProduct[facility].productions)
		{
			$scope.newProduct[facility].productions = [];

			for (day = 1; day <= $scope.days; day++)
			{
				$scope.newProduct[facility].productions[day] = {
					'day': day,
					'date': $filter('date')(new Date($scope.currentDate.year, $scope.currentDate.month - 1, day), 'yyyy-MM-dd'),
					'category_id': $scope.newProduct[facility].category_id,
					'product_group_id': $scope.newProduct[facility].product_group_id,
					'facility_id': 0,
					'product_id': $scope.newProduct[facility].id,
					'order_id': 0,
					'planned': 0,
					'manual_planned': -1,
					'auto_planned': 0,
					'performed': 0,
					'batches': 0,
					'manual_batches': -1,
					'auto_batches': 0
				};
			}
		}

		$scope.newProduct[facility].isPlanned = true;

		$scope.productionProducts.push($scope.newProduct[facility]);

		$scope.newProduct[facility] = {};
		$scope.isAddProductShown[facility] = false;
		document.querySelector('.production-block .productions-block-content').focus();
	}


	$scope.focusProductionsBlock = function($event)
	{
		if (($event.which === 38) || (($event.which === 40)))
        {
        	document.querySelector('.production-block .productions-block-content').focus();
        }
	}


	
	$scope.chosenModalType = 'total';


	$scope.chooseModalType = function(type)
	{
		$scope.chosenModalType = type;
		$scope.chosenModalFacility = 0;
	}


	$scope.updateProductionPlanned = function(product)
	{
		product.production.planned = Math.round((product.production.batches * product.product_group.units_from_batch) * 1000) / 1000;
		product.production.manual_batches = product.production.batches;
		product.production.manual_planned = product.production.planned;
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

			document.querySelector('body').classList.add('modal-open');
		}, 
		function(response) 
		{
            $scope.isModalLoading = false;
        });
	}


	$scope.hideProductOrdersModal = function()
	{
		$scope.isProductOrdersModalShown = false;
		$scope.modalProductOrders = [];

		document.querySelector('body').classList.remove('modal-open');
		document.querySelector('.production-block .productions-block-content').focus();
	}


	$scope.showReplanModal = function(order)
	{
		$scope.isReplanModalShown = true;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideReplanModal = function()
	{
		$scope.isReplanModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
		document.querySelector('.production-block .productions-block-content').focus();
	}


	$scope.replan = function()
	{
		$scope.isReplaning = true;
		ProductionsRepository.replan(function(response) 
		{
			$scope.isReplaning = false;
			toastr.success('План производства успешно перестроен!');

			$scope.hideReplanModal();
			$scope.init();
		}, 
		function(response) 
		{
            $scope.isReplaning = false;
        });
	}


	$scope.loadExportFile = function() 
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

		ExportsRepository.productions(request, function(response) 
		{
			document.location.href = response.file;
		},
		function(response)
		{
			
		});
	}


	$scope.initScroll = function()
	{
		setTimeout(function()
		{
			var productionBlock = document.querySelector('.production-block');
			var mainBlock = productionBlock.querySelector('.productions-block-content');
			var leftBlock = productionBlock.querySelector('.products-block-content');
			var topBlock = productionBlock.querySelector('.productions-block-top-table > div');

			mainBlock.focus();

			var scrollLeft = mainBlock.querySelector('.table').clientWidth / $scope.days * ($scope.currentDate.day - 1);
			scrollLeft = scrollLeft - mainBlock.clientWidth / 2 + 25;
			if (scrollLeft < 0)
			{
				scrollLeft = 0;
			}

			mainBlock.scrollLeft = scrollLeft;

			mainBlock.addEventListener('scroll', function(event) 
			{
				var scrollTop = mainBlock.scrollTop;
				var scrollLeft = mainBlock.scrollLeft;

				leftBlock.scrollTop = scrollTop;
				topBlock.scrollLeft = scrollLeft;
			});

			$scope.isLoading = false;
			$scope.$apply();
		}, 150);
	}
}]);