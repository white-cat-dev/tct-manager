<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TCT Manager</title>

        <!-- Fonts -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> -->

        <!-- Styles -->
        <link rel="stylesheet" type="text/css" href="{{ mix('/css/app.css') }}">
        
    </head>
    <body ng-app="tctApp">
        @include('partials.header')

        <div class="fluid-container">
            <div class="row">
                <div class="col-2">
                    @include('partials.main-menu')
                </div>
                <div class="col-10">
                    <div class="content-block">
                        <div ng-view ng-cloak>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')

        <!-- Scripts -->
        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="https://kit.fontawesome.com/7747b04567.js" crossorigin="anonymous"></script>
    </body>
</html>
