<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foookat Online Services</title>
    <link href="{{ URL::asset('css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/animate/animate.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/inspinia/style.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/custom/style.css') }}" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="orange-overlay">
    <div class="middle-box text-center animated fadeInDown">
        <div>
            <img class="img-responsive" src="{{ URL::asset('images/logo/foookat_logo_alpha.png') }}"/>
            <h4>Welcome to Foookat</h4>
            <p style="color:white;">{{ $message }}</p>
            <p class="m-t">
                <small>&copy; Copyrights Foookat Online Services Pvt. Ltd. 2016</small>
            </p>
        </div>
    </div>
</div>
<!-- Mainly scripts -->
<script src="{{ URL::asset('js/jquery/jquery-2.1.1.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap/bootstrap.min.js') }}"></script>
</body>
</html>