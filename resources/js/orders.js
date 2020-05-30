angular.module('tctApp').controller('OrdersController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'toastr',
	'ProductsRepository',
	'OrdersRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		toastr,
		ProductsRepository,
		OrdersRepository
	){

	$scope.Math = window.Math;

	$scope.baseUrl = '';

	$scope.currentStatus = 0;
	$scope.currentOrder = null;

	$scope.orders = [];
	$scope.order = {
		'cost': 0,
		'paid': 0,
		'weight': 0,
		'pallets': 0,
		'priority': '1',
		'delivery': '',
		'delivery_distance': 0,
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


	$scope.init = function(status)
	{
		$scope.chooseStatus(status);
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

		OrdersRepository.get({id: $scope.id}, function(response) 
		{
			$scope.order = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'orders';

		$scope.id = $routeParams['id'];

		ProductsRepository.query(function(response) 
		{
			$scope.productGroups = response;
		});

		if ($scope.id)
		{
			OrdersRepository.get({id: $scope.id}, function(response) 
			{
				$scope.order = response;

				for (product of $scope.order.products)
				{
					$scope.chooseProductGroup(product, product.product_group_id);
					$scope.chooseProduct(product, product);
				}
			});
		}
		else
		{
			$scope.addProduct();
		}
	}


	$scope.save = function() 
	{
		OrdersRepository.save({id: $scope.id}, $scope.order, function(response) 
		{
			toastr.success($scope.id ? 'Заказ успешно обновлен!' : 'Новый заказ успешно создан!');

			$scope.orderErrors = {};
			$scope.id = response.id;
			$scope.order.url = response.url;
		}, 
		function(response) 
		{
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

				$scope.init();
			}
		}, 
		function(response) 
		{
        	toastr.error('Произошла ошибка на сервере');
        });
	}


	$scope.loadOrders = function()
	{
		OrdersRepository.query({'status': $scope.currentStatus}, function(response) 
		{
			$scope.orders = response;
		});
	}


	$scope.chooseStatus = function(status)
	{
		$scope.currentStatus = status;

		$scope.loadOrders();
	}


	$scope.chooseOrder = function(order)
	{
		console.log(order);
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


	$scope.chooseProductGroup = function(productData, productGroup)
	{
		productData.id = null;
		productData.category = productGroup.category;
		productData.weight_unit = productGroup.weight_unit;
		productData.unit_in_units = productGroup.unit_in_units;
		productData.units_in_pallete = productGroup.units_in_pallete;

		for (key in $scope.productGroups)
		{
			if ($scope.productGroups[key].id == productGroup.id)
			{
				productData.products = $scope.productGroups[key].products;
				break;
			}
		}

		if (!productGroup.category.variations)
		{
			$scope.chooseProduct(productData, productData.products[0]);
		}

		$scope.updateOrderInfo();
	}


	$scope.chooseProduct = function(productData, product)
	{
		productData.id = product.id;
		productData.in_stock = product.in_stock;
		productData.free_in_stock = product.free_in_stock;
		productData.units_text = product.units_text;
		productData.pivot.price = product.price;
		productData.pivot.cost = product.price * productData.pivot.count;

		$scope.updateOrderInfo();
	}


	$scope.updateCount = function(product) 
	{
		if (product.pivot.count.length > 10)
		{
			product.pivot.count = product.pivot.count.substring(0, 10);
		}
		product.pivot.count = product.pivot.count.replace(/[^,.\d]/g, '');
		product.pivot.count = product.pivot.count.replace(',', '.');

		if (product.pivot.count.split('.').length - 1 > 1)
		{
			var index = product.pivot.count.lastIndexOf('.');
			var count = product.pivot.count.substring(0, index);
			product.pivot.count = product.pivot.count.substring(index).replace('.', '');
			product.pivot.count = count + product.pivot.count;
		}

		product.pivot.cost = product.pivot.price * product.pivot.count;

		$scope.updateOrderInfo();
    }


	$scope.updateOrderInfo = function(pallets) 
	{
		$scope.order.cost = 0;
		$scope.order.weight = 0;
		if (!pallets)
		{
			$scope.order.pallets = 0;
		}

		$scope.order.main_category = '';

		for (product of $scope.order.products) 
		{
			if (product.pivot.price)
			{
				$scope.order.cost += product.pivot.cost;
				$scope.order.weight += product.weight_unit * product.unit_in_units * product.pivot.count;
				if (!pallets)
				{
					$scope.order.pallets += Math.ceil(product.pivot.count / product.units_in_pallete); 
				}

				if (product.category)
				{
					$scope.order.main_category = product.category.main_category;
				}
			}
		}

		$scope.order.cost += $scope.order.pallets * 150;

		if ($scope.order.delivery)
		{
			switch ($scope.order.delivery) {
				case ('sverdlovsk'):
					$scope.order.cost += 2500;
				    break;

				case ('other'):
					$scope.order.cost += 3000;
					break;
			}

			if ($scope.order.delivery_distance)
			{
				$scope.order.cost += 50 * $scope.order.delivery_distance;
			}
		}

		$scope.order.cost = Math.ceil($scope.order.cost);
		$scope.order.weight = Math.ceil($scope.order.weight);
    }


    $scope.isRealizationModalShown = false;
    $scope.modalOrder = {};
    $scope.isAllRealizationsChosen = false;


    $scope.showRealizationModal = function(order)
    {
    	$scope.modalOrder = order || $scope.order;
    	$scope.isRealizationModalShown = true;

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
    		for (realization of $scope.modalOrder.realizations)
    		{
    			realization.performed = realization.planned;
    		}
    	}
    }

    $scope.checkAllRealizations = function(realization) 
    {
    	if (realization.performed > realization.planned)
		{
			realization.performed = realization.planned
		}

    	for (realization of $scope.modalOrder.realizations)
		{
			if (realization.performed < realization.planned)
			{
				$scope.isAllRealizationsChosen = false;
				return;
			}
		}
		$scope.isAllRealizationsChosen = true;
    }


    $scope.saveRealization = function()
    {
    	OrdersRepository.saveRealization({'realizations': $scope.modalOrder.realizations}, function(response) 
		{
			$scope.successTopAlert = 'Все изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);


			if (!$scope.baseUrl)
			{
				$scope.init();
			}


			$scope.hideRealizationModal();
		});
    }
}]);