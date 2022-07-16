<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services=Service::all();

        return $this->SendResponse(ServiceResource::collection($services),'Service Home');
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
        $validator= Validator::make($request->all(), [

            'name'=>'required|string',
            'summary'=>'required|string',
            'price'=>'required|string',

        ]);

        if($validator->fails())
        {
            return $this->SendError('Error Validation', $validator->errors(), 400);
        }

        $services= new Service();
        $services->name=$request->name;
        $services->summary=$request->summary;
        $services->price=$request->price;

        $services->save();

        return $this->SendResponse(new ServiceResource($services),'New Service Successfully Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $services=Service::find($id);

        if(is_null($services))
        {
            return $this->SendError('Service Not Found!');
        }

        return $this->SendResponse(new ServiceResource($services),'Services fetched successfully');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $services=Service::find($id);

        $validator= Validator::make($request->all(), [

            'name'=>'required|string',
            'summary'=>'required|string',
            'price'=>'required|string',

        ]);

        if($validator->fails())
        {
            return $this->SendError('Error Validation', $validator->errors(), 400);
        }

        $services= new Service();
        $services->name=$request->name;
        $services->summary=$request->summary;
        $services->price=$request->price;

        $services->save();

        return $this->SendResponse(new ServiceResource($services),'New Service Successfully Added');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $services=Service::find($id);

        if(is_null($services))
        {
            return $this->SendError('Service Not Found!');
        }

        $services->delete();

        return $this->SendResponse([],'Service deleted successfully!');
    }
}
