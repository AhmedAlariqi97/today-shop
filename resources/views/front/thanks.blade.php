@extends('front.layout.app')

@section('content')

<section class="container">
    <div class="col-md-12 text-center py-5">

        @if (Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
        @endif

        <h2>Thank You!</h2>
        <p>Your Order ID is: {{ $id }}</p>
    </div>
</section>

@endsection


@section('customjs')

@endsection
