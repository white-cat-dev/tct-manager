<div class="date-pagination-block" ng-if="lastPage >= 0">
	<div class="date-groups">
		<div class="input-group date-group">
			<button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.year = currentDate.year - 1; loadOrders()" ng-disabled="currentDate.year == years[0]">
			    <i class="fas fa-chevron-left"></i>
			</button>

			<ui-select ng-model="currentDate.year" ng-change="loadOrders()" skip-focusser="true" search-enabled="false">
	            <ui-select-match placeholder="Год">
		            <span ng-bind-html="$select.selected"></span>
		        </ui-select-match>
	            <ui-select-choices repeat="year in years">
	                <span ng-bind-html="year"></span>
	            </ui-select-choices>
			</ui-select>

			<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.year = currentDate.year + 1; loadOrders()" ng-disabled="currentDate.year == years[years.length - 1]">
			    <i class="fas fa-chevron-right"></i>
			</button>
		</div>

		<div class="input-group date-group">
		    <button class="btn btn-primary input-group-prepend" type="button" ng-click="currentDate.month = currentDate.month - 1; loadOrders()" ng-disabled="currentDate.month == monthes[0].id">
			    <i class="fas fa-chevron-left"></i>
			</button>

			<ui-select ng-model="currentDate.month" ng-change="loadOrders()" skip-focusser="true" search-enabled="false">
	            <ui-select-match placeholder="Месяц">
		            <span ng-bind-html="$select.selected.name"></span>
		        </ui-select-match>
	            <ui-select-choices repeat="month.id as month in monthes">
	                <span ng-bind-html="month.name"></span>
	            </ui-select-choices>
			</ui-select>

			<button class="btn btn-primary input-group-append" type="button" ng-click="currentDate.month = currentDate.month + 1; loadOrders()" ng-disabled="currentDate.month == monthes[monthes.length - 1].id">
			    <i class="fas fa-chevron-right"></i>
			</button>
		</div>
	</div>

	<ul class="pagination">
		<li>
			Страницы: 
		</li>
       {{--  <li class="previous">
        	<button class="btn btn-primary" type="button" ng-click="choosePage(currentPage - 1)" ng-disabled="currentPage == 1">
			    <i class="fas fa-chevron-left"></i>
			</button>
        </li> --}}
        <li ng-repeat="x in [].constructor(lastPage) track by $index">
			<button class="btn" type="button" ng-class="{'active': $index + 1 == currentPage}" ng-click="choosePage($index + 1)">
				@{{ $index + 1 }}
			</button>
        </li>
		{{-- <li class="next">
        	<button class="btn btn-primary" type="button" ng-click="choosePage(currentPage + 1)" ng-disabled="currentPage == lastPage">
			    <i class="fas fa-chevron-right"></i>
			</button>
        </li> --}}
    </ul>
</div>