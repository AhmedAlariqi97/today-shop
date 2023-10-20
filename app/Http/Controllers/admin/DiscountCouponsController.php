<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DiscountCouponsController extends Controller
{
    public function index (Request $request) {
        $discountCoupons = DiscountCoupon::latest();

        if (!empty($request->get('keyword'))) {
            $discountCoupons = $discountCoupons->where('code','like','%'.$request->get('keyword').'%');
            $discountCoupons = $discountCoupons->orWhere('name','like','%'.$request->get('keyword').'%');
        }

        $discountCoupons = $discountCoupons->paginate(10);
        // $data['categories'] = $categories;
        return View('admin.discount_coupon.list',compact('discountCoupons'));

    }

    public function create () {
        return View('admin.discount_coupon.create');
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            // starting date must be greater than current date
            if(!empty($request->starts_at)) {

                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($startAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not less than current date time']
                    ]);
                }
            }

            // expireing date must be greater than starting date
            if(!empty($request->starts_at) && !empty($request->expires_at)) {

                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($expireAt->gt($startAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expire date must be greater than start date']
                    ]);
                }
            }

            $discountCoupon = new DiscountCoupon();
            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();


            session()->flash('success','Discount Coupon added successful');

            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($Id, Request $request) {

        $discountCoupon = DiscountCoupon::find($Id);
        if (empty($discountCoupon)) {

            session()->flash('error','Record not found');
            return redirect()->route('discount-Coupons.index');
        }

        $data['discountCoupon'] = $discountCoupon;
        return View('admin.discount_coupon.edite',$data);
    }

    public function update ($Id, Request $request) {

        $discountCoupon = DiscountCoupon::find($Id);

        if (empty($discountCoupon)) {

            session()->flash('error','Record not found');
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Discount Coupon not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            // starting date must be greater than current date
            if(!empty($request->starts_at)) {

                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($startAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not less than current date time']
                    ]);
                }
            }

            // expireing date must be greater than starting date
            if(!empty($request->starts_at) && !empty($request->expires_at)) {

                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($expireAt->gt($startAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expire date must be greater than start date']
                    ]);
                }
            }

            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();


            session()->flash('success','Discount Coupon updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($Id, Request $request) {

        $discountCoupon = DiscountCoupon::find($Id);
        if (empty($discountCoupon)) {

            $request->session()->flash('error','Record not found');
            // return redirect()->route('sub-categories.index');
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Discount Coupon not found'
             ]);
        }



        $discountCoupon->delete();

        $request->session()->flash('success','Discount Coupon deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully'
        ]);
    }
}
