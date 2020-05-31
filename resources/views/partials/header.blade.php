<header>
	<nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="far fa-calendar-check"></i> ТСТ
            </a>

            <button class="btn main-menu-toggler" type="button" onclick="document.querySelector('body').classList.toggle('main-menu-open')">
                <span><i class="fas fa-times"></i></span>
                <span><i class="fas fa-bars"></i></span>
            </button>

            <ul class="navbar-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Вход
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <button type="button" class="btn nav-link" ng-click="logout()">
                            <i class="fas fa-sign-out-alt"></i> Выход
                        </button>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
</header>