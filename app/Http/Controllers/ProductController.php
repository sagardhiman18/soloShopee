<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Validator;
use App\Models\Images;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\ProductImages;
use App\Models\ProductConfigurationbyAdmin;
use Exception;


class ProductController extends Controller
{
    public function uplodImagesTogallery(Request $request) {

        // $imageProduct = Storage::disk('public')->put('image/products/productImage', $request->file('productImage'));
        // dd('hello');
        // $imageProductObj = new Images();
        // $imageProductObj->guid = Str::uuid();
        // $imageProductObj->title = 'productImage';
        // $imageProductObj->path = $imageProduct;
        // $imageProductObj->product_type = $request->input('product_type');
        // $imageProductObj->save();
        // dd($request->file('imageGallery'));
        foreach($request->file('imageGallery') as $singleGallery) {
            // print_r($request->file('imageGallery'));
            // die;
            $imageGallery = Storage::disk('public')->put('image/GalleryImages', $singleGallery);
            $imageGalleryObj = new Images();
            $imageGalleryObj->guid = Str::uuid();
            // $imageGalleryObj->title = 'productGallery';
            // $imageGalleryObj->product_type = $request->input('product_type');
            $imageGalleryObj->path = $imageGallery;
            $imageGalleryObj->save();
            $productGalleryArray[] = $imageGallery;
            // $productGalleryArray[] = $imageGalleryObj->guid;
        }

        return response()->json(["message" => 'Images Uploaded To gallery', "galleryImages" => $productGalleryArray], 200);
    }


    public function getImagesFromgallery(Request $request) {

        $imagesList = Images::select('guid', 'path')->paginate(20);
        return response()->json(["images" => $imagesList], 200);
    }


