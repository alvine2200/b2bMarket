<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SupportResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class SupportController extends BaseController
{
    public function create_ticket(Request $request)
    {
        $photo=$request->file('photo')->store('photo');

        $validator= Validator::make($request->all(), [
            'subject'=>'required|string',
            'description'=>'required',
            'photo'=>'mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if($validator->fails())
        {
            return $this->SendError('validation failed',$validator->errors(), 400);
        }


        $support=new Support();
        $support->subject=$request->subject;
        $support->description=$request->description;
        $support->ticket_number=mt_rand(1000000,9999999);
        $support->photo=$photo;
        $support->user_id=Auth::user()->id;
        $support->sending_date= \Carbon\Carbon::now('GMT')->format('Y-m-d H:i:s');

        $support->save();

        return $this->SendResponse(new SupportResource($support),'Ticket Submitted Successfully');
    }


    public function cancel_ticket($id)
    {

        $support=Support::find($id);

        if($support == null)
        {
            return $this->SendError([],'Sorry, Ticket id Not Found');
        }

        $support->delete();

        return $this->SendResponse([],'Ticket Cancelled Successfully');
    }

    public function update_ticket(Request $request, $id)
    {
        $photo = $request->file('photo')->store('photo');

        $validator= Validator::make($request->all(), [
            'subject'=>'required|string',
            'description'=>'required',
            'photo'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('validation failed',$validator->errors(), 400);
        }

        $support=Support::find($id);

        if($support==null)
        {
            return $this->SendError([],'Sorry, Id not found');
        }

        $support->subject=$request->subject;
        $support->description=$request->description;
        $support->photo=$photo;
        $support->user_id=Auth::user()->id;

        $support->update();

        return $this->SendResponse(new SupportResource($support),'Ticket is updated successfully');

    }

    public function show_ticket($id)
    {
        $support=Support::find($id);

        if($support == null)
        {
            return $this->sendError([],'sorry, id not found');
        }
        return $this->SendResponse(new SupportResource($support),'Ticket Retrieved successfully');
    }

    public function user_tickets($id)
    {

        $support=Support::where('user_id',$id)->get();

        if($support === null)
           {
             return $this->SendError([],'Sorry id not found');
           }

        return $this->SendResponse(new SupportResource($support),'Users tickets retrieved successfully');

    }

    public function resolve_ticket($id)
    {
        $tickets=Support::find($id);
        $tickets->status='Resolved';

        $tickets->update();

        return $this->SendResponse(new SupportResource($tickets),'Ticket Resolved successfully');
    }
}
