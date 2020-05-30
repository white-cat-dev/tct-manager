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
			$scope.recipe = response;
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


	$scope.showDelete = function(recipe)
	{
		$scope.isDeleteModalShown = true;
		$scope.deleteType = 'recipe';
		$scope.deleteData = recipe;

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

		RecipesRepository.delete({id: id}, function(response) 
		{
			if ($scope.baseUrl)
			{
				$location.path($scope.baseUrl).replace();
			}
			else
			{
				toastr.success('Рецепт успешно удален!');

				$scope.init();
			}
		}, 
		function(response) 
		{
        	toastr.error('Произошла ошибка на сервере');
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