    public function createProduct(Request $request) {

        try {
        $productObj = new Product();
        // dd($request->input('category_guid')['name']);
        $category_guid = $request->input('category_guid');
        $subcategory_guid = $request->input('subcategory_guid');

        // $category_guid = $request->input('category_guid')['guid'];
        // $category_name = $request->input('category_guid')['name'];
        // $subcategory_guid = $request->input('subcategory_guid')['guid'];
        // $subcategory_name = $request->input('subcategory_guid')['name'];


        $seller_guid = $request->input('seller_guid');
        $tax_guid = $request->input('tax_guid');
        $products = $request->input('products');
        // dd($products['name']);
        $subcategoryID = Subcategory::select('id', 'name')->where('guid', $subcategory_guid)->first();
        $categoryID = Category::select('id', 'name')->where('guid', $category_guid)->first();

        // dd($categoryID == null);
        if ($categoryID == null) {
            return response()->json([
            'message' => "Invalid Category ID",
            'status' => '404',
            ], 404);
        }

        if ($subcategoryID == null) {
            return response()->json([
            'message' => "Invalid Sub Category ID",
            'status' => '404',
            ], 404);
        }

        // $attributeWithVariations = json_encode($request->input('attributeWithVariations'));
        // dd($attributeWithVariations);
        //foreach ($request->input('products') as $keys => $singleProducts) { 

            // dd($singleProducts['productImage']);
            // dd($singleProducts['productGallery']);
        $productObj->guid = Str::uuid();
        $productObj->name = $products['name'];
        $productObj->tax_id = $tax_guid;
        $productObj->seller_id = $seller_guid;
        $productObj->category_id = $categoryID->id;
        $productObj->subcategory_id = $subcategoryID->id;

        $productObj->category_name = $categoryID->name;
        $productObj->subcategory_name = $subcategoryID->name;

        $productObj->description = $products['description'];
        $productObj->brand = $products['brand'];
        $productObj->manufacturer = $products['manufactured'];
        $productObj->length = $products['length'];
        $productObj->width = $products['width'];
        $productObj->height = $products['height'];
        $productObj->weight = $products['weight'];
        $productObj->isProductExp = $products['isProductExp'];
        $productObj->quantity = $products['quantity'];
        $productObj->isProductReturn = $products['isProductReturn'];
        $productObj->stateorigin = $products['stateorigin'];
        $productObj->hsn = $products['hsn'];
        $productObj->max_price = $products['maxretailprice'];
        $productObj->base_price = $products['baseprice'];
        $productObj->taxpercent = $products['taxpercent'];
        $productObj->selling_price = $products['sellprice'];
        $productObj->leadTime = $products['leadTime'];
        $productObj->cod_allowed = $products['isCOD'];
        $productObj->isCancellable = $products['isCancellable'];

        if (array_key_exists('attributeWithVariations', $request->all())) {
            // dd('dd');
            $productObj->attributeWithVariations = json_encode($request->input('attributeWithVariations'));
        }
        // dd('ss');
        $productObj->status = $products['status'];
        // print_r($productObj->toArray());
        // die;
        $productObj->save();

        if (array_key_exists('productImage', $products)) {
            $productImageobj = new ProductImages();
            $productImageobj->guid = Str::uuid();
            $productImageobj->product_id = $productObj->id;
            $productImageobj->title = 'productImage';
            $productImageobj->path = $products['productImage'];
            $productImageobj->product_type = 0;
            $productImageobj->save();
            // dd($products['productImage']);
            // Images::where('guid', $products['productImage'])->update(['product_id' => $productObj->id]);
            // foreach ($products['productGallery'] as $keyForsingleGallery => $valuesForsingleGallery) {
            //     Images::where('guid', $valuesForsingleGallery)->update(['product_id' => $productObj->id]);
            // }
        }

        if (array_key_exists('productGallery', $products)) {
            // Images::where('guid', $products['productImage'])->update(['product_id' => $productObj->id]);
            foreach ($products['productGallery'] as $keyForsingleGallery => $valuesForsingleGallery) {

                $productImageobj = new ProductImages();
                $productImageobj->guid = Str::uuid();
                $productImageobj->product_id = $productObj->id;
                $productImageobj->title = 'productGallery';
                $productImageobj->path = $valuesForsingleGallery;
                $productImageobj->product_type = 0;
                $productImageobj->save();

                // Images::where('guid', $valuesForsingleGallery)->update(['product_id' => $productObj->id]);
            }
        }
      //  }
    //   $request->input('variatations')
        if (array_key_exists('variatations', $request->all())) {

            $variatations = $request->input('variatations');
            // print_r($request->input('variatations'));
            // die;
            foreach ($variatations as $singlevariationkey => $singlevariationvalue) {
            
                $productVariantObj = new ProductVariant();
                $productVariantObj->guid = Str::uuid();
                $productVariantObj->product_id = $productObj->id;
                $productVariantObj->name = $singlevariationvalue['name'];
                // $productVariantObj->description = $singlevariationvalue['description'];
                // $productVariantObj->brand = $singlevariationvalue['brand'];
                // $productVariantObj->manufacturer = $singlevariationvalue['manufactured'];
                // $productVariantObj->length = $singlevariationvalue['length'];
                // $productVariantObj->width = $singlevariationvalue['width'];
                // $productVariantObj->height = $singlevariationvalue['height'];
                // $productVariantObj->weight = $singlevariationvalue['weight'];
                // $productVariantObj->isProductExp = $singlevariationvalue['isProductExp'];
                // $productVariantObj->quantity = $singlevariationvalue['quantity'];
                // $productVariantObj->isProductReturn = $singlevariationvalue['isProductReturn'];
                // $productVariantObj->stateorigin = $singlevariationvalue['stateorigin'];
                // $productVariantObj->hsn = $singlevariationvalue['hsn'];
                $productVariantObj->max_price = $singlevariationvalue['maxretailprice'];
                $productVariantObj->base_price = $singlevariationvalue['baseprice'];
                $productVariantObj->taxpercent = $singlevariationvalue['taxpercent'];
                $productVariantObj->selling_price = $singlevariationvalue['sellprice'];
                $productVariantObj->isDefault = $singlevariationvalue['isDefault'];
                $productVariantObj->quantity = $singlevariationvalue['quantity'];
                // $productVariantObj->leadTime = $singlevariationvalue['leadTime'];
                // $productVariantObj->cod_allowed = $singlevariationvalue['isCOD'];
                // $productVariantObj->isCancellable = $singlevariationvalue['isCancellable'];

                $productVariantObj->save();

                if (array_key_exists('productImage', $singlevariationvalue)) {

                    $productImageobj = new ProductImages();
                    $productImageobj->guid = Str::uuid();
                    $productImageobj->product_id = $productVariantObj->id;
                    $productImageobj->title = 'productImage';
                    $productImageobj->path = $singlevariationvalue['productImage'];
                    $productImageobj->product_type = 1;
                    $productImageobj->save();

                    // Images::where('guid', $singlevariationvalue['productImage'])->update(['product_id' => $productVariantObj->id]);

                }

                if (array_key_exists('productGallery', $singlevariationvalue)) {
                    foreach ($singlevariationvalue['productGallery'] as $keyForsingleGallery => $valuesForsingleGallery) {

                        $productImageobj = new ProductImages();
                        $productImageobj->guid = Str::uuid();
                        $productImageobj->product_id = $productVariantObj->id;
                        $productImageobj->title = 'productGallery';
                        $productImageobj->path = $valuesForsingleGallery;
                        $productImageobj->product_type = 1;
                        $productImageobj->save();

                        // Images::where('guid', $valuesForsingleGallery)->update(['product_id' => $productVariantObj->id]);
                    }
                }
            }
            // dd('if');
        }
        // dd('else');
        return response()->json([
            'message' => 'Products saved successfully',
            'status' => '200',
        ], 200);

        } catch (\Exception $e) {
            print_r($e->getMessage());
            die;
            $result = [
                'message' => 'bad request',
                'status' => '404',
            ];
            return response()->json($result, 404);
        }
    }


