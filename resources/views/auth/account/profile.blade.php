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
                    @include('auth.message')

                </div>
                <div class="col-md-3">
                    @include('front.partials.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>
                        <form action="" method="post" name="profileForm" id="profileForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input value="{{ $user->name}}" type="text" name="name" id="name" placeholder="Enter Your Name" class="form-control">
                                        <p class="error"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input value="{{ $user->email}}" type="text" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                        <p class="error"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone">Phone</label>
                                        <input value="{{ $user->phone}}" type="text" name="phone" id="phone" placeholder="Enter Your Phone" class="form-control">
                                        <p class="error"></p>
                                    </div>

                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-dark">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection


@section('customjs')
<script>
    $("#profileForm").submit(function(event) {
        event.preventDefault();
        var fromArray = $(this).serializeArray();
        $("button[type=submit]").prop('disabled', true);

        $.ajax({
            url: '{{ route("account.updateProfile") }}',
            type: 'post',
            data: fromArray,
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled', false);

                if (response["status"] == true) {

                    $(".error").removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('in-invalid');

                    window.location.href = "{{ route('account.profile') }}";


                } else {

                    var errors = response['errors'];

                    // simple code for all input
                    $(".error").removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('in-invalid');

                    $.each(errors, function(key, value) {
                        $(`#${key}`).addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(value);
                    });
                }


            },
            error: function() {
                console.log("something went wrong");
            }
        });

    });
</script>
@endsection
