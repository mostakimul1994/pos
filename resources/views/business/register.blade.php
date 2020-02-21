@extends('layouts.auth')
@section('title', __('lang_v1.register'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <h1 class="text-center  page-header">{{ config('app.name', 'POS') }}</h1>
        <div class="header-right-div">
            {{ __('business.already_registered')}} <a href="{{ action('Auth\LoginController@login') }}">{{ __('business.sign_in') }}</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title text-center">@lang('business.register_and_get_started_in_minutes')</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            {!! Form::open(['url' => route('business.postRegister'), 'method' => 'post', 
                            'id' => 'business_register_form','files' => true ]) !!}
                                @include('business.partials.register_form')
                            {!! Form::close() !!}
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection