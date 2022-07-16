<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Business;
use App\Http\Resources\Marketplace\Business as MarketplaceBusinessResource;

class BaseController extends Controller
{
    protected $queryPattern = "/^(\([a-z:_%\"\',\s]*\))?(\{[a-z,\*_]*\}$)?/i";
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    //
    // this has search built into it
    private function matchVarToStr($my_var)
    {
        $my_var = str_replace("_id", '', $my_var);
        $my_var = str_replace("_", ' ', $my_var);
        $my_var = preg_replace('/(.+)([A-Z]{1})(.+)/', '$1 $2$3', $my_var);
        $my_var = ucfirst($my_var);

        return $my_var;
    }
    private function constructMatchStr($from, $to, $match_value, $mode = 'to')
    {
        $match_str = $this->matchVarToStr($from);
        $match_str .= " $mode ";
        $match_str .= $this->matchVarToStr($to);
        $match_str .= " ($match_value)";
        return $match_str;
    }

    public function setForgetMatchesFlag($userId = null){
        if($userId == null){
            $userId = Auth::user()->id;
        }
        Cache::put("$userId forget_matches", '1');
    }
    
    public function removeForgetMatchesFlag(){
        $userId = Auth::user()->id;
        Cache::forget("$userId forget_matches", '1');
    }

    public function shouldForgetMatches(){ 
        $userId = Auth::user()->id;
        if(Cache::has("$userId forget_matches")){
            if(Cache::get("$userId forget_matches") == '1'){
                return true;
            }
        }
        return false;
    }
    
