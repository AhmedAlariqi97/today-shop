@extends('front.layout.app')

@section('content')

<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-12">
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

                </div>
                <div class="col-md-3">
                @include('front.partials.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">My Wishlist</h2>
                        </div>
                        <div class="card-body p-4">
                        @if ($wishlists->isNotEmpty())
                            @foreach($wishlists as $wishlist)
                                <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                    <div class="d-block d-sm-flex align-items-start text-center text-sm-start">
                                        <!-- Image -->
                                                @php
                                                    $productImage = getProductImage($wishlist->product_id);
                                                @endphp

                                                @if (!empty($productImage))
                                                <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route("front.product",$wishlist->product->slug) }}"
                                                        style="width: 10rem;">
                                                    <img src="{{ asset('upload/product/small/'.$productImage->image) }}" class="img-fluid">
                                                </a>
                                                @else
                                                <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route("front.product",$wishlist->product->slug) }}" style="width: 10rem;">
                                                    <img src="{{ asset('admin-assets/img/default-150x150.png') }}" class="img-fluid">
                                                </a>
                                                @endif

                                        <div class="pt-2">
                                            <h3 class="product-title fs-base mb-2"><a href="{{ route("front.product",$wishlist->product->slug) }}">{{ $wishlist->product->title}}</a></h3>
                                            <span class="fs-lg text-accent pt-2"><strong>${{ $wishlist->product->price}}</strong></span>
                                            @if($wishlist->product->compare_price > 0)
                                            <span class="h6 text-underline"><del>${{ $wishlist->product->compare_price }}</del></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                        <button onclick="removeProduct({{ $wishlist->product_id }});" class="btn btn-outline-danger btn-sm" type="button"><i class="fas fa-trash-alt me-2"></i>Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div>
                                <h3 class="h5">
                                    Your wishlist is empty !!
                                </h3>
                            </div>
                        @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


@endsection


@section('customjs')

<script>
    //function delete item from wishlist
    function removeProduct(id) {

        if (confirm('Are you want to delete ?')) {

            $.ajax({
                url: '{{ route("account.removeProductFromWishlist") }}',
                type: 'post',
                data: {id:id},
                dataType: 'json',
                success: function(response) {

                    if (response.status == true) {
                        window.location.href = '{{ route("account.wishlist") }}';
                    }

                }

            });
        }

    }
</script>
@endsection
