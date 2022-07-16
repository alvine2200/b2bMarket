<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableBusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableBusinessType as SelectableBusinessTypeResource;

class SelectableBusinessTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $businessType = SelectableBusinessType::all();
        return $this->sendResponse(SelectableBusinessTypeResource::collection($businessType), 'Selectable Business types fetched.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        // dd($input);
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors(), code : 400);       
        }

        $businessType = SelectableBusinessType::create($input);
        return $this->sendResponse(new SelectableBusinessTypeResource($businessType), 'Selectable Types created.');
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
        $businessType = SelectableBusinessType::find($id);
        if (is_null($businessType)) {
            return $this->sendError('Selectable Business Type not found!');
        }
        return $this->sendResponse(new SelectableBusinessTypeResource($businessType), 'Selectable Business Types retrieved.');
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
        //
        $businessType = SelectableBusinessType::find($id);
        if (is_null($businessType)) {
            return $this->sendError('Selectable Business Type not found!');
        }
        
        $input = $request->all();
        $businessType->update($input);

        return $this->sendResponse(new SelectableBusinessTypeResource($businessType), 'Selectable Business Type successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $businessType = SelectableBusinessType::find($id);
        if (is_null($businessType)) {
            return $this->sendError('Selectable Business Type not found!');
        }

        $businessType->delete();
        return $this->sendResponse([], 'Selectable Business Type deleted.');

    }
}
