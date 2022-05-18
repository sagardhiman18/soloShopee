<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function addToCart(Request $request) {

        if (Auth::user() == null) {
            return response()->json([
            'message' => "Unauthenticate user",
            'status' => '404',
            ], 404);
        }

        dd($request->input('type'));

        if($request->input('type') == 'simple') {

            $productData = Product::where('guid', $request->input('product_guid'))->first();

            $cartObj = new Cart();
            $cartObj->guid = Str::uuid();
            $cartObj->user_id = Auth::user()->id;
            $cartObj->product_id = $productData->id;
            $cartObj->quantity = $request->input('quantity');
            $cartObj->image = $request->input('image');

            $cartObj->save();

            return response()->json([
                'message' => "Added To Cart",
                'status' => '200',
            ], 200);

        } else if ($request->input('variant')) {

            $productData = ProductVariant::where('guid', $request->input('product_guid'))->first();

            $cartObj = new Cart();

            $cartObj->guid = Str::uuid();
            $cartObj->user_id = Auth::user()->id;
            $cartObj->parent_id = $productData->product_id;
            $cartObj->product_variant_id = $productData->id;
            $cartObj->quantity = $request->input('quantity');
            $cartObj->image = $request->input('image');

            $cartObj->save();
            return response()->json([
                'message' => "Added To Cart",
                'status' => '200',
                ], 200);

        }

        // $cartObj->user_id = Auth::user()->id;

    }
}
