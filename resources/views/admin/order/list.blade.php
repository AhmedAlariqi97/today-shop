@extends('admin.layouts.pages-layout')

@section('content')
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Orders</h1>
							</div>
							<div class="col-sm-6 text-right">
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                    @include('admin.message')
						<div class="card">
                            <form action="" method="get">
                                <div class="card-header">
                                    <div class="card-title">
                                        <button type="button" onclick="window.location.href='{{ route("orders.index") }}'" class="btn btn-default btn-sm">
                                            Reset
                                        </button>
                                    </div>
                                    <div class="card-tools">
                                        <div class="input-group input-group" style="width: 250px;">
                                            <input value="{{ Request::get('keyword') }}" type="text" name="keyword" class="form-control float-right" placeholder="Search">

                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
							<div class="card-body table-responsive p-0">
								<table class="table table-hover text-nowrap">
									<thead>
										<tr>
											<th>Orders #</th>
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Phone</th>
											<th>Status</th>
                                            <th>Total</th>
                                            <th>Date Purchased</th>
										</tr>
									</thead>
									<tbody>
                                    @if ($orders->isNotEmpty())
                                    @foreach($orders as $order)
										<tr>
											<td><a href="{{ route('orders.detial', $order->id) }}">{{ $order->id }}</a></td>
											<td>{{ $order->first_name.' '.$order->last_name }}</td>
                                            <td>{{ $order->email }}</td>
                                            <td>{{ $order->mobile }}</td>
                                            <td>
                                                @if ($order->status == 'pending')
                                                <span class="badge bg-danger">Pending</span>
                                                @elseif ($order->status == 'shipped')
                                                <span class="badge bg-info">Shipped</span>
                                                @elseif ($order->status == 'Delivered')
                                                <span class="badge bg-success">Delivered</span>
                                                @else
                                                <span class="badge bg-danger">Canselled</span>
                                                @endif
											</td>
											<td>${{ $order->grand_total }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M,Y') }}</td>
										</tr>
                                     @endforeach
                                     @else
                                    <tr>
                                        <td colspan="5">Records not found</td>
                                    </tr>
                                    @endif


									</tbody>
								</table>
							</div>
							<div class="card-footer clearfix">
                            {{ $orders->links() }}
							</div>
						</div>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->



@endsection

@section('customjs')
<script>
    function deleteCategory(id) {

        var url = '{{ route("categories.delete", "ID") }}';
        var newUrl = url.replace("ID", id);

        if (confirm("Are you sure you want to delete")) {
            $.ajax({
                url: newUrl,
                type: 'delete',
                data: {},
                dataType: 'json',
                headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response["status"]) {
                        window.location.href = "{{ route('categories.index') }}";
                    }
                }
            });
        }
    }
</script>

@endsection
