@extends('layouts.app')

@section('content')
    <section class="innerhead">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 bannercont animate zoomIn text-center" data-anim-type="zoomIn" data-anim-delay="300">
                        <div class="center">
                            <h1>Password Reset</h1>
                        </div>
                        <div class="breadcrum-sitemap">
                            <ul>
                                <li><a href="/">Home</a></li>
                                <li><a href="javascript:void(0);">Password Reset</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .show{
            display:block;
        }
    </style>
    <div class="bootstrap-5 container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <!--<div class="card-header text-center">{{ __('Reset Password') }}</div>-->

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        
                            <script>
                                setTimeout(function() {
                                    let alert = document.querySelector('.alert');
                                    if (alert) {
                                        alert.classList.remove('show');
                                        alert.classList.add('fade');
                                        setTimeout(() => alert.remove(), 150);
                                    }
                                }, 3000);
                            </script>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" placeholder="Enter Email Here" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ auth()->check() ? auth()->user()->email : old('email') }}" 
                                           required autocomplete="email" autofocus>


                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Send Reset Password Link') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
