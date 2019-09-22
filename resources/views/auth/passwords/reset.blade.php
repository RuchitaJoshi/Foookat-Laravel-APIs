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
            @if (count($errors) > 0)
                <div class="col-md-12 alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="float: left;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ route('password.reset') }}">
                {!! csrf_field() !!}
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input type="email" class="form-control" name="email" placeholder="Email Address"
                               value="{{ $email or old('email') }}">
                    </div>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input type="password" class="form-control" name="password_confirmation"
                               placeholder="Confirm Password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary block full-width m-b">
                            <i class="fa fa-btn fa-refresh"></i>Reset Password
                        </button>
                    </div>
                </div>
            </form>
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