    public function matchingBusinesses($fromController, $suffixMatchKey, $query, $limit=50)
    {
        $cur_business = Auth::user()->business;
        $cacheKey = "$cur_business->slug $fromController $suffixMatchKey";

        if($this->shouldForgetMatches()){
            Cache::forget($cacheKey);
            $this->removeForgetMatchesFlag();
        }

        $expiresInSeconds = 60;
        $businesses = Cache::remember($cacheKey, $expiresInSeconds, function () use ($cur_business, $query, $limit) {
            $businesses_cursor = $query->inRandomOrder()->limit($limit)->cursor();
            $businesses = collect();


            $match_config = [
                ["from" => "headquarters_id", "to" => "headquarters_id", "mode" => "id_id", "weight" => 0.2],
                ["from" => "headquarters_id", "to" => "operating_countries", "mode" => "id_arr", "weight" => 0.2],
                ["from" => "headquarters_id", "to" => "expand_countries", "mode" => "id_arr", "weight" => 0.5],
                // ["from" => "operating_countries", "to" => "headquarters_id", "mode" => "arr_id", "weight" => 1],
                ["from" => "operating_countries", "to" => "operating_countries", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "operating_countries", "to" => "expand_countries", "mode" => "arr_arr_ratio", "weight" => 1],
                // ["from" => "expand_countries", "to" => "headquarters_id", "mode" => "arr_id", "weight" => 1],
                ["from" => "expand_countries", "to" => "operating_countries", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "expand_countries", "to" => "expand_countries", "mode" => "arr_arr", "weight" => 0.2],
                ["from" => "main_sector_id", "to" => "main_sector_id", "mode" => "id_id", "weight" => 1],
                ["from" => "mainServices", "to" => "mainServices", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "mainServices", "to" => "service_interests", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "service_interests", "to" => "mainServices", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "service_interests", "to" => "service_interests", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "mainProducts", "to" => "mainProducts", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "commercialInterests", "to" => "commercialInterests", "mode" => "arr_arr_diff_ratio", "weight" => 1],
                ["from" => "commercialInterests", "to" => "commercialInterests", "mode" => "arr_arr_ratio", "weight" => 0.2],
                ["from" => "technologyInterests", "to" => "technologyInterests", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "distributionInterests", "to" => "distributionInterests", "mode" => "arr_arr_ratio", "weight" => 0.2],
                ["from" => "distributionInterests", "to" => "distributionInterests", "mode" => "arr_arr_diff_ratio", "weight" => 1],
                ["from" => "consultingInterests", "to" => "consultingInterests", "mode" => "arr_arr_ratio", "weight" => 0.2],
                ["from" => "consultingInterests", "to" => "consultingInterests", "mode" => "arr_arr_diff_ratio", "weight" => 1],
                ["from" => "investingInterests", "to" => "investingInterests", "mode" => "arr_arr_ratio", "weight" => 0.2],
                ["from" => "investingInterests", "to" => "investingInterests", "mode" => "arr_arr_diff_ratio", "weight" => 1],
                ["from" => "impExpInterests", "to" => "impExpInterests", "mode" => "arr_arr_ratio", "weight" => 1],
                ["from" => "impExpInterests", "to" => "impExpInterests", "mode" => "arr_arr_diff_ratio", "weight" => 0.2],
                ["from" => "valueChainsDealingWith", "to" => "valueChainsDealingWith", "mode" => "arr_arr_ratio", "weight" => 1],
            ];


            // todo: implement matching algorithm with cursor
            foreach ($businesses_cursor as $business) {
                $ttl_match_value = 0;
                $ttl_expeted = 0;
                $matches = [];

                foreach ($match_config as $operation) {
                    $ttl_expeted += $operation["weight"];
                    $from_variable = $cur_business[$operation["from"]];
                    $to_variable = $business[$operation["to"]];
                    if ($from_variable == null || $to_variable == null) continue;

                    if ($operation["mode"] == "id_id") {
                        if ($from_variable == $to_variable) {
                            $match_value = $operation["weight"];
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value));
                        }
                    } else if ($operation["mode"] == "id_arr") {
                        if ($to_variable->contains($from_variable)) { //
                            $match_value = $operation["weight"];
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value));
                        }
                    } else if ($operation["mode"] == "arr_id") {
                        if ($from_variable->contains($to_variable)) { //
                            $match_value = $operation["weight"];
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value));
                        }
                    } else if ($operation["mode"] == "arr_arr") {
                        $ttl_count = $from_variable->count();
                        $diff_count = $from_variable->diff($to_variable)->count();
                        $similar_count = $ttl_count - $diff_count;

                        if ($similar_count > 0) {
                            $match_value = $operation["weight"];
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value));
                        }
                    } else if ($operation["mode"] == "arr_arr_ratio") {
                        $ttl_count = $from_variable->count();
                        $diff_count = $from_variable->diff($to_variable)->count();
                        $similar_count = $ttl_count - $diff_count;

                        if ($similar_count > 0) {
                            $match_value = $operation["weight"] * ($similar_count / $ttl_count);
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value));
                        }
                    } else if ($operation["mode"] == "arr_arr_diff") {
                        $ttl_count = $from_variable->count();
                        $diff_count = $from_variable->diff($to_variable)->count();
                        $similar_count = $ttl_count - $diff_count;

                        if ($diff_count > 0) {
                            $match_value = $operation["weight"];
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value, "Looking For"));
                        }
                    } else if ($operation["mode"] == "arr_arr_diff_ratio") {
                        $ttl_count = $from_variable->count();
                        $diff_count = $from_variable->diff($to_variable)->count();
                        $similar_count = $ttl_count - $diff_count;

                        if ($diff_count > 0) {
                            $match_value = $operation["weight"] * ($diff_count / $ttl_count);
                            $ttl_match_value += $match_value;
                            array_push($matches, $this->constructMatchStr($operation["from"], $operation["to"], $match_value, "Looking For"));
                        }
                    }
                }


                $raw_bs_value = new MarketplaceBusinessResource($business);
                $bs_value = json_decode($raw_bs_value->toJson(), true);
                $bs_value["ttl_match_value"] = $ttl_match_value;
                $bs_value["ttl_match_percentage"] = round(($ttl_match_value/$ttl_expeted) * 100);
                $bs_value["matches"] = $matches;
                $businesses->push($bs_value);
            }

            return $businesses->sortBy("ttl_match_percentage", SORT_REGULAR, true)->values()->all();
        });

        return $businesses;
    }
}
