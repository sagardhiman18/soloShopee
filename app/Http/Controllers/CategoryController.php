<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth:admins');
    // }

    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:category',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $file = Storage::disk('public')->put('image/categories', $request->file('image'));

        $categoryObj = new Category();
        $categoryObj->guid = Str::uuid();
        $categoryObj->name = $request->input('name');
        $categoryObj->image = $file;
        $categoryObj->save();

        return response()->json([
            'message' => 'Category saved successfully',
            'status' => '200',
        ], 200);
    }


    public function showallCategory()
    {

        $allCategoryData = Category::all();

        return response()->json([
            'data' => $allCategoryData,
            'status' => '200',
        ], 200);
    }


    public function showCategoryById(Request $request)
    {
        // dd($request->input('id'));
        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:category',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $categoryData = Category::select('id', 'guid', 'name', 'image')
                                    ->where('guid', $request->input('id'))
                                    ->first();

        if ($categoryData == null) {
            return response()->json([
                'message' => "Data not found for the given ID",
                'status' => '404',
            ], 404);   
        }                                    
        // dd($categoryData);
        return response()->json([
            'data' => $categoryData->toArray(),
            'status' => '200',
        ], 200);
    }


    public function deleteCategory(Request $request)
    {

        $allCategoryData = Category::where('guid',  $request->input('id'))->first();

        if ($allCategoryData == null) {
            return response()->json([
                'message' => "Invalid Category ID",
                'status' => '404',
            ], 404);   
        }  
        $allCategoryData->delete();

        return response()->json([
            'data' => "Category Deleted Successfully",
            'status' => '200',
        ], 200);
    }


    public function editCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $categoryData = Category::where('guid', $request->input('guid'))->first();
        if ($categoryData == null) {
            return response()->json([
                'message' => "Data not found for the given ID",
                'status' => '404',
            ], 404);   
        } 

        if ($request->file('image') != null) {
            $file = Storage::disk('public')->put('image/categories', $request->file('image'));
            $tempVar['image'] = $file;
        }
        
        $tempVar['guid'] = $request->input('guid');
        $tempVar['name'] = $request->input('name');

        $categoryData->update($tempVar);

        return response()->json([
            'data' =>  $categoryData,
            'message' => 'Category updated successfully',
            'status' => '200',
        ], 200);
    }

}
