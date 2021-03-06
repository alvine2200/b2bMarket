<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Paypal_Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaypalResource;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PayPalController extends BaseController
{

    public function createpaypal()
    {
        // return api for checkout button
        return view('paypal.paypal_view');

    }


    public function processPaypal(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        //generated by business
        $invoice_id = random_bytes(5);
        //unique generated number
        $order_id = random_int(100, 10000);

        $order_date = \Carbon\Carbon::now();

        //during ordering you must belong to a business for you to order
       // $business = Business::where('user_id', Auth::id())->first();

        //get cart_total
        $sum=Cart::where('user_id', Auth::user()->id)->sum('price')->get();


        //all this details to be in the checkout form
        $order= $request->input('paid_by');
        $order= $request->input('paid_to');
        $order= $request->input('email');
        $order= $request->input('phone');
        $order= $request->input('country');
        $order= $request->input('city');
        $order= $request->input('address');
        $order=$invoice_id;
        $order=$order_id;
        $order=$order_date;
       // $order->business_id=$business->id;
        $order=$request->input('business_name');
        $order=$sum;
        $order= $request->input('shipping_method');
        $order= $request->input('payment_method');

        $response = $provider->createOrder([

            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('processSuccess'),
                "cancel_url" => route('processCancel'),
            ],

            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" =>$sum,
                    ]
                ]
            ],
            [
                'order_date'=>$order_date,
                'order_id'=>$order_id,
                'invoice_id'=>$invoice_id,                
                'business_name'=>$request->business_name,
                'paid_by'=>$request->paid_by,
                'paid_to'=>$request->paid_to,

            ],
        ]);

        Order::create([

            'referrence_number' =>$response['id'],
            'order_status' =>$response['status'],
            'email'=>$request->email,
            'paid_by' =>$request->paid_by,
            'paid_to'=>$request->paid_to,
            'order_id'=>$order_id,
            'invoice_id'=>$invoice_id,
            'order_date'=>$order_date,
            'shipping_method' =>$request->shipping_method,
            'payment_method' =>$request->payment_method,
            'phone' =>$request->phone,            
            'address' =>$request->address,
            'country' =>$request->country,
            'business_name'=>$request->business_name,
            'user_id'=>Auth::user()->id,
            //'business_id'=>$business->id,
            'total' =>$sum,   

        ]);

        //creates transactions table
        Paypal_Payment::create([

            'referrence_number' =>$response['id'],
            'order_status' =>$response['status'],
            'order_date' =>$order_date,
            'order_id'=>$order_id,
            'invoice_id'=>$invoice_id,
            'paid_by'=>$request->paid_by,
            'paid_to'=>$request->paid_to,

        ]);



        if (isset($response['id']) && $response['id'] != null) {

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

             return $this->SendError([],'Something went wrong, please try again');



        }
        else
        {
            return $this->SendError('Something went wrong, please try again');
        }
    }


    public function processSuccess(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            DB::table('paypal_payments')
                ->where('referrence_number', $response['id'])
                ->update(['order_status' => 'COMPLETED', 'updated_at' => \Carbon\Carbon::now()]);

            return $this->sendResponse(new PaypalResource($response), 'Transactions completed successfully');
        
        }
         else 
         {

            return $this->SendError([], 'Something went wrong, please try again');

            /*
            return redirect()
                ->route('createpaypal')
                ->with('error', $response['message'] ?? 'Something went wrong.');
               */
        }
    }

    public function processCancel(Request $request)
    {
        /*  return redirect()
            ->route('createpaypal')
            ->with('error', $response['message'] ?? 'You have canceled the transaction.');

        */
        return $this->SendError([], 'Transaction cancelled successfully');
    }
}
