@extends('front.layout.app')

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Forgot Password</li>
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
                <form action="{{ route('auth.authenticate')}}" method="post">
                @csrf
                    <h4 class="modal-title">Forgot Your Password</h4>
                    <div class="form-group">
                        <input type="text" value="{{ old('email') }}" name="email" id="email"
                             class="form-control @error('email') is-invalid @enderror" placeholder="Email">

                             @error('email')
                               <p class="invalid-feedback">
                                {{ $message }}
                               </p>
                            @enderror
                    </div>

                    <button type="submit" class="btn btn-dark btn-block btn-lg">Confirm Email</button>
                </form>
                <div class="text-center small">Do you have an account? <a href="{{ route('auth.login') }}">Login</a></div>
            </div>
        </div>
    </section>
</main>


@endsection


@section('customjs')
@endsection
