angular.module('tctApp').controller('OrdersController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'$filter',
	'toastr',
	'ProductsRepository',
	'OrdersRepository',
	'ExportsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		$filter,
		toastr,
		ProductsRepository,
		OrdersRepository,
		ExportsRepository
	){

	$scope.Math = window.Math;

	$scope.baseUrl = '';

	$scope.currentStatus = 0;
	$scope.currentMainCategory = ['tiles', 'blocks'];
	$scope.currentOrder = null;

	$scope.orders = [];
	$scope.order = {
		'cost': 0,
		'paid': 0,
		'pay_type': 'cash',
		'weight': 0,
		'pallets': 0,
		'pallets_price': 150,
		'priority': '1',
		'delivery': '',
		'delivery_distance': 0,
		'delivery_price': 0,
		'products': []
	};
	$scope.id = 0;

	$scope.orderErrors = {};

	$scope.productGroups = [];

	$scope.units = {
		'area': [
			{
				'key': 'unit',
				'name': 'шт.'
			},
			{
				'key': 'units',
				'name': 'м<sup>2</sup>'
			},
			{
				'key': 'pallete',
				'name': 'поддон'
			},
		],
		'volume': [
			{
				'key': 'unit',
				'name': 'шт.'
			},
			{
				'key': 'units',
				'name': 'м<sup>3</sup>'
			},
			{
				'key': 'pallete',
				'name': 'поддон'
			},
		],
		'length': [
			{
				'key': 'units',
				'name': 'шт.'
			},
			{
				'key': 'pallete',
				'name': 'поддон'
			},
		]
	};


	$scope.palletsPrices = {
		'cash': 150,
		'cashless': 160,
		'vat': 175
	};


	$scope.init = function(status)
	{
		$scope.chooseStatus(status);
		console.log(status);
		$scope.loadOrders();
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'orders';

		$scope.id = $routeParams['id'];

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});

		$scope.isLoading = true;

		OrdersRepository.get({id: $scope.id}, function(response) 
		{
			$scope.order = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'orders';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.isLoading = true;

			OrdersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.order = response;

				$scope.order.manual_pallets = $scope.order.pallets;
				$scope.order.manual_pallets_price = $scope.order.pallets_price;
				$scope.order.manual_delivery_price = $scope.order.delivery_price;

				if ($scope.order.date)
				{
					var date = $scope.order.date.split("-");
					$scope.order.date_raw = date[2] + date[1] + date[0];
				}

				if ($scope.order.date_to)
				{
					var dateTo = $scope.order.date_to.split("-");
					$scope.order.date_to_raw = dateTo[2] + dateTo[1] + dateTo[0];
				}

				if ($scope.order.priority)
				{
					$scope.order.priority = '' + $scope.order.priority;
				}

				for (payment of $scope.order.payments)
				{
					var date = payment.date.split("-");
					payment.date_raw = date[2] + date[1] + date[0];
				}

				for (realization of $scope.order.realizations)
				{
					var date = realization.date.split("-");
					realization.date_raw = date[2] + date[1] + date[0];
				}

				ProductsRepository.query(function(response) 
				{
					$scope.isLoading = false;

					$scope.productGroups = response;
					
					for (product of $scope.order.products)
					{
						product.pivot.manual_price = product.pivot.price;

						var chosenProductGroup = null;
						for (productGroup of $scope.productGroups)
						{
							if (productGroup.id == product.product_group_id)
							{
								chosenProductGroup = productGroup;
								break;
							}
						}

						if (chosenProductGroup) 
						{
							$scope.chooseProductGroup(product, chosenProductGroup, product);
						}
					}
				});
			});
		}
		else
		{
			$scope.order.date_raw = $filter('date')(new Date, 'ddMMyyyy');
			$scope.order.date_to_raw = $filter('date')(new Date, 'ddMMyyyy');

			ProductsRepository.query(function(response) 
			{
				$scope.productGroups = response;
			});

			$scope.addProduct();
		}
	}


	$scope.save = function() 
	{
		$scope.isSaving = true;


		if ($scope.currentOrder)
		{
			$scope.id = $scope.currentOrder.id;
			$scope.order = $scope.currentOrder;
			$scope.baseUrl = 'orders';
			$scope.order['production'] = true;
		}

		OrdersRepository.save({id: $scope.id}, $scope.order, function(response) 
		{
			$scope.isSaving = false;

			toastr.success($scope.id ? 'Заказ успешно обновлен!' : 'Новый заказ успешно создан!');

			$location.path($scope.baseUrl).replace();

			if ($scope.currentOrder)
			{
				$scope.init('production');
			}

			// $scope.orderErrors = {};
			// $scope.id = response.id;
			// $scope.order.url = response.url;
		}, 
		function(response) 
		{
            $scope.isSaving = false;

            switch (response.status) 
            {
            	case 422:
            		toastr.error('Проверьте введенные данные');
            		$scope.orderErrors = response.data.errors;
            		break

            	default:
            		toastr.error('Произошла ошибка на сервере');
            		break;
            }
        });
	}


	$scope.getDate = function() 
	{
		$scope.isAddSaving = true;

		OrdersRepository.getDate($scope.order, function(response) 
		{
			$scope.isAddSaving = false;

			var dateTo = response.date.split("-");
			$scope.order.date_to_raw = dateTo[2] + dateTo[1] + dateTo[0];

			toastr.success('Дата готовности заказа успешно рассчитана!');
		}, 
		function(response) 
		{
            $scope.isAddSaving = false;

            switch (response.status) 
            {
            	default:
            		toastr.error('Произошла ошибка на сервере');
            		break;
            }
        });
	}


	$scope.showDelete = function(order)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'order';
		$scope.deleteData = order;

		document.querySelector('body').classList.add('modal-open');
	}


	$scope.hideDelete = function()
	{
		$scope.isDeleteModalShown = false;

		document.querySelector('body').classList.remove('modal-open');
	}


	$scope.delete = function(id)
	{
		$scope.hideDelete();

		OrdersRepository.delete({id: id}, function(response) 
		{
			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				toastr.success('Заказ успешно удален!');

				$scope.loadOrders();
			}
		}, 
		function(response) 
		{
        	toastr.error('Произошла ошибка на сервере');
        });
	}


	$scope.loadOrders = function()
	{
		$scope.isLoading = true;

		var request = {
			'main_category': $scope.currentMainCategory.join(',')
		};

		if ($scope.currentStatus)
		{
			request.status = $scope.currentStatus;
		}

		OrdersRepository.query(request, function(response) 
		{
			$scope.isLoading = false;

			$scope.orders = response;

			if ($scope.currentOrder)
			{
				for (order of $scope.orders)
				{
					if (order.id == $scope.currentOrder.id)
					{
						$scope.currentOrder = order;
						break;
					}
				}
			}
			// window.scrollTo(0, 0);
		}, 
		function(response) 
		{
            $scope.isLoading = false;
            toastr.error('Произошла ошибка на сервере');
        });
	}


	$scope.chooseStatus = function(status)
	{
		$scope.currentStatus = status;

		$scope.loadOrders();
	}


	$scope.chooseMainCategory = function(category)
	{
		var index = $scope.currentMainCategory.indexOf(category);
		if (index !== -1)
		{
			$scope.currentMainCategory.splice(index, 1);
		}
		else
		{
			$scope.currentMainCategory.push(category);
		}

		$scope.loadOrders();
	}


	$scope.chooseOrder = function(order)
	{
		if ($scope.currentOrder && $scope.currentOrder.id == order.id)
		{
			$scope.currentOrder = null;
		}
		else
		{
			$scope.currentOrder = order;
		}
	}


	$scope.addProduct = function()
	{
		$scope.order.products.push({
			'id': null,
			'products': [],
			'pivot': {
				'price': 0,
				'count': 0,
				'cost': 0
			}
		});
	}


	$scope.deleteProduct = function(index)
	{
		$scope.order.products.splice(index, 1);

		$scope.updateOrderInfo();
	}


	$scope.chooseProductGroup = function(productData, productGroup, product)
	{
		if (!product)
		{
			productData.id = null;
			productData.product_id = null;
			productData.pivot.price = 0;
		}

		productData.category = productGroup.category;
		productData.weight_unit = productGroup.weight_unit;
		productData.unit_in_units = productGroup.unit_in_units;
		productData.units_in_pallete = productGroup.units_in_pallete;

		productData.products = productGroup.products;

		if (product)
		{
			$scope.chooseProduct(productData, product);
		}
		else if (!productGroup.category.variations)
		{
			$scope.chooseProduct(productData, productData.products[0]);
		}

		$scope.updateOrderInfo();
	}


	$scope.chooseProduct = function(productData, product)
	{
		productData.id = product.id;
		productData.product_id = product.id;
		productData.in_stock = product.in_stock;
		productData.free_in_stock = product.free_in_stock;
		productData.units_text = product.units_text;
		productData.price = product.price;
		productData.price_cashless = product.price_cashless;
		productData.price_vat = product.price_vat;

		$scope.updateOrderInfo();
	}


	$scope.updateOrderInfo = function() 
	{
		$scope.order.cost = 0;
		$scope.order.weight = 0;
		$scope.order.pallets = 0;
		$scope.order.main_category = '';

		$scope.isAllRealizationsActive = true;

		for (product of $scope.order.products) 
		{
			if (!product.id)
			{
				continue;
			}

			switch ($scope.order.pay_type) 
			{
				case ('cash'):
					product.pivot.price = product.price;
					break;

				case ('cashless'):
					product.pivot.price = product.price_cashless;
					break;

				case ('vat'):
					product.pivot.price = product.price_vat;
					break;
			}

			if (product.pivot.manual_price !== undefined)
			{
				product.pivot.price = product.pivot.manual_price;
			}

			product.pivot.cost = Math.round(product.pivot.price * product.pivot.count * 100) / 100;

			$scope.order.cost += product.pivot.cost;
			$scope.order.weight += product.weight_unit * product.unit_in_units * product.pivot.count;
			$scope.order.pallets += product.units_in_pallete ? Math.ceil(product.pivot.count / product.units_in_pallete) : 0; 

			if (product.category)
			{
				$scope.order.main_category = product.category.main_category;
			}

			if (product.pivot.count > product.in_stock)
			{
				$scope.isAllRealizationsActive = false;
				$scope.order.all_realizations = false;
			}
		}

		$scope.order.pallets_price = $scope.palletsPrices[$scope.order.pay_type];

		if ($scope.order.manual_pallets_price !== undefined)  
		{
			$scope.order.pallets_price = $scope.order.manual_pallets_price;
		}

		if ($scope.order.manual_pallets !== undefined)
		{
			$scope.order.pallets = $scope.order.manual_pallets;
		}

		$scope.order.cost += $scope.order.pallets * $scope.order.pallets_price;

		if ($scope.order.delivery)
		{
			switch ($scope.order.delivery) {
				case ('sverdlovsk'):
					$scope.order.delivery_price = 2500;
				    break;

				case ('other'):
					$scope.order.delivery_price = 3000;
					break;
			}

			if ($scope.order.delivery_distance)
			{
				$scope.order.delivery_price += 50 * $scope.order.delivery_distance;
			}
		}
		else
		{
			$scope.order.delivery_price = 0;
		}

		if ($scope.order.manual_delivery_price !== undefined)
		{
			$scope.order.delivery_price = $scope.order.manual_delivery_price;
		}

		$scope.order.cost += $scope.order.delivery_price;

		$scope.order.cost = Math.ceil($scope.order.cost);
		$scope.order.weight = Math.ceil($scope.order.weight);

		console.log($scope.isFullPaymentChosen);
		if ($scope.isFullPaymentChosen)
		{
			$scope.order.paid = $scope.order.cost;
		}
    }


    $scope.isRealizationModalShown = false;
    $scope.modalOrder = null;
    $scope.isAllRealizationsChosen = false;


    $scope.showRealizationModal = function(order)
    {
    	$scope.modalOrder = order || $scope.order;
    	$scope.modalOrder.realization_date_raw = $filter('date')(new Date(), 'ddMMyyyy');
    	$scope.modalOrder.disabled_realizations = true;
    	$scope.isRealizationModalShown = true;

    	$scope.modalOrder.realizations = [];

    	for (product of $scope.modalOrder.products) 
    	{
    		var planned = Math.round((product.progress.total - product.progress.realization) * 100) / 100;
    		var maxPerformed = (product.in_stock < planned) ? product.in_stock : planned;

    		$scope.modalOrder.realizations.push({
    			'order_id': $scope.modalOrder.id, 
    			'product': product,
    			'planned': planned,
    			'performed': 0,
    			'max_performed': maxPerformed
    		});

    		if (maxPerformed > 0)
    		{
    			$scope.modalOrder.disabled_realizations = false;
    		}
    	}

    	console.log($scope.modalOrder.realizations);

    	$scope.isAllRealizationsChosen = false;
    }


    $scope.hideRealizationModal = function()
    {
    	$scope.isRealizationModalShown = false;
    }


    $scope.chooseAllRealizations = function()
    {
    	if ($scope.isAllRealizationsChosen)
    	{
    		console.log($scope.modalOrder.realizations);
    		for (realization of $scope.modalOrder.realizations)
    		{
    			realization.performed = realization.max_performed;
    		}
    	}
    }

    $scope.checkAllRealizations = function(realization) 
    {
    	if (realization.performed > realization.max_performed)
		{
			realization.performed = realization.max_performed;
		}

    	for (realization of $scope.modalOrder.realizations)
		{
			if (realization.performed < realization.max_performed)
			{
				$scope.isAllRealizationsChosen = false;
				return;
			}
		}
		$scope.isAllRealizationsChosen = true;
    }


    $scope.saveRealization = function()
    {
    	for (realization of $scope.modalOrder.realizations)
    	{
    		realization.date_raw = $scope.modalOrder.realization_date_raw;
    	}

    	$scope.isSaving = true;
    	OrdersRepository.saveRealization({'realizations': $scope.modalOrder.realizations}, function(response) 
		{
			$scope.isSaving = false;

			toastr.success('Выдача заказа успешно сохранена!');

			if (!$scope.baseUrl)
			{
				$scope.loadOrders();
			}

			$scope.hideRealizationModal();
		}, 
		function(response) 
		{
            $scope.isSaving = false;
            toastr.error('Произошла ошибка на сервере');
        });
    }



    $scope.isPaymentModalShown = false;
    $scope.modalPayment = null;
    $scope.isFullPaymentChosen = false;


    $scope.showPaymentModal = function(order)
    {
    	$scope.modalOrder = order || $scope.order;
    	$scope.modalPayment = {
    		'order_id': $scope.modalOrder.id,
    		'date_raw': $filter('date')(new Date(), 'ddMMyyyy'),
    		'paid': 0
    	}
    	$scope.isPaymentModalShown = true;

    	$scope.isFullPaymentChosen = false;
    }


    $scope.hidePaymentModal = function()
    {
    	$scope.isPaymentModalShown = false;
    }


    $scope.chooseFullPayment = function()
    {
    		console.log(111);
    	if ($scope.isFullPaymentChosen)
    	{
    		if ($scope.modalPayment)
    		{
    			$scope.modalPayment.paid = $scope.modalOrder.cost - $scope.modalOrder.paid;
    		}
    		else
    		{
    			console.log(345);
    			$scope.order.paid = $scope.order.cost;
    		}
    	}
    }

    $scope.checkFullPayment = function() 
    {
    	if ($scope.modalOrder)
    	{
    		$scope.isFullPaymentChosen = $scope.modalPayment.paid >= ($scope.modalOrder.cost - $scope.modalOrder.paid);
    	}
    	else
    	{
    		$scope.isFullPaymentChosen = $scope.order.paid >= $scope.order.cost;
    	}
    }


    $scope.savePayment = function()
    {
    	$scope.isSaving = true;
    	OrdersRepository.savePayment($scope.modalPayment, function(response) 
		{
			$scope.isSaving = false;

			toastr.success('Платеж успешно внесен!');

			if (!$scope.baseUrl)
			{
				$scope.loadOrders();
			}

			$scope.hidePaymentModal();
		}, 
		function(response) 
		{
            $scope.isSaving = false;
            toastr.error('Произошла ошибка на сервере');
        });
    }


    $scope.loadExportFile = function(order) 
	{
		ExportsRepository.order({'id': order.id}, function(response) 
		{
			window.open(
			 	response.file,
			 	'_blank' // <- This is what makes it open in a new window.
			);
		}, 
		function(response) 
		{
        });
	}
}]);