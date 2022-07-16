<?php

namespace App\Http\Controllers\ProductsServices;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductsServices\Product as ProductResource;
use App\Models\Business;
use App\Models\ProductsServices\Product;
use App\Models\ProductsServices\PurchaseOrder;
use App\Models\ProductsServices\SelectableProductTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsServicesController extends BaseController
{
    //
    public function listProducts(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $business = $authUser->business;
        $products = $business->products;
        return $this->sendResponse(ProductResource::collection($products), 'Products listed successfully');
    }

    public function listSavedProducts(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $products = $user->savedProducts;
        return $this->sendResponse(ProductResource::collection($products), 'Products listed successfully');
    }

    public function listOtherBusinessProducts(Request $request, $slug)
    {
        $business = Business::firstWhere("slug", $slug);
        if ($business == null){
            return $this->sendError("Business with slug '$slug' not found", [], 404);
        }

        $products = $business->products;
        return $this->sendResponse(ProductResource::collection($products), 'Products listed successfully');
    }

    public function createProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'gallery_image' => 'required|image|max:10240',
            'thumbnail_image' => 'required|image|max:10240',
            'description' => 'required|string',
            'quantity' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'category' => 'required|string',
            'tags' => "required|string",
            'units' => "required|string",
            'minimum_purchase_quantity' => "required|numeric",
            'sku' => "required|string|unique:products",
            'pdf_specs' => 'required|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $inp_product = $request->except(["tags", "pdf_specs", "gallery_image", "thumbnail_image"]);

        $authUser = User::find(Auth::user()->id);
        // business_id
        $inp_product["business_id"] = $authUser->business->id;
        // created_by
        $inp_product["created_by"] = $authUser->id;

        //gallery_image
        $path = $request->file('gallery_image')->store('product_images');
        $inp_product["gallery_image"] = $path;

        //thumbnail_image
        $path = $request->file('thumbnail_image')->store('product_images');
        $inp_product["thumbnail_image"] = $path;

        //pdf_specs
        $path = $request->file('pdf_specs')->store('product_images');
        $inp_product["pdf_specs"] = $path;

        // creating product
        $product = Product::create($inp_product);

        // tags
        if(!is_array($request->tags)){
            $raw_request_tags = json_decode($request->tags);
            if($raw_request_tags == null){
                throw new \Exception("Invalid Json Field tags");
            }
        }
        else{
            $raw_request_tags = $request->tags;
        }
        $request_tags = [];
        foreach($raw_request_tags as $raw_request_tag){
            $request_tag = SelectableProductTag::firstOrCreate(['name' => $raw_request_tag]);
            array_push($request_tags, $request_tag->id);
        }
        $product->tags()->sync($request_tags);
        $product->save();

        return $this->sendResponse(new ProductResource($product), 'Product added successfully.');
    }

    public function saveProduct(Request $request, $id){
        $product = Product::find($id);
        if ($product == null){
            return $this->sendError("Product with id '$id' not found", [], 404);
        }

        $user = User::find(Auth::user()->id);

        $user->savedProducts()->sync([$product->id], false);
        return $this->sendResponse([], "You saved a product");
    }

    public function unsaveProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product == null){
            return $this->sendError("Product with id '$id' not found", [], 404);
        }

        $user = User::find(Auth::user()->id);

        $user->savedProducts()->detach($product->id);
        return $this->sendResponse([], "You removed a product from saved products");
    }
}
