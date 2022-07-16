<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableGender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableGender as SelectableGenderResource;

class SelectableGenderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $genders = SelectableGender::all();
        return $this->sendResponse(SelectableGenderResource::collection($genders), 'Selectable Genders fetched.');
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

        $gender = SelectableGender::create($input);
        return $this->sendResponse(new SelectableGenderResource($gender), 'Selectable Gender created.');
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
        $gender = SelectableGender::find($id);
        if (is_null($gender)) {
            return $this->sendError('Selectable Gender not found!');
        }
        return $this->sendResponse(new SelectableGenderResource($gender), 'Selectable Gender retrieved.');
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
        $gender = SelectableGender::find($id);
        if (is_null($gender)) {
            return $this->sendError('Selectable Gender not found!');
        }
        
        $input = $request->all();
        $gender->update($input);

        return $this->sendResponse(new SelectableGenderResource($gender), 'Selectable Gender successfully updated.');
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
        $gender = SelectableGender::find($id);
        if (is_null($gender)) {
            return $this->sendError('Selectable Gender not found!');
        }

        $gender->delete();
        return $this->sendResponse([], 'Selectable Gender deleted.');

    }
}
