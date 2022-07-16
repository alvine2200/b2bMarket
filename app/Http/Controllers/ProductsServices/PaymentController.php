<?php

namespace App\Http\Controllers\ProductsServices;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductsServices\Payment as PaymentResource;
use App\Models\ProductsServices\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends BaseController
{
    //
    public function sentPayments(Request $request){
        $authUser = User::find(Auth::user()->id);

        $payments = Payment::whereHas("order", function($query) use ($authUser){
            $query->where("user_id", $authUser->id);
        })->get();

        return $this->sendResponse(PaymentResource::collection($payments), 'Sent Payments retrieved successfully');
    }
}
