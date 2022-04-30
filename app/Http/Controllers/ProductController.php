<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;


class ProductController extends Controller
{
    public function createProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:category',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $file = Storage::disk('public')->put('image/categories', $request->file('image'));

        $productObj = new Product();
        $productObj->guid = Str::uuid();
        $productObj->name = $request->input('name');
        $productObj->image = $file;
        $productObj->save();

        return response()->json([
            'message' => 'Category saved successfully',
            'status' => '200',
        ], 200);
    }


    public function showallProduct()
    {

        $allProductData = Product::all();

        return response()->json([
            'data' => $allProductData,
            'status' => '200',
        ], 200);
    }


    public function showProductById(Request $request)
    {
        // dd($request->input('id'));
        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:category',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $productData = Product::select('id', 'guid', 'name', 'image')
                                    ->where('guid', $request->input('id'))
                                    ->first();

        if ($productData == null) {
            return response()->json([
                'message' => "Data not found for the given ID",
                'status' => '404',
            ], 404);   
        }                                    
        // dd($categoryData);
        return response()->json([
            'data' => $productData->toArray(),
            'status' => '200',
        ], 200);
    }
}
