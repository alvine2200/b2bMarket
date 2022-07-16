<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableInvestorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableInvestorType as SelectableInvestorTypeResource;

class SelectableInvestorTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SelectableInvestorType::query();
        if($q = $request->input('q')){
            $query->where('name', 'like', "%$q%");
        }

        $investorTypes = $query->get();
        return $this->sendResponse(SelectableInvestorTypeResource::collection($investorTypes), 'Selectable investor Types fetched.');
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

        $investorType = SelectableInvestorType::create($input);
        return $this->sendResponse(new SelectableInvestorTypeResource($investorType), 'Selectable investor Type created.');
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
        $investorType = SelectableInvestorType::find($id);
        if (is_null($investorType)) {
            return $this->sendError('Selectable investor Type not found!');
        }
        return $this->sendResponse(new SelectableInvestorTypeResource($investorType), 'Selectable investor Type retrieved.');
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
        $investorType = SelectableInvestorType::find($id);
        if (is_null($investorType)) {
            return $this->sendError('Selectable investor Type not found!');
        }
        
        $input = $request->all();
        $investorType->update($input);

        return $this->sendResponse(new SelectableInvestorTypeResource($investorType), 'Selectable investor Type successfully updated.');
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
        $investorType = SelectableInvestorType::find($id);
        if (is_null($investorType)) {
            return $this->sendError('Selectable investor Type not found!');
        }

        $investorType->delete();
        return $this->sendResponse([], 'Selectable investor Type deleted.');

    }
}
