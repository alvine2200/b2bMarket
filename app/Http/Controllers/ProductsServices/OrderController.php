<?php

namespace App\Http\Controllers\ProductsServices;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductsServices\DeliveryAddress as DeliveryAddressResource;
use App\Http\Resources\ProductsServices\Payment as PaymentResource;
use App\Http\Resources\ProductsServices\PurchaseOrderBought;
use App\Http\Resources\ProductsServices\PurchaseOrderSold;
use App\Mail\Invoice as MailInvoice;
use App\Mail\Receipt;
use App\Models\ProductsServices\DeliveryAddress;
use App\Models\ProductsServices\Invoice;
use App\Models\ProductsServices\Payment;
use App\Models\ProductsServices\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    public function getOrder(Request $request)
    {
        $authUser = User::find(Auth::user()->id);

        $order = PurchaseOrder::where("user_id", $authUser->id)->where("status", "!=", "Complete")->first();
        if ($order == null) {
            return $this->sendError("Purchase order not found", [], 404);
        }

        return $this->sendResponse(new PurchaseOrderBought($order), 'Purchase order retrieved successfully');
    }

    //
    public function addShippingInformation(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "postal_code" => "required",
            "city" => "required",
            "country" => "required",
            "phone" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $inp = $request->all();
        $inp["user_id"] = $authUser->id;

        DeliveryAddress::create($inp);

        return $this->sendResponse([], 'Delivery address added successfully');
    }

    public function listShippingInformation(Request $request)
    {
        $authUser = User::find(Auth::user()->id);

        // $deliveryAddresses = $authUser->deliveryAddresses();
        $deliveryAddresses = DeliveryAddress::where("user_id", $authUser->id)->get();

        return $this->sendResponse(DeliveryAddressResource::collection($deliveryAddresses), 'Delivery address listed successfully');
    }

    public function setShippingInformation(Request $request, $id)
    {
        $authUser = User::find(Auth::user()->id);
        $deliveryAddress = DeliveryAddress::find($id);
        if ($deliveryAddress == null) {
            return $this->sendError("Delivery Address with id '$id' not found", [], 404);
        }

        $order = PurchaseOrder::where("user_id", $authUser->id)->where("status", "!=", "Complete")->first();
        if ($order == null) {
            return $this->sendError("Purchase order not found", [], 404);
        }

        $order->delivery_address_id = $id;
        $order->status = "Set Delivery Address";
        $order->save();

        return $this->sendResponse(new PurchaseOrderBought($order), 'Delivery address set successfully');
    }

    public function setPaymentMode(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            "payment_mode" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $order = PurchaseOrder::where("user_id", $authUser->id)->where("status", "!=", "Complete")->first();
        if ($order == null) {
            return $this->sendError("Purchase order not found", [], 404);
        }

        $order->payment_mode = $request->payment_mode;
        $order->status = "Set Payment Mode";
        $order->save();

        return $this->sendResponse(new PurchaseOrderBought($order), 'Payment mode set successfully');
    }

    public function setDeliveryMode(Request $request)
    {
        $authUser = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            "delivery_mode" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $order = PurchaseOrder::where("user_id", $authUser->id)->where("status", "!=", "Complete")->first();
        if ($order == null) {
            return $this->sendError("Purchase order not found", [], 404);
        }

        $order->delivery_mode = $request->delivery_mode;
        $order->status = "Set Delivery Mode";
        $order->save();

        return $this->sendResponse(new PurchaseOrderBought($order), 'Delivery mode set successfully');
    }

    public function completeOrder(Request $request)
    {
        $authUser = User::find(Auth::user()->id);

        $order = PurchaseOrder::where("user_id", $authUser->id)->where("status", "!=", "Complete")->first();
        if ($order == null) {
            return $this->sendError("Purchase order not found", [], 404);
        }

        # todo: move cart items to order items
        $cartItems = $authUser->cart->products()->withPivot('quantity')->get();
        foreach ($cartItems as $cartItem) {
            $order->products()->sync([$cartItem->id => ["quantity" => $cartItem->pivot->quantity]], false);
        }

        # invoice
        # generate random invoice number using time and date
        $carbonNow = now();
        $invoiceNumber = "B" . $carbonNow->timestamp . "I";
        $invoice = $order->invoice;
        // dd($invoice);
        if($invoice == null){
            $invoice = Invoice::Create(
                [
                    "purchase_order_id" => $order->id,
                    "invoice_number" => $invoiceNumber
                ]
            );
        }

        // todo: uncomment to send invoice email
        // Mail::to("steve.wandie90@gmail.com")->send(new MailInvoice($invoice));


        # todo: payment mode link to transaction

        $order->status="Complete";
        $order->save();

        # clear cart
        $authUser->cart->delete();

        return $this->sendResponse(new PurchaseOrderBought($order), 'Order completed successfully');
    }

    private function sendReceipt($payment){
        $order = $payment->order;
        $payee = $order->user;

        return view('receipt')->with(compact('payment', 'order', 'payee'));
    }

    public function payOrder(Request $request, $id)
    {
        $authUser = User::find(Auth::user()->id);
        $order = PurchaseOrder::where("id", $id)->where("status", "Complete")->first();
        if ($order == null) {
            return $this->sendError("Purchase order not found", [], 404);
        }

        $validator = Validator::make($request->all(), [
            "amount" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        # generate random payment number using time and date
        $carbonNow = now();
        $paymentNumber = "B" . $carbonNow->timestamp . "P";
        $payment = Payment::create([
            "user_id" => $authUser->id,
            "purchase_order_id" => $id,
            "amount" => $request->amount,
            "payment_number"=>$paymentNumber
        ]);

        // todo: uncomment to send receipt email
        // Mail::to("steve.wandie90@gmail.com")->send(new Receipt($payment));

        return $this->sendResponse(new PaymentResource($payment), 'Payment proccessed successfully');
    }

    public function boughtOrders(Request $request){
        $authUser = User::find(Auth::user()->id);
        $orders = PurchaseOrder::where("user_id", $authUser->id)->get();

        return $this->sendResponse(PurchaseOrderBought::collection($orders), 'Purchase orders retrieved successfully');
    }

    public function soldOrders(Request $request){
        $user = User::find(Auth::user()->id);
        $business = $user->business;

        $orders = PurchaseOrder::whereHas("products", function($query) use ($business) {
            $query->where("business_id", $business->id);
        })->get();

        return $this->sendResponse(PurchaseOrderSold::collection($orders), 'Orders sold retrieved successfully');
    }
}
