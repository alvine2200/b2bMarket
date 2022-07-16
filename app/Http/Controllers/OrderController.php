<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\ProductsServices\Product;
use App\Models\Business;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use PDF;

class OrderController extends BaseController
{

    public function place_order(Request $request)
    {

        $invoice_id = random_bytes(10);

        $order_id= random_int(100, 1000000);


        $order_date=now()->format('Y-m-d H:i:s');

        $validator = Validator::make($request->all(),[

            'full_name' =>'required|string',
            'business_name'=>'required|string',
            'address'=>'required|string',
            'country'=>'required|string',
            'city'=>'required|string',
            'street'=>'required|string',
            'phone'=>'required|string',
            'email'=>'required|email|unique:users',
        ]);
/*
        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

      //checks for business id, if no business then return nullable
       $business=Business::where('user_id',Auth::user()->id)->first();

        else
        {
            $order = new Order();
            $order->user_id=Auth::user()->id;
            $order->business_id=$business->id;
            $order->full_name=$request->full_name;
            $order->business_name=$request->business_name;
            $order->address=$request->address;
            $order->country=$request->country;
            $order->city=$request->city;
            $order->phone=$request->phone;
            $order->street=$request->street;
            $order->email=$request->email;
            $order->order_id=(bin2hex($order_id));
            $order->invoice_id=(bin2hex($invoice_id));
            $order->order_date=$order_date;
            //$order->total=$request->total; //only for testing
            $order->payment_method=$request->payment_method;
            $order->shipping_method=$request->shipping_method;

            //calculate total order Price
            $sum=Cart::where('user_id', Auth::user()->id)->sum('price')->first();
            $order->total=$sum;
            $order->save();
           /* $cart_total=0;

            $cart_items_total= Cart::where('user_id',Auth::id())->first();

            foreach ($cart_items_total as $item_total)
            {
                $cart_total +=$item_total->products->price;
            }
            */
/*
            //from orders table an invoice is generated and and stored in invoice table you can print it for future reference
            Invoice::create([

                'order_id' => $order->order_id,
                'user_id' => $order->user_id,
                'business_id'=> $order->business_id,
                'invoice_id'=> $order->invoice_id,
                'order_date'=> $order->order_date,
                'total'=> $order->total,

            ]);


            //after inserting order in order table, inserts ordered items in orderitems table
            $cart_items = Cart::where('user_id',Auth::id())->first();

            foreach($cart_items as $item)
            {
                OrderItem::create([

                    'user_id'=>Auth::id(),
                    'order_id'=>$order->id,
                    'product_id'=>$item->product_id,
                    'quantity'=>$item->product_qty,
                    'price'=>$item->products->price,

                ]);


                //after every order minus the order quantity from Product quantity
                $product=Product::where('id',$item->product_id)->first();
                $product->quantity=$product->quantity-$item->product_qty;

            }


        //after placing an order destroy the items from cart to null after  checking user_id and Auth::id()
            $cart_items = Cart::where('user_id',Auth::id())->first();
            $cart_items->destroy($cart_items);



         return $this->SendResponse(new OrderResource($order),'Order successfully Completed, invoice generated');

            //seed payment_method,shipping_method


        }
        */
   }


   //for individual/business order view
   public function view_my_orders()
      {

        $user_id=Auth::user()->id;
        $user=Order::where('user_id',$user_id)->get();

        return $this->SendResponse(new OrderResource($user),'Your Orders are fetched');

   }

   //will be only for admin use
   public function view_order()
   {
       $order=Order::all();

       return $this->SendResponse(OrderResource::collection($order),'All Orders Fetched');
   }

    //after invoice is processed, needs to be downloaded maybe, require dompdf package
  /* public function generate_invoice()
   {
       //find a good invoice view and connect it here to download
     $pdf = PDF::loadView('Invoice_view');

     return $pdf->download('invoice.pdf');
   }
   */


}
