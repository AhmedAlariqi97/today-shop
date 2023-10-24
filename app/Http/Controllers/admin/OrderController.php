<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index (Request $request) {
        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders = $orders->leftJoin('users','users.id','orders.user_id');

        if (!empty($request->get('keyword'))) {
            $orders = $orders->where('users.name','like','%'.$request->get('keyword').'%');
            $orders = $orders->orWhere('users.email','like','%'.$request->get('keyword').'%');
            $orders = $orders->orWhere('orders.id','like','%'.$request->get('keyword').'%');
        }

        $orders = $orders->paginate(10);
        // $data['orders'] = $orders;
        return View('admin.order.list',compact('orders'));

    }

    public function detial ($id) {

        $orders = Order::select('orders.*','countries.name as countryName')
                        ->where('orders.id',$id)
                        ->leftJoin('countries','countries.id','orders.country_id')
                        ->first();

        $orderItems = OrderItem::where('order_id',$id)->get();

        $data['orders'] = $orders;

        $data['orderItems'] = $orderItems;

        return view('admin.order.detial', $data);
    }

    public function changeOrderStatus (Request $request ,$orderId) {

        $order = Order::find($orderId);
        $order-> status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        $message = 'Order status updated successfully';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);

    }

    public function sendInvoiceEmail(Request $request, $orderId) {

        orderEmail($orderId, $request->userType);

        $message = 'Order email sent successfully';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
