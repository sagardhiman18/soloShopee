<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Subcategory;
use App\Models\Attribute;
use App\Models\Variance;

class VarianceController extends Controller
{

    public function createVariance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'subcategory_id' => 'required',
            'attribute_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        // $subcategoryData = Subcategory::select('id', 'guid', 'name')
        //                             ->where('guid', $request->input('subcategory_id'))
        //                             ->first();

        $attributeData = Attribute::select('id')
                                    ->where('guid', $request->input('attribute_id'))
                                    ->first();

        if ($attributeData == null) {
            return response()->json([
                'message' => "Invalid Attribute ID",
                'status' => '404',
            ], 404);   
        }

        foreach ($request->input('variance') as $keys => $values) {

            // $varianceData = Variance::where('name', $values)->first();

            // if ($varianceData == null) {
                $varianceObj = new Variance();
                $varianceObj->attribute_id = $attributeData->id;
                $varianceObj->guid = Str::uuid();
                $varianceObj->name = $values;
                $varianceObj->save();
            // }
        }

        return response()->json([
            'message' => 'Variance saved successfully',
            'status' => '200',
        ], 200);
    }


    public function deleteVariance(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'attribute_guid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        // $subcategory_guid = $request->input('subcategory_guid');
        $attribute_guid = $request->input('attribute_guid');
        $variance_guid = $request->input('variance_guid');

        $attributeData = Attribute::select('id')
                                        ->where('guid', $attribute_guid)
                                        ->first();

        if ($attributeData == null) {
            return response()->json([
            'message' => "Invalid Attribute ID",
            'status' => '404',
            ], 404);
        }

        $varianceData = Variance::where([['attribute_id', '=', $attributeData->id],['guid', '=', $variance_guid]])
                                    ->first();

        if ($varianceData == null) {
            return response()->json([
                'message' => "Invalid Input",
                'status' => '404',
            ], 404);   
        }  

        // Variance::where('attribute_id',$attributeData->id)->delete();
        $varianceData->delete();

        return response()->json([
            'message' => "Variance Deleted Successfully",
            'status' => '200',
        ], 200);
    }

    
    public function editVariance(Request $request)
    {
        // dd('sjbdbk');
        $validator = Validator::make($request->all(), [
            'attribute_guid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }
        
        // $subcategoryData = Subcategory::select('id', 'guid', 'name')
        //                             ->where('guid', $request->input('subcategory_id'))
        //                             ->first();

        $attribute_guid = $request->input('attribute_guid');
        $variance_guid = $request->input('variance_guid');
        $variance = $request->input('variance');
        
        $attributeData = Attribute::select('id')
                                    ->where('guid', $attribute_guid)
                                    ->first();

        if ($attributeData == null) {
            return response()->json([
                'message' => "Invalid Attribute ID",
                'status' => '404',
            ], 404);   
        }


        $varianceData = Variance::where('attribute_id', $attributeData->id)
                                    ->where('guid', $variance_guid)
                                    ->first();

        if ($varianceData == null) {
            return response()->json([
                'message' => "Invalid Variance ID",
                'status' => '404',
            ], 404);   
        }

        Variance::where('attribute_id', $attributeData->id)
                    ->where('guid', $variance_guid)
                    ->update(['name' => $variance]);
        // Variance::where('attribute_id', $attributeData->id)->delete();
        // foreach ($request->input('variance') as $keys => $values) {
            
        //     $varianceObj = new Variance();
        //     $varianceObj->attribute_id = $attributeData->id;
        //     $varianceObj->guid = Str::uuid();
        //     $varianceObj->name = $values;
        //     $varianceObj->save();
        // }       

        return response()->json([
            'message' => 'Variance updated successfully',
            'status' => '200',
        ], 200);
    }
}
