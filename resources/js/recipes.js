angular.module('tctApp').controller('RecipesController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'toastr',
	'RecipesRepository',
	'MaterialsRepository',
	'CategoriesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		toastr,
		RecipesRepository,
		MaterialsRepository,
		CategoriesRepository
	){


	$scope.baseUrl = '';


	$scope.recipes = [];
	$scope.recipe = {
		'material_groups': [
			{
				'id': '',
				'pivot': {
					'count': 0
				}
			}
		]
	};
	$scope.id = 0;

	$scope.materialGroups = [];
	$scope.categories = [];

	$scope.recipeErrors = {};


	$scope.init = function()
	{
		RecipesRepository.query(function(response) 
		{
			$scope.recipes = response;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'recipes';

		$scope.id = $routeParams['id'];

		RecipesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.order = response;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'recipes';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			RecipesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.recipe = response;
			});
		}

		MaterialsRepository.query(function(response) 
		{
			$scope.materialGroups = response;
		});

		CategoriesRepository.query(function(response) 
		{
			$scope.categories = response;
		});
	}


	$scope.save = function() 
	{
		RecipesRepository.save({id: $scope.id}, $scope.recipe, function(response) 
		{
			toastr.success($scope.id ? 'Рецепт успешно обновлен!' : 'Новый рецепт успешно создан!');

			$scope.recipeErrors = {};
			$scope.id = response.id;
			$scope.recipe.url = response.url;
		}, 
		function(response) 
		{
            switch (response.status) 
            {
            	case 422:
            		toastr.error('Проверьте введенные данные');
            		$scope.recipeErrors = response.data.errors;
            		break

            	default:
            		toastr.error('Произошла ошибка на сервере');
            		break;
            }
        });
	}


	$scope.delete = function(id)
	{
		OrdersRepository.delete({id: id}, function(response) 
		{
			$scope.init();
		}, 
		function(response) 
		{
           
        });
	}


	$scope.addMaterialGroup = function()
	{
		$scope.recipe.material_groups.push({
			'id': '',
			'pivot': {
				'count': 0
			}
		});
	}


	$scope.deleteMaterialGroup = function(index)
	{
		$scope.recipe.material_groups.splice(index, 1);
	}
}]);