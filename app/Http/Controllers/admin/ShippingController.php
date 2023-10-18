<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create () {
        $countries = Country::get();
        $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
                           ->leftJoin('countries','countries.id','shipping_charges.country_id')->get();

        $data['countries'] = $countries;
        $data['shippingCharges'] = $shippingCharges;
        return View('admin.shipping.create',$data);
    }

    public function store (Request $request) {


        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {

            $count = ShippingCharge::where('country_id',$request->country)->count();
            if ($count > 0) {
                session()->flash('error','Shipping already added');
                return response()->json([
                    'status' => true,
                ]);
            }

            $shipping = new ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();


            session()->flash('success','Shipping added successful');

            return response()->json([
                'status' => true,
                'message' => 'shipping added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($shippingId, Request $request) {
        $country = Country::get();

        $shippingCharge = ShippingCharge::find($shippingId);
        if (empty($shippingCharge)) {
            return redirect()->route('shippings.create');
        }

        $data['country'] = $country;
        $data['shippingCharge'] = $shippingCharge;


        return View('admin.shipping.edite',$data);
    }

    public function update ($shippingId, Request $request) {

        $shippingCharge = ShippingCharge::find($shippingId);
        if (empty($shippingCharge)) {
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Shipping not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {


            $shippingCharge->country_id = $request->country;
            $shippingCharge->amount = $request->amount;
            $shippingCharge->save();


            $request->session()->flash('success','Shipping updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Shipping updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($shippingId, Request $request) {

        $shippingCharge = ShippingCharge::find($shippingId);

        if (empty($shippingCharge)) {
            $request->session()->flash('error','Product not found');
            // return redirect()->route('categories.index');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Product not found'
             ]);
        }

        $shippingCharge->delete();

        session()->flash('success','Shipping deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Shipping deleted successfully'
        ]);
    }
}
