@extends('admin.layouts.pages-layout')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edite Discount Coupon</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route ('discount-coupons.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="discountCouponForm" name="discountCouponForm">
            <div class="card">
                <div class="card-body">
                <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code">Code</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="code"
                                    value="{{ $discountCoupon->code }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name"
                                    value="{{ $discountCoupon->name }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea type="text" name="description" id="description" class="form-control" placeholder="description">
                                   {{ $discountCoupon->description }}
                                </textarea>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses">Max Uses</label>
                                <input type="text" name="max_uses" id="max_uses" class="form-control" placeholder="max uses"
                                      value="{{ $discountCoupon->max_uses }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses_user">Max Uses Users</label>
                                <input type="text" name="max_uses_user" id="max_uses_user" class="form-control" placeholder="max uses users"
                                       value="{{ $discountCoupon->max_uses_user }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option {{ ($discountCoupon->type == 'percent' ) ? 'selected' : '' }} value="percent">Percent</option>
                                    <option {{ ($discountCoupon->type == 'fixed' ) ? 'selected' : '' }} value="fixed">Fixed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount">Discount Amount</label>
                                <input type="text" name="discount_amount" id="discount_amount" class="form-control" placeholder="discount amount"
                                       value="{{ $discountCoupon->discount_amount }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_amount">Min Amount</label>
                                <input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="min amount"
                                       value="{{ $discountCoupon->min_amount }}">
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{ ($discountCoupon->status == 1 ) ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ ($discountCoupon->status == 0 ) ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="starts_at">Starts At</label>
                                <input type="text" name="starts_at" id="starts_at" class="form-control" placeholder="min-amount"
                                       value="{{ $discountCoupon->starts_at }}">
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at">Expires At</label>
                                <input type="text" name="expires_at" id="expires_at" class="form-control" placeholder="min-amount"
                                       value="{{ $discountCoupon->expires_at }}">
                                <p></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">update</button>
                <a href="{{ route ('discount-coupons.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>

        </form>

    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customjs')
<script>
    $(document).ready(function(){
            $('#starts_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
    });

    $(document).ready(function(){
            $('#expires_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
    });


    $("#discountCouponForm").submit(function(event) {
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("discount-coupons.update",$discountCoupon->id) }}',
            type: 'put',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled',false);

                if (response["status"] == true) {

                    window.location.href="{{ route('discount-coupons.index') }}";

                    $("#code").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                    $("#type").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                     $("#discount_amount").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                    $("#starts_at").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                    $("#expires_at").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                } else {

                    if (response['notFound'] == true) {
                        window.location.href="{{ route('discount-coupons.index') }}";
                        return false;
                    }

                    var errors = response['errors'];

                    if (errors['code']) {
                        $("#code").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['code']);
                    } else {
                        $("#code").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }

                    if (errors['type']) {
                        $("#type").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['type']);
                    } else {
                        $("#type").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }

                    if (errors['discount_amount']) {
                        $("#discount_amount").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['discount_amount']);
                    } else {
                        $("#discount_amount").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }

                    if (errors['starts_at']) {
                        $("#starts_at").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['starts_at']);
                    } else {
                        $("#starts_at").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }

                    if (errors['expires_at']) {
                        $("#expires_at").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['expires_at']);
                    } else {
                        $("#expires_at").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }
                }


            },
            error: function(jqXHR, exception) {
                console.log("something went wrong");
            }
        });

    });

    $("#name").change(function() {
        element = $(this);
        // $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("getSlug") }}',
            type: 'get',
            data: { title: element.val() },
            dataType: 'json',
            success: function(response) {
                // $("button[type=submit]").prop('disabled',false);

                if(response["status"] == true) {
                    $("#slug").val(response["slug"]);
                }
          }
        });

    });

    // dropZone

    // dropzone single image



    //dropzone multi-images

//     Dropzone.autoDiscover = false;
//     const dropzone = new Dropzone("#image", {
//     url: "{{ route('temp-images.create') }}",
//     maxFiles: 5, // Set the maximum number of files to be uploaded
//     paramName: 'image',
//     addRemoveLinks: true,
//     acceptedFiles: "image/jpeg,image/png,image/gif,imgae/svg",
//     headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     },
//     success: function(file, response) {
//         $("#image_id").val(response.image_id);
//         //console.log(response);
//     }
// });






</script>

@endsection
