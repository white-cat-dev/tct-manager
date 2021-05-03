angular.module('tctApp').controller('MaterialsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$filter',
	'$timeout',
	'MaterialsRepository',
	'ExportsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$filter,
		$timeout,
		MaterialsRepository,
		ExportsRepository
	){

	$scope.baseUrl = '';

	$scope.materialGroups = [];
	$scope.materials = [];
	$scope.materialGroup = {
		'url': '#',
		'variations': '',
		'materials': [
			{
				'variation': '',
				'price': 0,
				'in_stock': 0
			}
		]
	};
	$scope.id = 0;

	$scope.materialGroupErrors = {};


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


	$scope.init = function()
	{
		$scope.isLoading = true;
		MaterialsRepository.query(function(response) 
		{
			$scope.isLoading = false;
			$scope.materialGroups = response;

			$scope.materials = [];
			for (materialGroup of $scope.materialGroups) 
			{
				for (material of materialGroup.materials) 
				{
					$scope.materials.push(material);
				}
			}
		},
		function(response)
		{
			$scope.isLoading = false;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'materials';
		$scope.id = $routeParams['id'];

		$scope.isLoading = true;
		MaterialsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.isLoading = false;
			$scope.materialGroup = response;

			$scope.initStocks();
		},
		function(response)
		{
			$scope.isLoading = false;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'materials';
		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.isLoading = true;
			MaterialsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.isLoading = false;
				$scope.materialGroup = response;
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
		MaterialsRepository.save({id: $scope.id}, $scope.materialGroup, function(response) 
		{
			$scope.isSaving = false;

			toastr.success($scope.id ? 'Материал успешно обновлен!' : 'Новый материал успешно создан!');

			$scope.materialGroupErrors = {};
			$scope.id = response.id;
			$scope.materialGroup.url = response.url;
		}, 
		function(response) 
		{
            $scope.isSaving = false;

            switch (response.status) 
            {
            	case 422:
            		$scope.materialGroupErrors = response.data.errors;
            		break
            }
        });
	}


	$scope.showDelete = function(material)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'material';
		$scope.deleteData = material;

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
		MaterialsRepository.delete({id: id}, function(response) 
		{
			$scope.isDeleting = false;

			$scope.hideDelete();

			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				toastr.success('Материал успешно удален!');

				$scope.init();
			}
		}, 
		function(response) 
		{
        	$scope.isDeleting = false;
        });
	}


	$scope.showCopy = function(material)
	{
		$scope.isCopyModalShown = true;
		$scope.copyType = 'material';
		$scope.copyData = material;

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
		MaterialsRepository.copy({id: id}, {}, function(response) 
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


	$scope.addMaterial = function()
	{
		$scope.materialGroup.materials.push({
			'variation': '',
			'price': 0,
			'in_stock': 0
		});
	}


	$scope.deleteMaterial = function(index)
	{
		$scope.materialGroup.materials.splice(index, 1);
	}


	$scope.saveEditField = function(groupNum, num, key) 
	{
		var materialGroup = $scope.materialGroups[groupNum];
		var material = materialGroup.materials[num];

		if (material[key] != material['new_' + key])
		{
			material[key] = material['new_' + key];
		}
		else
		{
			return;
		}

		MaterialsRepository.save({id: materialGroup.id}, materialGroup, function(response) 
		{
			toastr.success('Изменения успешно сохранены!');
		}, 
		function(response) 
		{
        });
	}


	$scope.loadExportFile = function () 
	{
		ExportsRepository.materials(function(response) 
		{
			document.location.href = response.file;
		}, 
		function(response) 
		{
        });
	}


	$scope.isSupplyModalShown = false;
	$scope.modalSupply = {
		'supplies': []
	};


    $scope.showSupplyModal = function(supply)
    {
    	if (supply)
    	{
    		$scope.modalSupply['supplies'] = [supply];
    		var date = supply.date.split("-");
			$scope.modalSupply.date_raw = date[2] + date[1] + date[0];
			$scope.isSupplyModalEditing = true;
    	}
    	else
    	{
    		$scope.modalSupply['supplies'] = [];
    		$scope.modalSupply.date_raw = $filter('date')(new Date(), 'ddMMyyyy'),
    		$scope.addSupplyMaterial();
    		$scope.isSupplyModalEditing = false;
    	}
    	$scope.isSupplyModalShown = true;
    }


    $scope.hideSupplyModal = function()
    {
    	$scope.isSupplyModalShown = false;
    }


	$scope.addSupplyMaterial = function()
	{
		$scope.modalSupply.supplies.push({
			'material_id': null,
			'performed': 0
		});
	}


	$scope.deleteSupplyMaterial = function(index)
	{
		$scope.modalSupply.supplies.splice(index, 1);
	}


    $scope.saveSupply = function()
    {
    	for (supply of $scope.modalSupply.supplies)
    	{
    		supply.date_raw = $scope.modalSupply.date_raw;
    	}

    	$scope.isModalSaving = true;

    	MaterialsRepository.saveSupply({'supplies': $scope.modalSupply.supplies}, function(response) 
		{
			$scope.isModalSaving = false;

			toastr.success('Все изменения успешно сохранены!');

			$scope.hideSupplyModal();

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


    $scope.monthes = [];
	$scope.years = [];

    $scope.currentDate = {};
    
    $scope.supplies = [];

    $scope.initSupplies = function()
    {
		var request = {
			'id': $scope.id
		};
		if ($scope.currentDate.year)
		{
			request.year = $scope.currentDate.year;
		}
		if ($scope.currentDate.month)
		{
			request.month = $scope.currentDate.month;
		}

    	$scope.isSuppliesLoading = true;

    	MaterialsRepository.supplies(request, function(response) 
		{
			$scope.isSuppliesLoading = false;

			$scope.monthes = response.monthes;
			$scope.years = response.years;

			$scope.currentDate.month = response.month;
			$scope.currentDate.year = response.year;

			$scope.supplies = response.supplies;
		},
		function(response) 
		{
			$scope.isSuppliesLoading = false;
		});
    }


    $scope.stocksMonthes = [];
	$scope.stocksYears = [];

    $scope.stocksCurrentDate = {};
    $scope.stocksCurrentMaterial = 0;
    
    $scope.stocks = [];

    $scope.initStocks = function()
    {
		if ($scope.stocksCurrentMaterial == 0)
		{
			$scope.stocksCurrentMaterial = $scope.materialGroup.materials[0].id;
		}

		var request = {
			'id': $scope.stocksCurrentMaterial
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

    	MaterialsRepository.stocks(request, function(response) 
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