<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foookat Online Services</title>
    <link href="{{ URL::asset('css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/animate/animate.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/inspinia/style.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/custom/style.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/plugins/bootstrap-toggle/bootstrap-toggle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/plugins/jqTimepicker/jquery.timepicker.css') }}" rel="stylesheet">
</head>
<body class="pace-done skin-1">
<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header"
                    style="background: url({{ URL::asset('images/patterns/header-profile-skin-1.png') }})">
                    <div class="dropdown profile-element">
                        <span>
                            <img alt="image" class="img-table" height="50" width="50"
                                 @if (Auth::user()->profile_picture) src="{{ Auth::user()->profile_picture }}"
                                 @else src="{{ URL::asset('images/uploads/avatars/default.png') }}" @endif >
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block text-xs m-t-xs"> <strong
                                        class="font-bold">{{ Auth::user()->name }}</strong><b class="caret"></b>
                        </span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="#">Profile</a></li>
                            <li><a href="{{ url('/logout') }}">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        <img alt="image" class="img-table" height="50" width="50"
                             @if (Auth::user()->profile_picture) src="{{ Auth::user()->profile_picture }}"
                             @else src="{{ URL::asset('images/uploads/avatars/default.png') }}" @endif >
                    </div>
                </li>

                @include('partials.navigation.navigation')

            </ul>
        </div>
    </nav>
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i>
                    </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <a href="{{ url('/logout') }}">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        @yield('content')

        <div class="footer">
            <div>
                <strong>&copy; Copyrights</strong> Foookat Online Services 2016
            </div>
        </div>
    </div>
</div>
<!-- Mainly scripts -->
<script src="{{ URL::asset('js/jquery/jquery-2.1.1.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/inspinia/inspinia.js') }}"></script>
<script src="{{ URL::asset('js/custom/main.js') }}"></script>
<script src="{{ URL::asset('js/google-places/places.js') }}"></script>
<script src="{{ URL::asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ URL::asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/pace/pace.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/bootstrap-toggle/bootstrap-toggle.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jqTimepicker/jquery.timepicker.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyC0dxOmaA4-s1ad3H_Ci3qB9X-A6uJlqz0&libraries=places&callback=initAutocomplete"></script>
</body>
</html>

