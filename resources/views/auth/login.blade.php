@extends('layouts.auth')
@section('title', __('lang_v1.login'))

@section('content')
<div class="container">
    <div class="row">
        <h1 class="text-center  page-header">{{ config('app.name', 'ultimatePOS') }}</h1>
        @if(env('ALLOW_REGISTRATION', true))
            <div class="header-right-div">
                {{ __('business.not_yet_registered')}} <a href="{{ route('business.getRegister') }}">{{ __('business.register_now') }}</a>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('lang_v1.login')</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">@lang('lang_v1.username')</label>

                            <div class="col-md-6">
                                @php
                                    $username = old('username');
                                    $password = null;
                                    if(config('app.env') == 'demo'){
                                        $username = 'admin';
                                        $password = '123456';

                                        $demo_types = array(
                                            'all_in_one' => 'admin',
                                            'super_market' => 'admin',
                                            'pharmacy' => 'admin-pharmacy',
                                            'electronics' => 'admin-electronics',
                                            'services' => 'admin-services',
                                            'restaurant' => 'admin-restaurant',
                                            'superadmin' => 'superadmin'
                                        );
                                        if( !empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types) ){
                                            $username = $demo_types[$_GET['demo_type']];
                                        }
                                    }
                                @endphp
                                <input id="username" type="text" class="form-control" name="username" value="{{ $username }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">@lang('lang_v1.password')</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password"
                                value="{{ $password }}" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('lang_v1.remember_me')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('lang_v1.login')
                                </button>
                                @if(config('app.env') != 'demo')
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    @lang('lang_v1.forgot_your_password')
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(config('app.env') == 'demo')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Demo Shops <small><i> Demos are for example purpose only, Ultimate POS <u>can be used in many other similar businesses.</u></i></small></h4></div>
                <div class="panel-body">
                    <div class="col-md-12 text-center">
                                <a href="?demo_type=all_in_one" class="btn btn-app bg-olive" data-toggle="tooltip" title="Showcases all feature available in the application." >
                                    <i class="fa fa-star"></i>
                                All In One</a>
                                <a href="?demo_type=pharmacy" class="btn bg-maroon btn-app" data-toggle="tooltip" title="Shops with products having expiry dates." >
                                <i class="fa fa-medkit"></i>
                                Pharmacy</a>
                                <a href="?demo_type=services" class="btn bg-orange btn-app" data-toggle="tooltip" title="For all service providers like Web Development, Restaurants, Repairing, Plumber, Salons, Beauty Parlors etc.">
                                <i class="fa fa-wrench"></i>
                                Multi-Service Center</a>
                                <a href="?demo_type=electronics" class="btn bg-purple btn-app" data-toggle="tooltip" title="Products having IMEI or Serial number code." >
                                <i class="fa fa-laptop"></i>
                                Electronics & Mobile Shop</a>
                                <a href="?demo_type=super_market" class="btn bg-navy btn-app" data-toggle="tooltip" title="Super market & Similar kind of shops." >
                                <i class="fa fa-shopping-cart"></i>
                                Super Market</a>
                                <a href="?demo_type=restaurant" class="btn bg-red btn-app" data-toggle="tooltip" title="Restaurants, Salons and other similar kind of shops." >
                                <i class="fa fa-cutlery"></i>
                                Restaurant</a>
                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a href="?demo_type=superadmin" class="btn bg-red-active btn-app" data-toggle="tooltip" title="SaaS & Superadmin extension Demo">
                            <i class="fa fa-university"></i>
                            SaaS / Superadmin*</a>
                            <p class="help-block"><span class="text-danger">*</span> SaaS / Superadmin module is an Premium extension to UltimatePOS.</p>
                    </div>
                </div>
            </div>
         </div>
    </div>           
    @endif
</div>
@endsection
