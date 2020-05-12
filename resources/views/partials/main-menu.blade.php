@inject('menuService', 'App\Services\MenuService')

<div class="main-menu-block">
	<div class="container">
		@foreach ($menuService->getItems() as $menuItem)
			<div class="menu-item @if (!empty($menuItem->submenu)) has-submenu @endif" @if (!empty($menuItem->submenu)) ng-class="{ 'submenu-opened': submenuOpened }" @endif>
				<a href="{{ $menuItem->url }}" onclick="document.querySelector('body').classList.remove('main-menu-open');">
					<span>{!! $menuItem->icon !!}</span>
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
</div>