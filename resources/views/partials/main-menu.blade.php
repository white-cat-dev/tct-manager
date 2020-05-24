@inject('menuService', 'App\Services\MenuService')

<div class="main-menu-block">
	<div class="container">
		@foreach ($menuService->getItems() as $menuItem)
			<div class="menu-item @if (!empty($menuItem->submenu)) has-submenu @endif" @if (!empty($menuItem->submenu)) ng-class="{ 'submenu-opened': submenuOpened }" ng-init="submenuOpened = {{ !empty($menuItem->submenu_opened) }}" @endif>
				@if (empty($menuItem->submenu))
				<a href="{{ $menuItem->url }}" onclick="document.querySelector('body').classList.remove('main-menu-open');">
					<span>{!! $menuItem->icon !!}</span>
					{{ $menuItem->name }}
				</a>

				@else
					<div class="link" ng-click="submenuOpened = !submenuOpened">
						<span>{!! $menuItem->icon !!}</span>
						{{ $menuItem->name }}
						
						<div class="toggle">
							<i class="fas fa-caret-down"></i>
						</div>
					</div>

					
					<div class="submenu-block">
						@foreach ($menuItem->submenu as $submenuItem)
							<div class="submenu-item">
								<a href="{{ $submenuItem->url }}" onclick="document.querySelector('body').classList.remove('main-menu-open');">
									<span>{!! $submenuItem->icon !!}</span>
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