<?php

namespace App\Http\Controllers\Admin;

use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SupportReplyResource;

class SupportReplyController extends BaseController
{
    public function get_all_tickets()
    {
        $tickets=Support::latest()->get();

        return $this->SendResponse(SupportReplyResource::collection($tickets),'All Ticket Fetched');
    }

    public function reply_to_ticket(Request $request, $id)
    {
        $image=$request->file('image')->store('image');

        $validator= Validator::make($request->all(), [
            'reply_message'=>'required|string',
            'image'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);
        if($validator->fails()){

            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $ticket=Support::find($id);

        if($ticket===null){
            return $this->SendError([],'No Ticket matched the Id');
        }

        $ticket->last_reply= \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        $ticket->reply_message=$request->reply_message;
        $ticket->image=$image;
        $ticket->status='Replied';

        $ticket->update();

        return $this->SendResponse(new SupportReplyResource($ticket),'Reply Sent');

    }

    public function resolve_ticket($id)
    {
        $resolve=Support::find($id);

        $resolve->status='Resolved';

        $resolve->update();

        return $this->SendResponse(new SupportReplyResource($resolve),'Ticket Resolved');

    }


}
