angular.module('tctApp').controller('MaterialsController', [
	'$scope',
	'$routeParams',
	'$location',
	'$filter',
	'$timeout',
	'toastr',
	'MaterialsRepository',
	'ExportsRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$filter,
		$timeout,
		toastr,
		MaterialsRepository,
		ExportsRepository
	){

	$scope.baseUrl = '';

	$scope.materialGroups = [];
	$scope.materials = [];
	$scope.materialGroup = {
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
		}
	];


	$scope.init = function()
	{
		MaterialsRepository.query(function(response) 
		{
			$scope.materialGroups = response;

			$scope.materials = [];
			for (materialGroup of $scope.materialGroups) 
			{
				for (material of materialGroup.materials) 
				{
					$scope.materials.push(material);
				}
			}
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'materials';
		$scope.id = $routeParams['id'];

		MaterialsRepository.get({id: $scope.id}, function(response) 
		{
			$scope.materialGroup = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'materials';
		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			MaterialsRepository.get({id: $scope.id}, function(response) 
			{
				$scope.materialGroup = response;
			});
		}
	}


	$scope.save = function() 
	{
		MaterialsRepository.save({id: $scope.id}, $scope.materialGroup, function(response) 
		{
			toastr.success($scope.id ? 'Материал успешно обновлен!' : 'Новый материал успешно создан!');

			$scope.materialGroupErrors = {};
			$scope.id = response.id;
			$scope.materialGroup.url = response.url;
		}, 
		function(response) 
		{
            switch (response.status) 
            {
            	case 422:
            		toastr.error('Проверьте введенные данные');
            		$scope.materialGroupErrors = response.data.errors;
            		break

            	default:
            		toastr.error('Произошла ошибка на сервере');
            		break;
            }
        });
	}


	$scope.delete = function(id)
	{
		MaterialsRepository.delete({id: id}, function(response) 
		{
			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				$scope.successAlert = 'Материал успешно удален!';
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


	$scope.saveEditField = function(key, groupNum) 
	{
		if (key == 'materials')
		{
			var materialGroup = $scope.materialGroups[groupNum]

			MaterialsRepository.save({id: materialGroup.id}, materialGroup, function(response) 
			{
				$scope.successTopAlert = 'Изменения успешно сохранены!';
				$scope.showTopAlert = true;

				$timeout(function() {
					$scope.showTopAlert = false;
				}, 2000);
			}, 
			function(response) 
			{
	        });
		}
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


    $scope.showSupplyModal = function()
    {
    	$scope.modalSupply.date = $filter('date')(new Date(), 'ddMMyyyy'),
    	$scope.addSupplyMaterial();
    	$scope.isSupplyModalShown = true;
    }


    $scope.hideSupplyModal = function()
    {
    	$scope.isSupplyModalShown = false;
    	$scope.modalSupply = {
			'supplies': []
		};
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
    		supply.date = $scope.modalSupply.date;
    	}

    	MaterialsRepository.saveSupply({'supplies': $scope.modalSupply.supplies}, function(response) 
		{
			$scope.successTopAlert = 'Все изменения успешно сохранены!';
			$scope.showTopAlert = true;

			$timeout(function() {
				$scope.showTopAlert = false;
			}, 2000);

			$scope.hideSupplyModal();
			$scope.init();
		});
    }
}]);