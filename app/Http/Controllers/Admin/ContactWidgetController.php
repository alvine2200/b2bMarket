<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ContactWidget;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ContactWidgetResource;

class ContactWidgetController extends BaseController
{
    public function post_contact(Request $request)
    {
        $validator= Validator::make($request->all(), [
            'address'=>'required|string',
            'phone'=>'required|string',
            'email'=>'required|email',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $contacts=ContactWidget::firstOrCreate([
            'address'=>$request->address,
            'phone'=>$request->phone,
            'email'=>$request->email,
        ]);

        return $this->SendResponse(new ContactWidgetResource($contacts),'Posted Successfully');
    }

    public function show_contact()
    {
        $contacts=ContactWidget::all();

        return $this->SendResponse(ContactWidgetResource::collection($contacts),'contacts retrieved successfully');
    }

    public function edit_contact($id)
    {
        $contacts=ContactWidget::find($id);

        return $this->SendResponse(new ContactWidgetResource($contacts),'Contact is fetched');
    }

    public function update_contact(Request $request,$id)
    {
        $validator= Validator::make($request->all(), [
            'address'=>'required|string',
            'phone'=>'required|string',
            'email'=>'required|email',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }
        $contacts=ContactWidget::find($id);

        $contacts->address=$request->address;
        $contacts->phone=$request->phone;
        $contacts->email=$request->email;

        $contacts->update();

        return $this->SendResponse(new ContactWidgetResource($contacts),'Contact updated successfully');

    }

    public function delete_contact($id)
    {
        $contacts=ContactWidget::find($id);
        $contacts->delete();

        return $this->SendResponse([],'contact deleted successfully');
    }
}
