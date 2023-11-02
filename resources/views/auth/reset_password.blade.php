@extends('front.layout.app')

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Reset Password</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
                @if (Session::has('success'))
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {!! Session::get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif
                @if (Session::has('error'))
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {!! Session::get('error') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif
            <div class="login-form">
                <form action="{{ route('auth.processResetPassword')}}" method="post">
                @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <h4 class="modal-title">Reset Your Password</h4>
                    <div class="form-group">
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password" id="new_password" name="new_password">
                        @error('new_password')
                               <p class="invalid-feedback">
                                {{ $message }}
                               </p>
                            @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation">
                        @error('password_confirmation')
                               <p class="invalid-feedback">
                                {{ $message }}
                               </p>
                            @enderror
                    </div>

                    <button type="submit" class="btn btn-dark btn-block btn-lg">Submit</button>
                </form>
                <div class="text-center small">Do you have an account? <a href="{{ route('auth.login') }}">Login</a></div>
            </div>
        </div>
    </section>
</main>


@endsection


@section('customjs')

@endsection
