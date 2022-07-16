<?php

namespace App\Http\Controllers;

use App\Models\ProductsServices\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;

class ProductsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $products = Product::all();
        return $this->sendResponse(ProductResource::collection($products), 'Products home .');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' =>'required|string',
            'gallery_image'=>'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'thumbnail_image'=>'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'description' =>'required|string',
            'quantity' =>'required|string',
            'unit_price' =>'required',
            'category'=>'required|string',
            'tags'=>"required|string",
            'units'=>"required|string",
            'minimum_purchase_quantity'=>"required|string",
            'sku'=>"string",
            'pdf_specs' => 'pdf|max:2048'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        else{


            $products= new Product();
            //gallery_image
            $image = $request->input('gallery_image');
            $image_name=time().'.'.$image->getClientOriginalName();
            $image->move('products_image',$image_name);

            $products->gallery_image=$image_name;

            //thumbnail_image
            $image = $request->input('thumbnail_image');
            $image_name=time().'.'.$image->getClientOriginalName();
            $image->move('products_image',$image_name);

            $products->thumbnail_image=$image_name;

            //pdf_specs
            $file = $request->input('pdf_specs');
            $file_name=time().'.'.$file->getClientOriginalName();
            $file->move('products_image',$file_name);

            $products->pdf_specs=$file_name;

            $products->name=$request->name;
            $products->quantity=$request->quantity;
            $products->description=$request->description;
            $products->tags=$request->tags;
            $products->units=$request->units;
            $products->sku=$request->sku;
            $products->category=$request->category;
            $products->minimum_purchase_quantity=$request->minimum_purchase_quantity;
            $products->unit_price=$request->unit_price;

            $products->save();


            return $this->sendResponse(new ProductResource($products),'Product added successfully.');

        }

    }

    public function show($id)
    {
        $products=Product::find($id);

        return $this->sendResponse(ProductResource::collection($products), 'Products fetched for edit.');

    }

   /* public function edit($id)
    {
        $products=Product::find($id);

        return $this->sendResponse(ProductResource::collection($products), 'Products fetched for edit.');
    }
    */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [

            'name' =>'required|string',
            'gallery_image'=>'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'thumbnail_image'=>'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'description' =>'required|string',
            'quantity' =>'required|string',
            'unit_price' =>'required',
            'category'=>'required|string',
            'tags'=>"required|string",
            'units'=>"required|string",
            'minimum_purchase_quantity'=>"required|string",
            'sku'=>"string",
            'pdf_specs'=>'pdf|max:2048'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        else{

            $products=Product::find($id);

            $image = $request->gallery_image;

            if($image)
              {

                $image_name=time().'.'.$image->getClientOriginalName();
                $image->move('products_image',$image_name);

                $products->gallery_image=$image_name;

              }


              $thumbnail_image=$request->thumbnail_image;

              if($thumbnail_image)
              {
                $image_name=time().'.'.$thumbnail_image->getClientOriginalName();
                $thumbnail_image->move('products_image',$image_name);

                $products->thumbnail_image=$image_name;
              }


              $pdf_specs=$request->pdf_specs;

              if ($pdf_specs)
               {
                  $file_name=time().'.'.$pdf_specs->getClientOriginalName();
                  $pdf_specs->move('products_image',$file_name);

                  $products->pdf_specs=$pdf_specs;
               }

            $products->name=$request->name;
            $products->quantity=$request->quantity;
            $products->description=$request->description;
            $products->tags=$request->tags;
            $products->units=$request->units;
            $products->sku=$request->sku;
            $products->category=$request->category;
            $products->minimum_purchase_quantity=$request->minimum_purchase_quantity;
            $products->unit_price=$request->unit_price;


            $products->save();

            return $this->sendResponse(new ProductResource($products), 'Product updated successfully.');


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $products=Product::find($id);

        if (is_null($products)) {
            return $this->sendError('Product not found!', [], 404);
        }

        $products->delete();

        return $this->sendResponse([], 'Product Successfully Deleted!');
    }

}


