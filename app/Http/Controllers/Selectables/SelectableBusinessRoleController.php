<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableBusinessRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableBusinessRole as SelectableBusinessRoleResource;

class SelectableBusinessRoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SelectableBusinessRole::query();
        if($q = $request->input('q')){
            $query->where('name', 'like', "%$q%");
        }

        $businessRoles = $query->get();
        return $this->sendResponse(SelectableBusinessRoleResource::collection($businessRoles), 'Selectable Business Roles fetched.');
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

        $businessRole = SelectableBusinessRole::create($input);
        return $this->sendResponse(new SelectableBusinessRoleResource($businessRole), 'Selectable Business Role created.');
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
        $businessRole = SelectableBusinessRole::find($id);
        if (is_null($businessRole)) {
            return $this->sendError('Selectable Business Role not found!');
        }
        return $this->sendResponse(new SelectableBusinessRoleResource($businessRole), 'Selectable Business Role retrieved.');
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
        $businessRole = SelectableBusinessRole::find($id);
        if (is_null($businessRole)) {
            return $this->sendError('Selectable Business Role not found!');
        }
        
        $input = $request->all();
        $businessRole->update($input);

        return $this->sendResponse(new SelectableBusinessRoleResource($businessRole), 'Selectable Business Role successfully updated.');
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
        $businessRole = SelectableBusinessRole::find($id);
        if (is_null($businessRole)) {
            return $this->sendError('Selectable Business Role not found!');
        }

        $businessRole->delete();
        return $this->sendResponse([], 'Selectable Business Role deleted.');

    }
}
