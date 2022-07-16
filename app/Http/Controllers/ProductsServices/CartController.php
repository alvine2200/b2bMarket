<?php

namespace App\Http\Controllers\ProductsServices;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductsServices\Cart as CartResource;
use App\Models\ProductsServices\Cart;
use App\Models\ProductsServices\Product;
use App\Models\ProductsServices\PurchaseOrder;
use App\Models\ProductsServices\SelectablePurchaseOrderTrackType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseController
{
    //
    public function displayCart(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $cart = Cart::firstOrCreate(["user_id" => $authUser->id]);

        return $this->sendResponse(new CartResource($cart), 'Cart retrieved successfully');
    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'product_qty' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUser = User::find(Auth::user()->id);
        $cart = Cart::firstOrCreate(["user_id" => $authUser->id]);

        $product = Product::find($request->product_id);

        $cart->products()->sync([$product->id => ["quantity" => $request->product_qty]], false);

        return $this->sendResponse([], 'Product added to cart successfully');
    }

    public function updateProductQty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'product_qty' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUser = User::find(Auth::user()->id);
        $cart = Cart::firstOrCreate(["user_id" => $authUser->id]);

        $product = Product::find($request->product_id);

        $cart->products()->sync([$product->id => ["quantity" => $request->product_qty]], false);

        return $this->sendResponse([], 'Product quantity updated successfully');
    }

    public function removeProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product == null) {
            return $this->sendError("Product with id '$id' not found", [], 404);
        }

        $authUser = User::find(Auth::user()->id);
        $cart = Cart::firstOrCreate(["user_id" => $authUser->id]);

        $cart->products()->detach($product->id);

        return $this->sendResponse([], 'Product removed from cart successfully');
    }

    public function clearCart(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $cart = $authUser->cart;

        if ($cart != null) {
            $cart->delete();
        }

        return $this->sendResponse([], 'Cart cleared successfully');
    }

    public function proceedToShipping(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $cart = $authUser->cart;

        if (!$cart->has("products")) {
            return $this->sendError("Cart has no items", [], 400);
        }

        # generate random order number using time and date
        $carbonNow = now();
        $orderNumber = "B" . $carbonNow->timestamp . "O";

        # new purchase order
        $purchaseOrder = PurchaseOrder::where("user_id", $authUser->id)->where("status","!=","Complete")->first();
        if($purchaseOrder == null){
            $purchaseOrder = PurchaseOrder::create(
                [
                    "user_id" => $authUser->id,
                    "order_number" => $orderNumber,
                    "status" => "New"
                ]
            );
        }

        return $this->sendResponse([], 'Order created successfully');
    }
}
