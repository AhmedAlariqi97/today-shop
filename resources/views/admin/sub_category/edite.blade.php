@extends('admin.layouts.pages-layout')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edite Sub Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route ('sub-categories.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="subCategoryForm" name="subCategoryForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select a category</option>
                                    @if($subcategory->isNotEmpty())
                                    @foreach ($subcategory as $subcategory)
                                    <option value="{{ $category->id }}">{{ $subcategory->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name"
                                 value="{{ $subcategory->name }}">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug"
                                  value="{{ $subcategory->slug }}">
                                <p></p>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{ ($subcategory->status == 1 ) ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ ($subcategory->status == 0 ) ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">update</button>
                <a href="{{ route ('sub-categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>

        </form>

    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customjs')
<script>
    $("#subCategoryForm").submit(function(event) {
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("sub-categories.update",$subcategory->id) }}',
            type: 'put',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled',false);

                if (response["status"] == true) {

                    window.location.href="{{ route('sub-categories.index') }}";

                    $("#name").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                    $("#slug").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                    $("#category").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                } else {

                    var errors = response['errors'];

                    if (errors['name']) {
                        $("#name").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['name']);
                    } else {
                        $("#name").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }

                    if (errors['slug']) {
                        $("#slug").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['slug']);
                    } else {
                        $("#slug").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                    }

                    if (errors['category']) {
                        $("#category").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['category']);
                    } else {
                        $("#category").removeClass('is-invalid')
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
        $element = $(this);
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
