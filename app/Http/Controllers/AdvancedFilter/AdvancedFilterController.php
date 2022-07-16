<?php

namespace App\Http\Controllers\AdvancedFilter;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AdvancedFilter\AdvancedFilterRecentSearch as AdvancedFilterRecentSearchResource;
use App\Models\AdvancedFilter\AdvancedFilterRecentSearch;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Selectables\SelectableCountry;
use App\Models\Selectables\SelectableContinent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdvancedFilterController extends BaseController
{
    //
    public function businesses(Request $request){
        $rangeorPlusRegex = "/(^\s*([0-9]+)\s*.{1}\s*([0-9]+)\s*$)|(^([0-9]+)\s*\+$)/";
        $validator = Validator::make($request->all(), [
            'country' => 'exists:selectable_countries,name',
            'region' => 'exists:selectable_countries,continent',
            'size' => "regex:$rangeorPlusRegex",
            'age' => "regex:$rangeorPlusRegex",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $suffixMatchKey = '';
        $query = Business::query();

        # name
        if($name = $request->query('name')){
            $suffixMatchKey .= "$name";
            
            $query->where("name","like","%$name%");
        }
        $suffixMatchKey.="-";
        # country
        if($raw_country = $request->query('country')){
            $suffixMatchKey .= "$raw_country";
            $country = SelectableCountry::firstWhere("name", $raw_country);
            if($country != null){
                $query->where(function($s_query) use ($raw_country){
                    $s_query->whereHas("headquarters", function($ss_query) use ($raw_country){
                        $ss_query->where('name',$raw_country);
                    });
                    $s_query->orWhereHas('operating_countries', function ($ss_query) use ($raw_country) {
                        $ss_query->where('name', $raw_country);
                    });
                });
            }
        }
        $suffixMatchKey.="-";
        # region
        if($raw_region = $request->query('region')){
            $suffixMatchKey .= "$raw_region";
            $region = SelectableContinent::firstWhere("name", $raw_country);
            if($region!=null){ //
                $query->where(function($s_query) use ($raw_region){
                    $s_query->whereHas("headquarters", function ($ss_query) use ($raw_region) {
                        $ss_query->where('continent', $raw_region);
                    });
    
                    $s_query->orWhereHas('operating_countries', function ($ss_query) use ($raw_region) {
                        $ss_query->where('continent', $raw_region);
                    });
                });
            }
        }
        $suffixMatchKey.="-";
        # size
        $sizeRange = [];
        if($size = $request->query('size')){
            $suffixMatchKey .= "$size";
            $sizesMatch = [];
            preg_match($rangeorPlusRegex, $size, $sizesMatch);
            $sizeRange["size_start_range"] = $sizesMatch[2] != "" ? $sizesMatch[2] : $sizesMatch[5];
            $sizeRange["size_end_range"] = $sizesMatch[3] != "" ? $sizesMatch[3] : null;
            if($sizesMatch[3] != ""){
                $sizeValidator = Validator::make($sizeRange, [
                    'size_start_range'=>"lte:size_end_range",
                ]);
                if($sizeValidator->fails()){
                    return $this->sendError('Error validation', $sizeValidator->errors());
                }
            }

            $query->where("size_start_range", ">=", $sizeRange["size_start_range"]);
            if($sizeRange["size_end_range"]){
                $query->where("size_end_range", "<=", $sizeRange["size_end_range"]);
            }
        }
        $suffixMatchKey.="-";
        # age range
        $ageRange = [];
        if($age = $request->query('age')){
            $suffixMatchKey .= "$age";
            $agesMatch = [];
            preg_match($rangeorPlusRegex, $age, $agesMatch);
            $ageRange["age_start_range"] = $agesMatch[2] != "" ? $agesMatch[2] : $agesMatch[5];
            $ageRange["age_end_range"] = $agesMatch[3] != "" ? $agesMatch[3] : null;
            if($agesMatch[3] != ""){
                $ageValidator = Validator::make($ageRange, [
                    'age_start_range'=>"lte:age_end_range",
                ]);
                if($ageValidator->fails()){
                    return $this->sendError('Error validation', $ageValidator->errors());
                }
            }

            $query->where("age_start_range", ">=", $ageRange["age_start_range"]);
            if($ageRange["age_end_range"]!=null) {
                $query->where("age_end_range", "<=", $ageRange["age_end_range"]);
            }

        }
        $suffixMatchKey.="-";
        # business_type
        if ($business_type = $request->query("business_type")) { //
            $query->where('business_type', $business_type);
            $suffixMatchKey.="$business_type";
        }
        $suffixMatchKey.="-";
        # sector
        if ($sector = $request->query("sector")) { //
            $query->where(function($s_query) use ($sector){
                $s_query->whereHas("mainSector", function($ss_query) use ($sector){
                    $ss_query->where('name',$sector);
                });
                
                $s_query->orWhereHas('otherSectors', function ($ss_query) use ($sector) {
                    $ss_query->where('name', $sector);
                });
                
                $s_query->orWhereHas('sectorInterests', function ($ss_query) use ($sector) {
                    $ss_query->where('name', $sector);
                });
            });
            $suffixMatchKey.="$sector";
        }
        $suffixMatchKey.="-";


        $businesses = $this->matchingBusinesses("advancedfilter", $suffixMatchKey, $query);

        // todo: add filter
        AdvancedFilterRecentSearch::updateOrCreate([
                "user_id"=>Auth::user()->id,
                "country"=>$request->query('country'),
                "region"=>$request->query('region'),
                "size_start_range"=>array_key_exists("size_start_range", $sizeRange) ? $sizeRange["size_start_range"] : null,
                "size_end_range"=>array_key_exists("size_end_range", $sizeRange) ? $sizeRange["size_end_range"] : null,
                "age_start_range"=>array_key_exists("age_start_range", $sizeRange) ? $ageRange["age_start_range"] : null,
                "age_end_range"=>array_key_exists("age_end_range", $sizeRange) ? $ageRange["age_end_range"] : null,
            ],
            ['updated_at' => now()]
        );
        

        return $this->sendResponse($businesses, "Retrieved advance filtered businesses successfully");
    }

    public function recent(Request $request){
        $recentSearches = AdvancedFilterRecentSearch::where('user_id', Auth::user()->id)->orderBy('updated_at', 'desc')->get();

        return $this->sendResponse(AdvancedFilterRecentSearchResource::collection($recentSearches), "Retrieved recent searches successfully");
    }
}
