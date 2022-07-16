<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableCountry as SelectableCountryResource;
use Exception;

class SelectableCountryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SelectableCountry::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%")
                ->orWhere('code_iso2', 'like', "%$q%")
                ->orWhere('code_iso3', 'like', "%$q%")
                ->orWhere('phone_code', 'like', "%$q%")
                ->orWhere('continent', 'like', "%$q%")
                ->orWhere('capital', 'like', "%$q%")
                ->orWhere('currency', 'like', "%$q%");
        }


        $countries = $query->get();

        return $this->sendResponse(SelectableCountryResource::collection($countries), 'Selectable Countries fetched.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return "Functionality currently disabled";
        $input = $request->all();
        // dd($input);
        $validator = Validator::make($input, [
            'name' => 'required',
            'code' => 'required',
            'continent' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), code: 400);
        }

        $country = SelectableCountry::create($input);
        return $this->sendResponse(new SelectableCountryResource($country), 'Selectable Country created.');
    }

    /**
     * Store newly created countries in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStore(Request $request)
    {
        return "Functionality currently disabled";
        //
        $input = $request->all();
        // dd($input);
        $validator = Validator::make($input, [
            'countries' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), code: 400);
        }

        foreach ($input['countries'] as $country) {
            if (SelectableCountry::where('name', $country['countryName'])->doesntExist()) {
                SelectableCountry::create([
                    'name' => $country['countryName'],
                    'code_iso2' => $country['countryCode'],
                    'continent' => $country['continentName'],
                    'capital' => $country['capital'],
                    'currency' => $country['currencyCode'] != null ? $country['currencyCode'] : 'unknown',
                ]);
            }
        }

        $countries = SelectableCountry::all();
        return $this->sendResponse(SelectableCountryResource::collection($countries), 'Selectable Countries bulk created.');
    }


    /**
     * Update bulk countries in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(Request $request)
    {
        return "Functionality currently disabled";
        //
        $input = $request->all();
        // dd($input);
        $countryKey = $request->query('country_key');
        $toCol = $request->query('to_col');
        $fromCol = $request->query('from_col');

        foreach ($input as $country) {
            try{
                if (SelectableCountry::where('name', $country[$countryKey])->exists()) {
                    SelectableCountry::where('name', $country[$countryKey])
                        ->update([
                            $toCol => $country[$fromCol],
                        ]);
                }
            }
            catch(Exception $e){
                dd($country);
            }
        }

        $countries = SelectableCountry::all();
        return $this->sendResponse(SelectableCountryResource::collection($countries), 'Selectable Countries bulk created.');
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
        $selectableCountry = SelectableCountry::find($id);
        if (is_null($selectableCountry)) {
            return $this->sendError('Selectable Country not found!');
        }
        return $this->sendResponse(new SelectableCountryResource($selectableCountry), 'Selectable Country retrieved.');
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
        return "Functionality currently disabled";
        //
        $country = SelectableCountry::find($id);
        if (is_null($country)) {
            return $this->sendError('Selectable Country not found!');
        }

        $input = $request->all();
        $country->update($input);

        return $this->sendResponse(new SelectableCountryResource($country), 'Selectable Country updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return "Functionality currently disabled";
        //
        $country = SelectableCountry::find($id);
        if (is_null($country)) {
            return $this->sendError('Selectable Country not found!');
        }

        $country->delete();
        return $this->sendResponse([], 'Selectable Country deleted.');
    }
}