    public function deleteProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_guid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $product_guid = $request->input('product_guid');

        $productData = Product::select('id')
                                        ->where('guid', $product_guid)
                                        ->first();

        if ($productData == null) {
            return response()->json([
            'message' => "Invalid Product ID",
            'status' => '404',
            ], 404);
        }
        ProductVariant::where('product_id',$productData->id)->delete();

        $productData->delete();

        return response()->json([
            'data' => "Product Deleted Successfully",
            'status' => '200',
        ], 200);
    }


    public function deleteMultipleProducts(Request $request)
    {
        // dd('sjdfkdj');
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'product_guid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $product_guids = $request->input('product_guid');

        foreach ($product_guids as $keys => $values) {

            $productData = Product::select('id')
            ->where('guid', $values)
            ->first();
            ProductVariant::where('product_id',$productData->id)->delete();
            $productData->delete();



        }   
        
        // if ($productData == null) {
        //     return response()->json([
        //     'message' => "Invalid Product ID",
        //     'status' => '404',
        //     ], 404);
        // }
        
        return response()->json([
            'data' => "Products Deleted Successfully",
            'status' => '200',
        ], 200);
    }



    public function showallProduct()
    {
        $allProductData = Product::select('id', 'guid', 'name', 'selling_price', 'quantity', 'type')->where('status', '1')->get();
        // dd($allProductData);
        foreach ($allProductData as $singleKey => $singleValueProduct) {
            $singleProductImage = ProductImages::where([['product_type', '=', '0'],['product_id', '=', $singleValueProduct->id], ['title', 'productGallery']])->pluck('path');

            $productvariantData = ProductVariant::select('id','guid', 'name', 'selling_price', 'quantity', 'isDefault', 'type')
                                                    ->where('product_id', $singleValueProduct->id)
                                                    ->get();
            foreach($productvariantData as $singleproductkey => $singleproductvariant) {
                // dd($singleproductvariant->id);
                $variantProductImage = ProductImages::where([['product_type', '=', '1'],['product_id', '=', $singleproductvariant->id], ['title', 'productGallery']])->pluck('path');

                $singleproductvariant['images'] = $variantProductImage;
            }

            $singleValueProduct['images'] = $singleProductImage;
            $singleValueProduct['productvariants'] = $productvariantData;
        }
        return response()->json([
            'products' => $allProductData,
            'status' => '200',
        ], 200);
    }


    public function showallDeactiveProduct()
    {
        $allProductData = Product::select('id', 'guid', 'name', 'selling_price', 'quantity')->where('status', '0')->get();
        // dd($allProductData);
        foreach ($allProductData as $singleKey => $singleValueProduct) {
            $singleProductImage = ProductImages::where([['product_type', '=', '0'],['product_id', '=', $singleValueProduct->id], ['title', 'productImage']])->pluck('path');

            $productvariantData = ProductVariant::select('id','guid', 'name', 'selling_price', 'quantity')
                                                    ->where('product_id', $singleValueProduct->id)
                                                    ->get();
            foreach($productvariantData as $singleproductkey => $singleproductvariant) {
                // dd($singleproductvariant->id);
                $variantProductImage = ProductImages::where([['product_type', '=', '1'],['product_id', '=', $singleproductvariant->id], ['title', 'productImage']])->pluck('path');

                $singleproductvariant['images'] = $variantProductImage;
            }
            
            $singleValueProduct['images'] = $singleProductImage;
            $singleValueProduct['productvariants'] = $productvariantData;
        }
        return response()->json([
            'products' => $allProductData,
            'status' => '200',
        ], 200);
    }


    public function showProductById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_guid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }
        $singleProductData = Product::where('guid', $request->input('product_guid'))->where('status', '1')->first();
        // dd($singleProductData);

        if ($singleProductData == null) {
            return response()->json([
                'message' => "Product is deactive",
                'status' => '422',
            ], 422);   
        }
        // $singleProductImage = ProductImages::select('title','path')->where([['product_type', '=', '0'],['product_id', '=', $singleProductData->id],['title', 'productGallery']])->get();

        $singleProductImage = ProductImages::where([['product_type', '=', '0'],['product_id', '=', $singleProductData->id], ['title', 'productGallery']])->pluck('path');

        // $productvariantData = ProductVariant::where('product_id', $singleProductData->id)->where('isDefault', 1)->get();
        $productvariantData = ProductVariant::where('product_id', $singleProductData->id)->get();
        foreach($productvariantData as $singleproductkey => $singleproductvariant) {
            // $variantProductImage = ProductImages::select('title','path')->where([['product_type', '=', '1'],['product_id', '=', $singleproductvariant->id],['title', 'productGallery']])->get();

            $variantProductImage = ProductImages::where([['product_type', '=', '1'],['product_id', '=', $singleproductvariant->id], ['title', 'productGallery']])->pluck('path');

            $singleproductvariant['images'] = $variantProductImage;
        }
            
        $singleProductData['images'] = $singleProductImage;
        $singleProductData['productvariants'] = $productvariantData;
        return response()->json([
            'products' => $singleProductData,
            'status' => '200',
        ], 200);

    }



    public function activatedeactivateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_guid' => 'required',
            'product_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        foreach ($request->input('product_guid') as $productKey => $productvalue) {

            Product::where('guid', $productvalue)
                        ->update(['status' =>$request->input('product_status')]);
        }

        return response()->json([
            'message' => 'Product Status changed successfully.',
            'status' => '200',
        ], 200);

    }


    public function editProduct(Request $request)
    {
        try {

            $product_guid = $request->input('product_guid');    
            $tax_guid = $request->input('tax_guid');
            $products = $request->input('products');

            $productID = Product::select('id', 'name')->where('guid', $product_guid)->first();

            if ($productID == null) {
                return response()->json([
                'message' => "Invalid Product ID",
                'status' => '404',
                ], 404);
            }
            if (array_key_exists('attributeWithVariations', $request->all())) {
                $attributeWithVariations = json_encode($request->input('attributeWithVariations'));
            }

            $productData = Product::where("guid", $product_guid)->update([
                'name' =>  $products['name'],
                'tax_id' => $tax_guid,
                'description' => $products['description'],
                'brand' => $products['brand'],
                'manufacturer' => $products['manufactured'],
                'length' => $products['length'],
                'width' => $products['width'],
                'height' => $products['height'],
                'weight' => $products['weight'],
                'isProductExp' => $products['isProductExp'],
                'quantity' => $products['quantity'],
                'isProductReturn' => $products['isProductReturn'],
                'stateorigin' => $products['stateorigin'],
                'hsn' => $products['hsn'],
                'max_price' => $products['maxretailprice'],
                'base_price' => $products['baseprice'],
                'taxpercent' => $products['taxpercent'],
                'selling_price' => $products['sellprice'],
                'leadTime' => $products['leadTime'],
                'cod_allowed' => $products['isCOD'],
                'isCancellable' => $products['isCancellable'],
                'status' => $products['status'],
                'attributeWithVariations' => $attributeWithVariations
                ]);

            ProductImages::where('product_id',$productID->id)->where('product_type', 0)->delete();

            if (array_key_exists('productImage', $products)) {
                $productImageobj = new ProductImages();
                $productImageobj->guid = Str::uuid();
                $productImageobj->product_id = $productID->id;
                $productImageobj->title = 'productImage';
                $productImageobj->path = $products['productImage'];
                $productImageobj->product_type = 0;
                $productImageobj->save();
            }

            if (array_key_exists('productGallery', $products)) {
                foreach ($products['productGallery'] as $keyForsingleGallery => $valuesForsingleGallery) {
    
                    $productImageobj = new ProductImages();
                    $productImageobj->guid = Str::uuid();
                    $productImageobj->product_id = $productID->id;
                    $productImageobj->title = 'productGallery';
                    $productImageobj->path = $valuesForsingleGallery;
                    $productImageobj->product_type = 0;
                    $productImageobj->save();

                }
            }

            if (array_key_exists('variatations', $request->all())) {
    
                $variatations = $request->input('variatations');
                foreach ($variatations as $singlevariationkey => $singlevariationvalue) {
                
                    $productVariantID = ProductVariant::select('id', 'name')
                                                        ->where('guid', $singlevariationvalue['productvariant_guid'])
                                                        ->first();

                    if ($productVariantID == null) {
                        return response()->json([
                            'message' => "Invalid Product Variants ID",
                            'status' => '404',
                            ], 404);
                    }

                    $productData = ProductVariant::where("guid", $product_guid)->update([
                        'name' =>  $singlevariationvalue['name'],
                        'product_id' => $productID->id,
                        'max_price' => $singlevariationvalue['maxretailprice'],
                        'base_price' => $singlevariationvalue['baseprice'],
                        'taxpercent' => $singlevariationvalue['taxpercent'],
                        'selling_price' => $singlevariationvalue['sellprice'],
                        'isDefault' => $singlevariationvalue['isDefault'],
                        'quantity' => $singlevariationvalue['quantity']
                        ]);

                    ProductImages::where('product_id',$productVariantID->id)->where('product_type', 1)->delete();
                    
                    if (array_key_exists('productImage', $singlevariationvalue)) {
    
                        $productImageobj = new ProductImages();
                        $productImageobj->guid = Str::uuid();
                        $productImageobj->product_id = $productVariantID->id;
                        $productImageobj->title = 'productImage';
                        $productImageobj->path = $singlevariationvalue['productImage'];
                        $productImageobj->product_type = 1;
                        $productImageobj->save();
        
                    }
    
                    if (array_key_exists('productGallery', $singlevariationvalue)) {
                        foreach ($singlevariationvalue['productGallery'] as $keyForsingleGallery => $valuesForsingleGallery) {
    
                            $productImageobj = new ProductImages();
                            $productImageobj->guid = Str::uuid();
                            $productImageobj->product_id = $productVariantID->id;
                            $productImageobj->title = 'productGallery';
                            $productImageobj->path = $valuesForsingleGallery;
                            $productImageobj->product_type = 1;
                            $productImageobj->save();
    
                        }
                    }
                }
            }
            return response()->json([
                'message' => 'Products Updated successfully',
                'status' => '200',
            ], 200);
    
            } catch (\Exception $e) {
                print_r($e->getMessage());
                die;
                $result = [
                    'message' => 'bad request',
                    'status' => '404',
                ];
                return response()->json($result, 404);
            }

    }


    public function showProductsByAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        ProductConfigurationbyAdmin::truncate();
        
        foreach ($request->input('categories') as $singleKey => $singleValue) {

            $categoryID = Category::select('id', 'name')->where('guid', $singleValue['guid'])->first();
            if ($categoryID == null) {
                return response()->json([
                'message' => "Invalid Category ID",
                'status' => '404',
                ], 404);
            }

            foreach ($singleValue['subcategories'] as $singleKeysubcategory => $singleValuesubcategory) { 

                $subcategoryID = Subcategory::select('id', 'name')->where('guid', $singleValuesubcategory['guid'])->first();            

                if ($subcategoryID == null) {
                    return response()->json([
                    'message' => "Invalid Sub Category ID",
                    'status' => '404',
                    ], 404);
                }

                // ProductConfigurationbyAdmin::where('category_id',$categoryID->id)->where('subcategory_id', $subcategoryID->id)->delete();

                
                

                $productConfigurationObj = new ProductConfigurationbyAdmin; 

                $productConfigurationObj->guid = Str::uuid();
                $productConfigurationObj->category_id = $categoryID->id;
                $productConfigurationObj->subcategory_id = $subcategoryID->id;
                $productConfigurationObj->productCount = $singleValuesubcategory['productCount'];

                // print_r($productConfigurationObj->toArray());
                // die;
                $productConfigurationObj->save();
                // dd($subcategoryID);
                }
        }

        return response()->json([
            'message' => 'Configuration successfully.',
            'status' => '200',
        ], 200);

    }

    public function showProductstoUsers()
    {
        $configureddata = ProductConfigurationbyAdmin::all();

        foreach ($configureddata as $keyconfiguration => $valueconfiguration) {

            $allProductData = Product::select('id', 'guid', 'name', 'selling_price', 'max_price', 'quantity', 'category_id', 'subcategory_id', 'type')
                                        ->where('category_id', $valueconfiguration['category_id'])
                                        ->where('subcategory_id', $valueconfiguration['subcategory_id'])
                                        ->where('status', '1')
                                        ->orderBy('id', 'desc')->take($valueconfiguration['productCount'])->get();
                                        $allData = [];
            foreach ($allProductData as $singleProductKey => $singleProductValue) {
                
                // print_r($allProductData->toArray());
                // die;
                $categoryData = Category::select('id', 'guid', 'name')->where('id', $singleProductValue->category_id)->first();
                $SubcategoryData = Subcategory::select('id', 'guid', 'name')->where('id', $singleProductValue->subcategory_id)->first();


                $singleProductImage = ProductImages::select('title','path')->where([['product_type', '=', '0'],['product_id', '=', $singleProductValue->id], ['title', 'productGallery']])->first();
                // print_r($singleProductValue->id);
                $productvariantData = ProductVariant::select('id','guid', 'name', 'selling_price', 'quantity', 'isDefault', 'type')
                                                        ->where('product_id', $singleProductValue->id)
                                                        ->where('isDefault', 1)
                                                        ->first();
                if ($productvariantData != null) {
                    $ProductVariantImage = ProductImages::select('title','path')->where([['product_type', '=', '1'],['product_id', '=', $productvariantData->id], ['title', 'productGallery']])->first();                
                    // $productvariantData['ProductVariantImage'] = $ProductVariantImage;
                    $productvariantData['images'] = $ProductVariantImage;
                    $singleProductValue['productvariants'] = $productvariantData;
                }

                // $singleProductValue['ProductImage'] = $singleProductImage;
                $singleProductValue['images'] = $singleProductImage;

                $allData["category_name"] = $categoryData->name;
                $allData["category_guid"] = $categoryData->guid;
                $allData["subcategories"]["subcategory_name"] = $SubcategoryData->name;
                $allData["subcategories"]["subcategory_guid"] = $SubcategoryData->guid;
                $allData["subcategories"]["data"][] = $singleProductValue;

                // print_r($allData);
                
            }

            // $allData[$categoryData->name][$SubcategoryData->name] = $allProductData->toArray(); 
            // $allData[$categoryData->name][$SubcategoryData->name] = $allProductData->toArray();  
            if (!empty($allData)) {
                $finalData[] = $allData; 
            }
              
            // print_r($allData); die; 
        }

        return response()->json([
            'data' => $finalData,
            'status' => '200',
        ], 200);
    }


    public function showProductstoAdmin()
    {
        $configureddata = ProductConfigurationbyAdmin::select('category_id','subcategory_id')->get();
        // print_r($configureddata->toArray()); die;
        foreach ($configureddata as $keyconfiguration => $valueconfiguration) {

            $categoryData = Category::select('id', 'guid')->where('id', $valueconfiguration['category_id'])->first();
            $SubcategoryData = Subcategory::select('id', 'guid')->where('id', $valueconfiguration['subcategory_id'])->first();
            // $valueconfiguration['guid'] = $categoryData->guid;
            // $subcategories = $valueconfiguration->subcategories;
            $tempVar['subcategories']['guid'] = $SubcategoryData->guid;

            $configurationCountdata = ProductConfigurationbyAdmin::select('productCount')->where('category_id', $categoryData->id)->where('subcategory_id', $SubcategoryData->id)->first();

            $tempVar['subcategories']['productCount'] = $configurationCountdata->productCount;

            $valueconfiguration = $tempVar;
            $valueconfiguration['guid'] = $categoryData->guid;

            $allData[] = $valueconfiguration;
        }

        return response()->json([
            'categories' => $allData,
            'status' => '200',
        ], 200);
    }


}
