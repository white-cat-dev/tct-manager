@inject('menuService', 'App\Services\MenuService')

<div class="main-menu-block">
	@foreach ($menuService->getItems() as $menuItem)
		<div class="menu-item @if (!empty($menuItem->submenu)) has-submenu @endif" @if (!empty($menuItem->submenu)) ng-class="{ 'submenu-opened': submenuOpened }" @endif>
			<a href="{{ $menuItem->url }}">
				{{ $menuItem->name }}
			</a>

			@if (!empty($menuItem->submenu))

				<div class="toggle" ng-click="submenuOpened = !submenuOpened">
					<i class="fas fa-caret-up"></i>
				</div>
				
				<div class="submenu-block">
					@foreach($menuItem->submenu as $submenuItem)
						<div class="submenu-item">
							<a href="{{ $submenuItem->url }}">
								{{ $submenuItem->name }}
							</a>
						</div>
					@endforeach
				</div>
			@endif
		</div>
	@endforeach
</div>