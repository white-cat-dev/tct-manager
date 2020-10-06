angular.module('tctApp').controller('RecipesController', [
	'$scope',
	'$routeParams',
	'$location',
	'$timeout',
	'RecipesRepository',
	'MaterialsRepository',
	'CategoriesRepository',
	function(
		$scope, 
		$routeParams,
		$location,
		$timeout,
		RecipesRepository,
		MaterialsRepository,
		CategoriesRepository
	){


	$scope.baseUrl = '';

	$scope.recipes = [];
	$scope.recipe = {
		'url': '#',
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
		$scope.isLoading = true;
		RecipesRepository.query(function(response) 
		{
			$scope.isLoading = false;
			$scope.recipes = response;
		},
		function(response) 
		{
			$scope.isLoading = false;
		});
	}


	$scope.initShow = function()
	{
		$scope.baseUrl = 'recipes';

		$scope.id = $routeParams['id'];

		$scope.isLoading = true;
		RecipesRepository.get({id: $scope.id}, function(response) 
		{
			$scope.isLoading = false;
			$scope.recipe = response;
		},
		function(response) 
		{
			$scope.isLoading = false;
		});
	}


	$scope.initEdit = function()
	{
		$scope.baseUrl = 'recipes';

		$scope.id = $routeParams['id'];

		if ($scope.id)
		{
			$scope.isLoading = true;
			RecipesRepository.get({id: $scope.id}, function(response) 
			{
				$scope.isLoading = false;
				$scope.recipe = response;
			},
			function(response) 
			{
				$scope.isLoading = false;
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
		$scope.isSaving = true;

		RecipesRepository.save({id: $scope.id}, $scope.recipe, function(response) 
		{
			$scope.isSaving = false;

			toastr.success($scope.id ? 'Рецепт успешно обновлен!' : 'Новый рецепт успешно создан!');

			$scope.recipeErrors = {};
			$scope.id = response.id;
			$scope.recipe.url = response.url;
		}, 
		function(response) 
		{
            $scope.isSaving = false;

            switch (response.status) 
            {
            	case 422:
            		$scope.recipeErrors = response.data.errors;
            		break
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
		$scope.isDeleting = true;

		RecipesRepository.delete({id: id}, function(response) 
		{
			$scope.isDeleting = false;

			$scope.hideDelete();

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
        	$scope.isDeleting = false;
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