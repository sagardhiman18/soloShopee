<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Subcategory;
use App\Models\Attribute;
use App\Models\Variance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AttributeController extends Controller
{
    
    public function createAttribute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subcategory_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $subcategoryData = Subcategory::select('id', 'guid', 'name')
                                    ->where('guid', $request->input('subcategory_id'))
                                    ->first();

        if ($subcategoryData == null) {
            return response()->json([
                'message' => "Invalid Sub category ID",
                'status' => '404',
            ], 404);   
        }

        foreach ($request->input('attributes') as $keys => $values) {

            // $attributeData = Attribute::where('name', $values)->first();

            // $attributeDataFordelete = Attribute::where('subcategory_id', $subcategoryData->id)->pluck('name');
            //print_r($request->input('attributes'));
            // $diff = array_diff($attributeDataFordelete->toArray(), $request->input('attributes'));
            // dd($diff);
            // if (array_key_exists($keys,$diff) || empty($diff)) {
            //     Attribute::where([['subcategory_id', '=',$subcategoryData->id],
            //     ['name', '=', $diff[$keys]]])
            //     ->delete();
            // }

            // if ($attributeData == null) {
                $attributeObj = new Attribute();
                $attributeObj->subcategory_id = $subcategoryData->id;
                $attributeObj->guid = Str::uuid();
                $attributeObj->name = $values;
                $attributeObj->save();
            // }

        }

        return response()->json([
            'message' => 'Attribute saved successfully',
            'status' => '200',
        ], 200);
    }

    public function deleteAttribute(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'subcategory_guid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $subcategory_guid = $request->input('subcategory_guid');
        $attribute_guid = $request->input('attribute_guid');

        $subcategoryData = Subcategory::select('id')
                                        ->where('guid', $subcategory_guid)
                                        ->first();

        if ($subcategoryData == null) {
            return response()->json([
            'message' => "Invalid Sub category ID",
            'status' => '404',
            ], 404);
        }

        $attributeData = Attribute::where([['subcategory_id', '=', $subcategoryData->id],['guid', '=', $attribute_guid]])
                                    ->first();

        if ($attributeData == null) {
            return response()->json([
                'message' => "Invalid Input",
                'status' => '404',
            ], 404);   
        }

        Variance::where('attribute_id',$attributeData->id)->delete();
        $attributeData->delete();

        return response()->json([
            'message' => "Attributes Deleted Successfully",
            'status' => '200',
        ], 200);
    }


    public function editAttribute(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'subcategory_guid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $subcategory_guid = $request->input('subcategory_guid');
        $attribute_guid = $request->input('attribute_guid');
        $attribute = $request->input('attribute');

        $subcategoryData = Subcategory::select('id',)
                                    ->where('guid', $subcategory_guid)
                                    ->first();

        if ($subcategoryData == null) {
            return response()->json([
                'message' => "Invalid Sub category ID",
                'status' => '404',
            ], 404);   
        }

        Attribute::where('subcategory_id', $subcategoryData->id)
                    ->where('guid', $attribute_guid)
                    ->update(['name' => $attribute]);

        // Attribute::where('subcategory_id', $subcategoryData->id)->delete();
        // dd($subcategoryData->id);                    
        //foreach ($request->input('attributes') as $keys => $values) {
            
            // $attributeObj = new Attribute();
            // $attributeObj->subcategory_id = $subcategoryData->id;
            // $attributeObj->guid = Str::uuid();
            // $attributeObj->name = $values;
            // $attributeObj->save();
        //}       

        return response()->json([
            'message' => 'Attribute updated successfully',
            'status' => '200',
        ], 200);
    }

}