<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Subcategory;
use App\Models\Category;

use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function createSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:subcategory',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }
        // dd($request->input('category_id'));
        $file = Storage::disk('public')->put('image/subcategories', $request->file('image'));

        
        $categoryData = Category::select('id', 'guid', 'name', 'image')
                                    ->where('guid', $request->input('category_id'))
                                    ->first();

        //                             dd($categoryData->id);
        $subCategoryObj = new Subcategory();
        $subCategoryObj->guid = Str::uuid();
        $subCategoryObj->category_id = $categoryData->id;
        $subCategoryObj->name = $request->input('name');
        $subCategoryObj->image = $file;
        $subCategoryObj->save();

        return response()->json([
            'message' => 'Subcategory saved successfully',
            'status' => '200',
        ], 200);
    }

    public function showallSubCategory()
    {

        $allsubCategoryData = Subcategory::all();

        foreach ($allsubCategoryData as $singleKeyCategory => $singlevaluecategory) {

            $categoryID = Category::select('guid')->where('id', $singlevaluecategory->category_id)->first();
            // dd($singlevaluecategory);
            $singlevaluecategory['category_guid'] = $categoryID->guid;
        }
        return response()->json([
            'data' => $allsubCategoryData,
            'status' => '200',
        ], 200);
    }

    public function showSubCategoryById(Request $request)
    {
        // dd($request->input('id'));
        // $validator = Validator::make($request->all(), [
        //     'id' => 'required|unique:category',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors'=>$validator->errors()], 400);
        // }

        $categoryData = Category::select('id', 'guid', 'name', 'image')
                                    ->where('guid', $request->input('id'))
                                    ->first();
           
                                    if ($categoryData == null) {
                                        return response()->json([
                                            'message' => "Invalid Category ID",
                                            'status' => '404',
                                        ], 404);   
                                    }                                    
                                    // dd($categoryData->id);
        $subcategoryData = Subcategory::select('id', 'guid', 'name', 'image')
                                    ->where('category_id', $categoryData->id)
                                    ->with('attributes.variances')
                                    ->get();

        // $data11 = $subcategoryData->attributes()->variances();
        // dd($subcategoryData);
        if ($subcategoryData == null) {
            return response()->json([
                'message' => "Data not found for the given ID",
                'status' => '404',
            ], 404);   
        }                                    
        // dd($categoryData);
        return response()->json([
            'subcategories' => $subcategoryData->toArray(),
            'status' => '200',
        ], 200);
    }


    public function deleteSubCategory(Request $request)
    {

        $allSubCategoryData = Subcategory::where('guid',  $request->input('guid'))->first();

        if ($allSubCategoryData == null) {
            return response()->json([
                'message' => "Invalid Sub Category ID",
                'status' => '404',
            ], 404);   
        }  
        $allSubCategoryData->delete();

        return response()->json([
            'message' => "Sub Category Deleted Successfully",
            'status' => '200',
        ], 200);
    }


    public function editsubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $subcategoryData = Subcategory::where('guid', $request->input('guid'))->first();
        if ($subcategoryData == null) {
            return response()->json([
                'message' => "Data not found for the given ID",
                'status' => '404',
            ], 404);   
        } 

        if ($request->file('image') != null) {
            $file = Storage::disk('public')->put('image/subcategories', $request->file('image'));
            $tempVar['image'] = $file;
        }
        
        $tempVar['guid'] = $request->input('guid');
        $tempVar['name'] = $request->input('name');
        
        $subcategoryData->update($tempVar);
        
        return response()->json([
            'data' =>  $subcategoryData,
            'message' => 'Category updated successfully',
            'status' => '200',
        ], 200);
    }


}
