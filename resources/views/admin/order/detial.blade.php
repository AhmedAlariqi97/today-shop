@extends('admin.layouts.pages-layout')

@section('content')

<section class="content-header">
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Order: #{{ $orders->id}}</h1>
							</div>
							<div class="col-sm-6 text-right">
                                <a href="{{ route('orders.index') }}" class="btn btn-primary">Back</a>
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
						<div class="row">
                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header pt-3">
                                        <div class="row invoice-info">
                                            <div class="col-sm-4 invoice-col">
                                            <h1 class="h5 mb-3">Shipping Address</h1>
                                            <address>
                                                <strong>{{ $orders->first_name.' '.$orders->last_name}}</strong><br>
                                                {{ $orders->address}}<br>
                                                {{ $orders->city}}, {{ $orders->countryName}}<br>
                                                Phone: {{ $orders->mobile}}<br>
                                                Email: {{ $orders->email}}
                                            </address>
                                            <br>
                                            <strong>Shipped Date</strong><br>
                                                    @if(!empty($orders->shipped_date))
                                                       {{ \Carbon\Carbon::parse($orders->shipped_date)->format('d M,Y') }}
                                                    @else
                                                        n/a
                                                    @endif

                                            </div>



                                            <div class="col-sm-4 invoice-col">
                                                <b>Invoice #007612</b><br>
                                                <br>
                                                <b>Order ID:</b> {{ $orders->id}}<br>
                                                <b>Total:</b> ${{ number_format($orders->grand_total,2) }}<br>
                                                <b>Status:</b>
                                                    @if ($orders->status == 'pending')
                                                    <span class="badge bg-danger">Pending</span>
                                                    @elseif ($orders->status == 'shipped')
                                                    <span class="badge bg-info">Shipped</span>
                                                    @elseif ($orders->status == 'Delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                    @else
                                                    <span class="badge bg-danger">Canselled</span>
                                                    @endif
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-3">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th width="100">Price</th>
                                                    <th width="100">Qty</th>
                                                    <th width="100">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if ($orderItems->isNotEmpty())
                                                @foreach($orderItems as $orderItem)
                                                <tr>
                                                    <td>{{ $orderItem->name }}</td>
                                                    <td>${{ number_format($orderItem->price,2) }}</td>
                                                    <td>{{ $orderItem->qty }}</td>
                                                    <td>${{ number_format($orderItem->total,2) }}</td>
                                                </tr>
                                                @endforeach
                                            @endif

                                                <tr>
                                                    <th colspan="3" class="text-right">Subtotal:</th>
                                                    <td>${{ number_format($orders->subtotal,2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th colspan="3" class="text-right">Discount: {{ (!empty($orders->coupon_code)) ? '('.$orders->coupon_code.')' : ''}}</th>
                                                    <td>${{ number_format($orders->discount,2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th colspan="3" class="text-right">Shipping:</th>
                                                    <td>${{ number_format($orders->shipping,2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-right">Grand Total:</th>
                                                    <td>${{ number_format($orders->grand_total,2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <form action="" method="post" name="changeOrderStatusform" id="changeOrderStatusform">
                                        <div class="card-body">
                                            <h2 class="h4 mb-3">Order Status</h2>
                                            <div class="mb-3">
                                                <select name="status" id="status" class="form-control">
                                                    <option value="pending" {{ ($orders->status == 'pending') ? 'selected' : ''}}>Pending</option>
                                                    <option value="shipped" {{ ($orders->status == 'shipped') ? 'selected' : ''}}>Shipped</option>
                                                    <option value="delivered" {{ ($orders->status == 'delivered') ? 'selected' : ''}}>Delivered</option>
                                                    <option value="canselled" {{ ($orders->status == 'canselled') ? 'selected' : ''}}>Canselled</option>
                                                    <!-- <option value="">Cancelled</option> -->
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="">Shipped Date</label>
                                                <input value="{{ $orders->shipped_date }}" type="text" name="shipped_date" id="shipped_date" class="form-control" placeholder="Shipped Date">
                                            </div>
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <form action="" method="post" name="sendInvoiceEmail" id="sendInvoiceEmail">
                                            <h2 class="h4 mb-3">Send Inovice Email</h2>
                                            <div class="mb-3">
                                                <select name="userType" id="userType" class="form-control">
                                                    <option value="customer">Customer</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary">Send</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
					<!-- /.card -->
				</section>

@endsection


@section('customjs')
<script>
    // shipped calender
    $(document).ready(function(){
            $('#shipped_date').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
    });

    //submit update orders

    $("#changeOrderStatusform").submit(function(event) {
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        $.ajax({
            url: '{{ route("orders.changeOrderStatus",$orders->id) }}',
            type: 'post',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled',false);

                if (response["status"] == true) {

                    window.location.href="{{ route('orders.detial',$orders->id) }}";

                } else {

                    if (response['notFound'] == true) {
                        window.location.href="{{ route('orders.index') }}";
                        return false;
                    }

                    var errors = response['errors'];

                }


            },
            error: function(jqXHR, exception) {
                console.log("something went wrong");
            }
        });

    });

    //submit send invoice email orders

    $("#sendInvoiceEmail").submit(function(event) {
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);

        if (confirm("Are you sure you want to send email ?")) {

            $.ajax({
                url: '{{ route("orders.sendInvoiceEmail",$orders->id) }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled',false);

                    if (response["status"] == true) {

                        window.location.href="{{ route('orders.detial',$orders->id) }}";

                    } else {

                        if (response['notFound'] == true) {
                            window.location.href="{{ route('orders.index') }}";
                            return false;
                        }

                        var errors = response['errors'];

                    }


                },
                error: function(jqXHR, exception) {
                    console.log("something went wrong");
                }
            });
        }

    });

</script>
@endsection
