<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Selectables\SelectableCountry;
use App\Models\Selectables\SelectableBusinessRole;
use App\Models\Selectables\SelectableBusinessSector;
use App\Models\Selectables\SelectableBusinessService;
use App\Models\Selectables\SelectableBusinessProduct;
use App\Models\Selectables\SelectableBusinessPlatformNeed;
use App\Models\Selectables\SelectableBusinessPartnershipInterest;
use App\Models\Selectables\SelectableBusinessCommercialInterest;
use App\Models\Selectables\SelectableBusinessDistributionInterest;
use App\Models\Selectables\SelectableImpExpInterest;
use App\Models\Selectables\SelectableBusinessConsultingInterest;
use App\Models\Selectables\SelectableBusinessInvestingInterest;
use App\Models\Selectables\SelectableTechnology;
use App\Models\Selectables\SelectableValueChain;
use App\Models\Selectables\SelectableInvestorType;
use App\Models\Selectables\SelectableMakeInvestmentInterest;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\Registration\Business as BusinessResource;

class Registration extends BaseController
{
    public function oldBusinessPartOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_type' => 'required|in:African,Non-African,Investor',
            'name' => 'required|unique:businesses',
            'email' => 'required|email|unique:businesses',
            'phone' => 'required|unique:businesses',
            'representative_full_name' => 'required',
            'representative_role' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
            'headquarters' => 'required|exists:selectable_countries,name',
            'operating_countries' => 'required',
            'expand_countries' => 'required',
            'main_sector' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }


        # **business**
        # business slug

        DB::beginTransaction();
        try {
            $bs_input = $request->only(["business_slug", "business_type", "name", "email", "phone"]);
            $bs_input["slug"] = Str::slug($bs_input['name']);

            # main_sector
            $mainSector = SelectableBusinessSector::where("name", $request->main_sector)->first();
            if ($mainSector == null) {
                $mainSector = new SelectableBusinessSector();
                $mainSector->name = $request->main_sector;
                $mainSector->save();
            }
            $bs_input['main_sector_id'] = $mainSector->id;

            if (collect($bs_input)->contains("business_slug") && $business = Business::where("slug", $bs_input['business_slug'])->get()) { //
                $business->update($bs_input);
            } else {
                $business = Business::create($bs_input);
            }

            # **user**
            # validating user full name
            $user_input = [
                "full_name" => $request->representative_full_name,
                "password" => bcrypt($request->password)
            ];
            $user = $business->user;
            if ($user != null) {
                $fullNameTaken = User::where("full_name", $request->representative_full_name)->where("id", "!=", $user->id)->exists();
            } else {
                $fullNameTaken = User::where("full_name", $request->representative_full_name)->exists();
            }

            if ($fullNameTaken) {
                $fullNameTakenError = [
                    "representative_full_name" => ["This name has already been taken"]
                ];
                return $this->sendError('Error validation', $fullNameTakenError, 400);
            }

            # business_role
            $businessRole = SelectableBusinessRole::firstOrCreate(["name" => $request->representative_role]);
            $user_input['business_role_id'] = $businessRole->id;
            $user_input['is_business_rep'] = true;
            // throw new \Exception(json_encode($user_input));
            # creating user
            $user = $business->user;
            if ($user != null) {
                $user->update($user_input);
            } else {
                $user = User::create($user_input);
                $business->user_id = $user->id;
            }

            # countries
            ## headquarters
            $headquarters = SelectableCountry::firstWhere("name", $request->headquarters);
            $business->headquarters_id = $headquarters->id;

            ## operating countries
            $raw_operating_countries = $request->operating_countries;
            $operating_countries = [];
            foreach ($raw_operating_countries as $raw_operating_country) {
                $operating_country = SelectableCountry::firstWhere("name", $raw_operating_country);
                if ($operating_country == null) {
                    throw new \Exception("operating_countries have an invalid country $raw_operating_country");
                }
                array_push($operating_countries, $operating_country->id);
            }
            $business->operating_countries()->sync($operating_countries);
            ## expand countries
            $raw_expand_countries = $request->expand_countries;
            $expand_countries = [];
            foreach ($raw_expand_countries as $raw_expand_country) {
                $expand_country = SelectableCountry::firstWhere("name", $raw_expand_country);
                if ($expand_country == null) {
                    throw new \Exception("expand_countries have an invalid country $raw_expand_country");
                }
                array_push($expand_countries, $expand_country->id);
            }
            $business->expand_countries()->sync($expand_countries);
            $business->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        $business = Business::where("name", $request->name)->first();
        return $this->sendResponse(new BusinessResource($business), "Part one complete");
    }

    /**
     * Used to set business type and company info
     */
    private function PartOne(Request $request)
    {
        DB::beginTransaction();
        try {
            $bs_input = $request->only(["business_slug", "business_type", "name", "email", "phone"]);
            # slug
            $bs_input["slug"] = Str::slug($bs_input['name']);

            # main_sector
            if ($request->has("main_sector")) {
                $mainSector = SelectableBusinessSector::where("name", $request->main_sector)->first();
                if ($mainSector == null) {
                    $mainSector = new SelectableBusinessSector();
                    $mainSector->name = $request->main_sector;
                    $mainSector->save();
                }
                $bs_input['main_sector_id'] = $mainSector->id;
            }

            # investor_type
            if ($request->has('investor_type')) {
                $investorType = SelectableInvestorType::firstOrCreate(["name" => $request->investor_type]);
                $bs_input['investor_type_id'] = $investorType->id;
            }

            # save business
            if (collect($bs_input)->contains("business_slug") && $business = Business::where("slug", $bs_input['business_slug'])->get()) { //
                $business->update($bs_input);
            } else {
                $business = Business::create($bs_input);
            }

            # countries
            ## headquarters
            $headquarters = SelectableCountry::firstWhere("name", $request->headquarters);
            $business->headquarters_id = $headquarters->id;

            ## operating countries
            if ($request->has("operating_countries")) {
                if (!is_array($request->operating_countries)) {
                    $raw_operating_countries = json_decode($request->operating_countries);
                    if ($raw_operating_countries == null) {
                        throw new \Exception("Invalid Json Field operating_countries");
                    }
                } else {
                    $raw_operating_countries = $request->operating_countries;
                }
                $operating_countries = [];
                foreach ($raw_operating_countries as $raw_operating_country) {
                    $operating_country = SelectableCountry::firstWhere("name", $raw_operating_country);
                    if ($operating_country == null) {
                        throw new \Exception("operating_countries have an invalid country $raw_operating_country");
                    }
                    array_push($operating_countries, $operating_country->id);
                }
                $business->operating_countries()->sync($operating_countries);
            }
            ## expand countries
            if ($request->has("expand_countries")) {
                if (!is_array($request->expand_countries)) {
                    $raw_expand_countries = json_decode($request->expand_countries);
                    if ($raw_expand_countries == null) {
                        throw new \Exception("Invalid Json Field expand_countries");
                    }
                } else {
                    $raw_expand_countries = $request->expand_countries;
                }
                // $raw_expand_countries = $request->expand_countries;
                $expand_countries = [];
                foreach ($raw_expand_countries as $raw_expand_country) {
                    $expand_country = SelectableCountry::firstWhere("name", $raw_expand_country);
                    if ($expand_country == null) {
                        throw new \Exception("expand_countries have an invalid country $raw_expand_country");
                    }
                    array_push($expand_countries, $expand_country->id);
                }
                $business->expand_countries()->sync($expand_countries);
            }


            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        $business = Business::where("name", $request->name)->first();
        return $this->sendResponse(new BusinessResource($business), "Part one complete");
    }

    public function businessPartOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_type' => 'required|in:African,Non-African,Investor',
            'name' => 'required|unique:businesses',
            'email' => 'required|email|unique:businesses',
            'phone' => 'required|unique:businesses',
            'headquarters' => 'required|exists:selectable_countries,name',
            'operating_countries' => 'required',
            'main_sector' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        return $this->PartOne($request);
    }

    public function investorPartOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_type' => 'required|in:African,Non-African,Investor',
            'name' => 'required|unique:businesses',
            'email' => 'required|email|unique:businesses',
            'phone' => 'required|unique:businesses',
            'headquarters' => 'required|exists:selectable_countries,name',
            'operating_countries' => 'required',
            'investor_type' => 'required|exists:selectable_investor_types,name',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        return $this->PartOne($request);
    }

    /**
     * Used to set representative info
     */
    public function BusinessPartTwo(Request $request)
    {
        $messages = ['password.regex' => 'The :attribute contain at least 6 characters, a letter, a symbol and a number.',];
        $passwordRegex = "/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#@%]).*$/";

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
            'representative_full_name' => 'required',
            'representative_role' => 'required',
            'password' => "required|regex:$passwordRegex",
            'confirm_password' => 'required|same:password',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            $business = Business::where("slug", $request->slug)->first();
            # **user**
            # validating user full name
            $user_input = [
                "full_name" => $request->representative_full_name,
                "password" => bcrypt($request->password)
            ];
            $user = $business->user;
            if ($user != null) {
                $fullNameTaken = User::where("full_name", $request->representative_full_name)->where("id", "!=", $user->id)->exists();
            } else {
                $fullNameTaken = User::where("full_name", $request->representative_full_name)->exists();
            }

            if ($fullNameTaken) {
                $fullNameTakenError = [
                    "representative_full_name" => ["This name has already been taken"]
                ];
                return $this->sendError('Error validation', $fullNameTakenError, 400);
            }

            # business_role
            $businessRole = SelectableBusinessRole::firstOrCreate(["name" => $request->representative_role]);
            $user_input['business_role_id'] = $businessRole->id;
            $user_input['is_business_rep'] = true;
            // throw new \Exception(json_encode($user_input));
            # creating user
            $user = $business->user;
            if ($user != null) {
                $user->update($user_input);
            } else {
                $user = User::create($user_input);
                $business->user_id = $user->id;
                $business->save();
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part two complete");
    }

    /**
     * Used to set business interests
     */
    public function BusinessPartThree(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            // 'partnership_interests' => 'required',
            // 'commercial_interests' => 'required',
            // 'distribution_interests' => 'required',
            // 'imp_exp_interests' => 'required',
            // 'consulting_interests' => 'required',
            // 'investing_interests' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # partnership_interest
            if ($request->has("partnership_interests")) {
                if (!is_array($request->partnership_interests)) {
                    $raw_partnership_interests = json_decode($request->partnership_interests);
                    if ($raw_partnership_interests == null) {
                        throw new \Exception("Invalid Json Field partnership_interests");
                    }
                } else {
                    $raw_partnership_interests = $request->partnership_interests;
                }
                $partnership_interests = [];
                foreach ($raw_partnership_interests as $raw_partnership_interest) {
                    $partnership_interest = SelectableBusinessPartnershipInterest::firstOrCreate(['name' => $raw_partnership_interest]);
                    array_push($partnership_interests, $partnership_interest->id);
                }
                $business->partnershipInterests()->sync($partnership_interests);
                $business->save();
            }

            # investing_interests
            if ($request->has("investing_interests")) {
                if (!is_array($request->investing_interests)) {
                    $raw_investing_interests = json_decode($request->investing_interests);
                    if ($raw_investing_interests == null) {
                        throw new \Exception("Invalid Json Field investing_interests");
                    }
                } else {
                    $raw_investing_interests = $request->investing_interests;
                }
                $investing_interests = [];
                foreach ($raw_investing_interests as $raw_investing_interest) {
                    $investing_interest = SelectableBusinessInvestingInterest::firstOrCreate(['name' => $raw_investing_interest]);
                    array_push($investing_interests, $investing_interest->id);
                }
                $business->investingInterests()->sync($investing_interests);
                $business->save();
            }

            # commercial_interests
            if ($request->has("commercial_interests")) {
                if (!is_array($request->commercial_interests)) {
                    $raw_commercial_interests = json_decode($request->commercial_interests);
                    if ($raw_commercial_interests == null) {
                        throw new \Exception("Invalid Json Field commercial_interests");
                    }
                } else {
                    $raw_commercial_interests = $request->commercial_interests;
                }
                $commercial_interests = [];
                foreach ($raw_commercial_interests as $raw_commercial_interest) {
                    $commercial_interest = SelectableBusinessCommercialInterest::firstOrCreate(['name' => $raw_commercial_interest]);
                    array_push($commercial_interests, $commercial_interest->id);
                }
                $business->commercialInterests()->sync($commercial_interests);
                $business->save();
            }

            # distribution_interests
            if ($request->has("distribution_interests")) {
                if (!is_array($request->distribution_interests)) {
                    $raw_distribution_interests = json_decode($request->distribution_interests);
                    if ($raw_distribution_interests == null) {
                        throw new \Exception("Invalid Json Field distribution_interests");
                    }
                } else {
                    $raw_distribution_interests = $request->distribution_interests;
                }
                $distribution_interests = [];
                foreach ($raw_distribution_interests as $raw_distribution_interest) {
                    $distribution_interest = SelectableBusinessDistributionInterest::firstOrCreate(['name' => $raw_distribution_interest]);
                    array_push($distribution_interests, $distribution_interest->id);
                }
                $business->distributionInterests()->sync($distribution_interests);
                $business->save();
            }
            # imp_exp_interests
            if ($request->has("imp_exp_interests")) {
                if (!is_array($request->imp_exp_interests)) {
                    $raw_imp_exp_interests = json_decode($request->imp_exp_interests);
                    if ($raw_imp_exp_interests == null) {
                        throw new \Exception("Invalid Json Field imp_exp_interests");
                    }
                } else {
                    $raw_imp_exp_interests = $request->imp_exp_interests;
                }
                $imp_exp_interests = [];
                foreach ($raw_imp_exp_interests as $raw_imp_exp_interest) {
                    $imp_exp_interest = SelectableImpExpInterest::firstOrCreate(['name' => $raw_imp_exp_interest]);
                    array_push($imp_exp_interests, $imp_exp_interest->id);
                }
                $business->impExpInterests()->sync($imp_exp_interests);
                $business->save();
            }
            # consulting_interests
            if ($request->has("consulting_interests")) {
                if (!is_array($request->consulting_interests)) {
                    $raw_consulting_interests = json_decode($request->consulting_interests);
                    if ($raw_consulting_interests == null) {
                        throw new \Exception("Invalid Json Field consulting_interests");
                    }
                } else {
                    $raw_consulting_interests = $request->consulting_interests;
                }
                $consulting_interests = [];
                foreach ($raw_consulting_interests as $raw_consulting_interest) {
                    $consulting_interest = SelectableBusinessConsultingInterest::firstOrCreate(['name' => $raw_consulting_interest]);
                    array_push($consulting_interests, $consulting_interest->id);
                }
                $business->consultingInterests()->sync($consulting_interests);
                $business->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part three complete");
    }

    /**
     * Used to set business investor profiling
     */
    public function businessPartFour(Request $request)
    {
        $rangeRegex = "/^\s*([0-9]+)\s*.{1}\s*([0-9]+)\s*$/";
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            'main_services' => 'required_without:main_products',
            'main_products' => 'required_without:main_services',
            'platform_needs' => 'required',
            'size' => "regex:$rangeRegex",
            'age' => "regex:$rangeRegex"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # main_services
            if ($request->has("main_services")) {
                if (!is_array($request->main_services)) {
                    $raw_main_services = json_decode($request->main_services);
                    if ($raw_main_services == null) {
                        throw new \Exception("Invalid Json Field main_services");
                    }
                } else {
                    $raw_main_services = $request->main_services;
                }
                $main_services = [];
                foreach ($raw_main_services as $raw_main_service) {
                    $main_service = SelectableBusinessService::firstOrCreate(['name' => $raw_main_service]);
                    array_push($main_services, $main_service->id);
                }
                $business->mainServices()->sync($main_services);
                $business->save();
            }


            # main_products
            if ($request->has("main_products")) {
                if (!is_array($request->main_products)) {
                    $raw_main_products = json_decode($request->main_products);
                    if ($raw_main_products == null) {
                        throw new \Exception("Invalid Json Field main_products");
                    }
                } else {
                    $raw_main_products = $request->main_products;
                }
                $main_products = [];
                foreach ($raw_main_products as $raw_main_product) {
                    $main_product = SelectableBusinessProduct::firstOrCreate(['name' => $raw_main_product]);
                    array_push($main_products, $main_product->id);
                }
                $business->mainProducts()->sync($main_products);
                $business->save();
            }
            # platform_needs
            if ($request->has("platform_needs")) {
                if (!is_array($request->platform_needs)) {
                    $raw_platform_needs = json_decode($request->platform_needs);
                    if ($raw_platform_needs == null) {
                        throw new \Exception("Invalid Json Field platform_needs");
                    }
                } else {
                    $raw_platform_needs = $request->platform_needs;
                }

                $platform_needs = [];
                foreach ($raw_platform_needs as $raw_platform_need) {
                    $platform_need = SelectableBusinessPlatformNeed::firstOrCreate(['name' => $raw_platform_need]);
                    array_push($platform_needs, $platform_need->id);
                }
                $business->platformNeeds()->sync($platform_needs);
                $business->save();
            }

            # business size
            if ($request->has('size')) {
                $sizeRange = [];
                $sizesMatch = [];
                preg_match($rangeRegex, $request->size, $sizesMatch);
                $sizeRange["size_start_range"] = $sizesMatch[1];
                $sizeRange["size_end_range"] = $sizesMatch[2];
                $sizeValidator = Validator::make($sizeRange, [
                    'size_start_range' => "lte:size_end_range",
                ]);
                if ($sizeValidator->fails()) {
                    return $this->sendError('Error validation', $sizeValidator->errors());
                }
                $business->update($sizeRange);
            }

            # business age
            if ($request->has('age')) {
                $ageRange = [];
                $ageMatch = [];
                preg_match($rangeRegex, $request->age, $ageMatch);
                $ageRange["age_start_range"] = $ageMatch[1];
                $ageRange["age_end_range"] = $ageMatch[2];
                $ageValidator = Validator::make($ageRange, [
                    'age_start_range' => "lte:age_end_range",
                ]);
                if ($ageValidator->fails()) {
                    return $this->sendError('Error validation', $ageValidator->errors());
                }
                $business->update($ageRange);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part four complete");
    }

    /**
     * Used to set business keywords and tags
     */
    public function businessPartFive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            // 'country_interests' => 'required',
            // 'product_interests' => 'required',
            // 'service_interests' => 'required',
            // 'technology_interests' => 'required',
            // 'value_chains_dealing_with' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # country_interests
            if ($request->has("country_interests")) {
                if (!is_array($request->country_interests)) {
                    $raw_country_interests = json_decode($request->country_interests);
                    if ($raw_country_interests == null) {
                        throw new \Exception("Invalid Json Field country_interests");
                    }
                } else {
                    $raw_country_interests = $request->country_interests;
                }
                $country_interests = [];
                foreach ($raw_country_interests as $raw_country_interest) {
                    $country_interest = SelectableCountry::firstWhere(['name' => $raw_country_interest]);
                    array_push($country_interests, $country_interest->id);
                }
                $business->countryInterests()->sync($country_interests);
                $business->save();
            }

            # product_interests
            if ($request->has("product_interests")) {
                if (!is_array($request->product_interests)) {
                    $raw_product_interests = json_decode($request->product_interests);
                    if ($raw_product_interests == null) {
                        throw new \Exception("Invalid Json Field product_interests");
                    }
                } else {
                    $raw_product_interests = $request->product_interests;
                }
                $product_interests = [];
                foreach ($raw_product_interests as $raw_product_interest) {
                    $product_interest = SelectableBusinessProduct::firstOrCreate(['name' => $raw_product_interest]);
                    array_push($product_interests, $product_interest->id);
                }
                $business->productInterests()->sync($product_interests);
                $business->save();
            }

            # service_interest
            if ($request->has("service_interest")) {
                if (!is_array($request->service_interests)) {
                    $raw_service_interests = json_decode($request->service_interests);
                    if ($raw_service_interests == null) {
                        throw new \Exception("Invalid Json Field service_interests");
                    }
                } else {
                    $raw_service_interests = $request->service_interests;
                }
                $service_interests = [];
                foreach ($raw_service_interests as $raw_service_interest) {
                    $service_interest = SelectableBusinessService::firstOrCreate(['name' => $raw_service_interest]);
                    array_push($service_interests, $service_interest->id);
                }
                $business->serviceInterests()->sync($service_interests);
                $business->save();
            }

            # technology_interests
            if ($request->has("technology_interests")) {
                if (!is_array($request->technology_interests)) {
                    $raw_technology_interests = json_decode($request->technology_interests);
                    if ($raw_technology_interests == null) {
                        throw new \Exception("Invalid Json Field technology_interests");
                    }
                } else {
                    $raw_technology_interests = $request->technology_interests;
                }
                $technology_interests = [];
                foreach ($raw_technology_interests as $raw_technology_interest) {
                    $technology_interest = SelectableTechnology::firstOrCreate(['name' => $raw_technology_interest]);
                    array_push($technology_interests, $technology_interest->id);
                }
                $business->technologyInterests()->sync($technology_interests);
                $business->save();
            }

            # value_chains_dealing_with
            if ($request->has("value_chains_dealing_with")) {
                if (!is_array($request->value_chains_dealing_with)) {
                    $raw_value_chains = json_decode($request->value_chains_dealing_with);
                    if ($raw_value_chains == null) {
                        throw new \Exception("Invalid Json Field value_chains");
                    }
                } else {
                    $raw_value_chains = $request->value_chains_dealing_with;
                }
                $value_chains = [];
                foreach ($raw_value_chains as $raw_value_chain) {
                    $value_chain = SelectableValueChain::firstOrCreate(['name' => $raw_value_chain]);
                    array_push($value_chains, $value_chain->id);
                }
                $business->valueChainsDealingWith()->sync($value_chains);
                $business->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part five complete");
    }

    /**
     * Used to set executive summary
     */
    public function businessPartSix(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            'executive_summary' => 'required_without:executive_summary_file|string|min:70|max:150',
            'executive_summary_file' => 'required_without:executive_summary|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # executive_summary
            if ($request->has('executive_summary')) {
                $business->executive_summary = $request->executive_summary;
                $business->save();
            }

            # executive_summary_file
            if ($request->has('executive_summary_file')) {
                # handle the executive_summary_file
                if ($business->executive_summary_file != null) {
                    Storage::delete($business->executive_summary_file);
                }

                $path = $request->file('executive_summary_file')->store('executive_summary_files');
                $business->executive_summary_file = $path;
                $business->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part six complete");
    }

    /**
     * Used to set company identity
     */
    public function businessPartSeven(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            'logo' => 'mimes:jpg,jpeg,png|max:10240',
            'banner' => 'mimes:jpg,jpeg,png|max:10240',
            'incorporation_number' => 'required|unique:businesses',
            'certificate_of_incorporation' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # incorporation_number
            $bs_input = $request->only(['incorporation_number']);
            $business->update($bs_input);

            # attached files
            ## handle the logo
            if ($request->has("logo")) {
                if ($business->logo != null) {
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
                ## handle the logo
                if ($business->logo != null) {
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
            }
            ## handle the banner
            if ($request->has("banner")) {
                if ($business->banner != null) {
                    Storage::delete($business->banner);
                }
                $path = $request->file('banner')->store('banners');
                $business->banner = $path;
                $business->save();
            }
            ## handle the certificate_of_incorporation
            if ($request->has("certificate_of_incorporation")) {
                if ($business->certificate_of_incorporation != null) {
                    Storage::delete($business->certificate_of_incorporation);
                }
                $path = $request->file('certificate_of_incorporation')->store('certificates_of_incorporation');
                $business->certificate_of_incorporation = $path;
                $business->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part seven complete");
    }


    public function oldBusinessPartTwo(Request $request)
    {
        $rangeRegex = "/^\s*([0-9]+)\s*.{1}\s*([0-9]+)\s*$/";
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            'logo' => 'mimes:jpg,jpeg,png|max:10240',
            'banner' => 'mimes:jpg,jpeg,png|max:10240',
            // 'certificate_of_incorporation' => 'required',
            'incorporation_number' => 'required|unique:businesses',
            'main_services' => 'required',
            'main_products' => 'required',
            'platform_needs' => 'required',
            // 'partnership_interests' => 'required',
            // 'commercial_interests' => 'required',
            // 'distribution_interests' => 'required',
            // 'imp_exp_interests' => 'required',
            // 'consulting_interests' => 'required',
            // 'investing_interests' => 'required',
            // 'service_interests' => 'required',
            // 'technology_interests' => 'required',
            // 'value_chains_dealing_with' => 'required',
            'executive_summary' => 'required_without:executive_summary_file|string|min:70',
            'executive_summary_file' => 'required_without:executive_summary|mimes:pdf',
            'size' => "regex:$rangeRegex",
            'age' => "regex:$rangeRegex"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # incorporation_number
            $bs_input = $request->only(['incorporation_number']);
            $business->update($bs_input);

            # attached files
            ## handle the logo
            if ($request->has("logo")) {
                if ($business->logo != null) {
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
                ## handle the logo
                if ($business->logo != null) {
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
            }
            ## handle the banner
            if ($request->has("banner")) {
                if ($business->banner != null) {
                    Storage::delete($business->banner);
                }
                $path = $request->file('banner')->store('banners');
                $business->banner = $path;
                $business->save();
            }
            ## handle the certificate_of_incorporation
            if ($request->has("certificate_of_incorporation")) {
                if ($business->certificate_of_incorporation != null) {
                    Storage::delete($business->certificate_of_incorporation);
                }
                $path = $request->file('certificate_of_incorporation')->store('certificates_of_incorporation');
                $business->certificate_of_incorporation = $path;
                $business->save();
            }

            # business size
            if ($request->has('size')) {
                $sizeRange = [];
                $sizesMatch = [];
                preg_match($rangeRegex, $request->size, $sizesMatch);
                $sizeRange["size_start_range"] = $sizesMatch[1];
                $sizeRange["size_end_range"] = $sizesMatch[2];
                $sizeValidator = Validator::make($sizeRange, [
                    'size_start_range' => "lte:size_end_range",
                ]);
                if ($sizeValidator->fails()) {
                    return $this->sendError('Error validation', $sizeValidator->errors());
                }
                $business->update($sizeRange);
            }

            # business age
            if ($request->has('age')) {
                $ageRange = [];
                $ageMatch = [];
                preg_match($rangeRegex, $request->age, $ageMatch);
                $ageRange["age_start_range"] = $ageMatch[1];
                $ageRange["age_end_range"] = $ageMatch[2];
                $ageValidator = Validator::make($ageRange, [
                    'age_start_range' => "lte:age_end_range",
                ]);
                if ($ageValidator->fails()) {
                    return $this->sendError('Error validation', $ageValidator->errors());
                }
                $business->update($ageRange);
            }

            # main_services
            if (!is_array($request->main_services)) {
                $raw_main_services = json_decode($request->main_services);
                if ($raw_main_services == null) {
                    throw new \Exception("Invalid Json Field main_services");
                }
            } else {
                $raw_main_services = $request->main_services;
            }
            $main_services = [];
            foreach ($raw_main_services as $raw_main_service) {
                $main_service = SelectableBusinessService::firstOrCreate(['name' => $raw_main_service]);
                array_push($main_services, $main_service->id);
            }
            $business->mainServices()->sync($main_services);
            $business->save();

            # main_products
            if (!is_array($request->main_products)) {
                $raw_main_products = json_decode($request->main_products);
                if ($raw_main_products == null) {
                    throw new \Exception("Invalid Json Field main_products");
                }
            } else {
                $raw_main_products = $request->main_products;
            }
            $main_products = [];
            foreach ($raw_main_products as $raw_main_product) {
                $main_product = SelectableBusinessProduct::firstOrCreate(['name' => $raw_main_product]);
                array_push($main_products, $main_product->id);
            }
            $business->mainProducts()->sync($main_products);
            $business->save();
            # platform_needs
            if (!is_array($request->platform_needs)) {
                $raw_platform_needs = json_decode($request->platform_needs);
                if ($raw_platform_needs == null) {
                    throw new \Exception("Invalid Json Field platform_needs");
                }
            } else {
                $raw_platform_needs = $request->platform_needs;
            }

            $platform_needs = [];
            foreach ($raw_platform_needs as $raw_platform_need) {
                $platform_need = SelectableBusinessPlatformNeed::firstOrCreate(['name' => $raw_platform_need]);
                array_push($platform_needs, $platform_need->id);
            }
            $business->platformNeeds()->sync($platform_needs);
            $business->save();
            # partnership_interest
            if ($request->has("partnership_interests")) {
                if (!is_array($request->partnership_interests)) {
                    $raw_partnership_interests = json_decode($request->partnership_interests);
                    if ($raw_partnership_interests == null) {
                        throw new \Exception("Invalid Json Field partnership_interests");
                    }
                } else {
                    $raw_partnership_interests = $request->partnership_interests;
                }
                $partnership_interests = [];
                foreach ($raw_partnership_interests as $raw_partnership_interest) {
                    $partnership_interest = SelectableBusinessPartnershipInterest::firstOrCreate(['name' => $raw_partnership_interest]);
                    array_push($partnership_interests, $partnership_interest->id);
                }
                $business->partnershipInterests()->sync($partnership_interests);
                $business->save();
            }
            # commercial_interest
            if ($request->has("commercial_interests")) {
                if (!is_array($request->commercial_interests)) {
                    $raw_commercial_interests = json_decode($request->commercial_interests);
                    if ($raw_commercial_interests == null) {
                        throw new \Exception("Invalid Json Field commercial_interests");
                    }
                } else {
                    $raw_commercial_interests = $request->commercial_interests;
                }
                $commercial_interests = [];
                foreach ($raw_commercial_interests as $raw_commercial_interest) {
                    $commercial_interest = SelectableBusinessCommercialInterest::firstOrCreate(['name' => $raw_commercial_interest]);
                    array_push($commercial_interests, $commercial_interest->id);
                }
                $business->commercialInterests()->sync($commercial_interests);
                $business->save();
            }
            # distribution_interest
            if ($request->has("distribution_interest")) {
                if (!is_array($request->distribution_interests)) {
                    $raw_distribution_interests = json_decode($request->distribution_interests);
                    if ($raw_distribution_interests == null) {
                        throw new \Exception("Invalid Json Field distribution_interests");
                    }
                } else {
                    $raw_distribution_interests = $request->distribution_interests;
                }
                $distribution_interests = [];
                foreach ($raw_distribution_interests as $raw_distribution_interest) {
                    $distribution_interest = SelectableBusinessDistributionInterest::firstOrCreate(['name' => $raw_distribution_interest]);
                    array_push($distribution_interests, $distribution_interest->id);
                }
                $business->distributionInterests()->sync($distribution_interests);
                $business->save();
            }
            # imp_exp_interest
            if ($request->has("imp_exp_interest")) {
                if (!is_array($request->imp_exp_interests)) {
                    $raw_imp_exp_interests = json_decode($request->imp_exp_interests);
                    if ($raw_imp_exp_interests == null) {
                        throw new \Exception("Invalid Json Field imp_exp_interests");
                    }
                } else {
                    $raw_imp_exp_interests = $request->imp_exp_interests;
                }
                $imp_exp_interests = [];
                foreach ($raw_imp_exp_interests as $raw_imp_exp_interest) {
                    $imp_exp_interest = SelectableImpExpInterest::firstOrCreate(['name' => $raw_imp_exp_interest]);
                    array_push($imp_exp_interests, $imp_exp_interest->id);
                }
                $business->impExpInterests()->sync($imp_exp_interests);
                $business->save();
            }
            # consulting_interest
            if ($request->has("consulting_interest")) {
                if (!is_array($request->consulting_interests)) {
                    $raw_consulting_interests = json_decode($request->consulting_interests);
                    if ($raw_consulting_interests == null) {
                        throw new \Exception("Invalid Json Field consulting_interests");
                    }
                } else {
                    $raw_consulting_interests = $request->consulting_interests;
                }
                $consulting_interests = [];
                foreach ($raw_consulting_interests as $raw_consulting_interest) {
                    $consulting_interest = SelectableBusinessConsultingInterest::firstOrCreate(['name' => $raw_consulting_interest]);
                    array_push($consulting_interests, $consulting_interest->id);
                }
                $business->consultingInterests()->sync($consulting_interests);
                $business->save();
            }
            # investing_interest
            if ($request->has("investing_interest")) {
                if (!is_array($request->investing_interests)) {
                    $raw_investing_interests = json_decode($request->investing_interests);
                    if ($raw_investing_interests == null) {
                        throw new \Exception("Invalid Json Field investing_interests");
                    }
                } else {
                    $raw_investing_interests = $request->investing_interests;
                }
                $investing_interests = [];
                foreach ($raw_investing_interests as $raw_investing_interest) {
                    $investing_interest = SelectableBusinessInvestingInterest::firstOrCreate(['name' => $raw_investing_interest]);
                    array_push($investing_interests, $investing_interest->id);
                }
                $business->investingInterests()->sync($investing_interests);
                $business->save();
            }
            # service_interest
            if ($request->has("service_interest")) {
                if (!is_array($request->service_interests)) {
                    $raw_service_interests = json_decode($request->service_interests);
                    if ($raw_service_interests == null) {
                        throw new \Exception("Invalid Json Field service_interests");
                    }
                } else {
                    $raw_service_interests = $request->service_interests;
                }
                $service_interests = [];
                foreach ($raw_service_interests as $raw_service_interest) {
                    $service_interest = SelectableBusinessService::firstOrCreate(['name' => $raw_service_interest]);
                    array_push($service_interests, $service_interest->id);
                }
                $business->serviceInterests()->sync($service_interests);
                $business->save();
            }
            # technology_interest
            if ($request->has("technology_interest")) {
                if (!is_array($request->technology_interests)) {
                    $raw_technology_interests = json_decode($request->technology_interests);
                    if ($raw_technology_interests == null) {
                        throw new \Exception("Invalid Json Field technology_interests");
                    }
                } else {
                    $raw_technology_interests = $request->technology_interests;
                }
                $technology_interests = [];
                foreach ($raw_technology_interests as $raw_technology_interest) {
                    $technology_interest = SelectableTechnology::firstOrCreate(['name' => $raw_technology_interest]);
                    array_push($technology_interests, $technology_interest->id);
                }
                $business->technologyInterests()->sync($technology_interests);
                $business->save();
            }
            # value_chains_dealing_with
            if ($request->has("value_chains_dealing_with")) {
                if (!is_array($request->value_chains_dealing_with)) {
                    $raw_value_chains = json_decode($request->value_chains_dealing_with);
                    if ($raw_value_chains == null) {
                        throw new \Exception("Invalid Json Field value_chains");
                    }
                } else {
                    $raw_value_chains = $request->value_chains_dealing_with;
                }
                $value_chains = [];
                foreach ($raw_value_chains as $raw_value_chain) {
                    $value_chain = SelectableValueChain::firstOrCreate(['name' => $raw_value_chain]);
                    array_push($value_chains, $value_chain->id);
                }
                $business->valueChainsDealingWith()->sync($value_chains);
                $business->save();
            }

            # executive_summary
            if ($request->has('executive_summary')) {
                $business->executive_summary = $request->executive_summary;
                $business->save();
            }
            # executive_summary_file
            if ($request->has('executive_summary_file')) {
                # handle the executive_summary_file
                if ($business->executive_summary_file != null) {
                    Storage::delete($business->executive_summary_file);
                }

                $path = $request->file('executive_summary_file')->store('executive_summary_files');
                $business->executive_summary_file = $path;
                $business->save();
            }

            // $businessRole = SelectableBusinessRole::where("name", $request_arr['business_role'])->first();
            // if($businessRole == null){
            //     $businessRole = new SelectableBusinessRole();
            //     $businessRole->name = $request->business_role;
            //     $businessRole->save();
            // }
            // $input['business_role_id'] = $businessRole->id;
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part two complete");
    }

    public function retrieveBusiness(Request $request, $slug)
    { //
        $business = Business::where("slug", $slug)->first();
        return $this->sendResponse(new BusinessResource($business), "Retrieved business");
    }

    public function oldInvestorPartOne(Request $request)
    {
        $messages = ['password.regex' => 'The :attribute contain at least 6 characters, a letter, a symbol and a number.',];
        $passwordRegex = "/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#@%]).*$/";

        $validator = Validator::make($request->all(), [
            'business_type' => 'required|in:African,Non-African,Investor',
            'name' => 'required|unique:businesses',
            'email' => 'required|email|unique:businesses',
            'phone' => 'required|unique:businesses',
            'representative_full_name' => 'required',
            'representative_role' => 'required',
            'password' => "required|regex:$passwordRegex",
            'confirm_password' => 'required',
            'headquarters' => 'required|exists:selectable_countries,name',
            'operating_countries' => 'required',
            'expand_countries' => 'required',
            'investor_type' => 'required|exists:selectable_investor_types,name',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }


        # **business**
        # business slug

        DB::beginTransaction();
        try {
            $bs_input = $request->only(["business_slug", "business_type", "name", "email", "phone"]);
            $bs_input["slug"] = Str::slug($bs_input['name']);

            if (collect($bs_input)->contains("business_slug") && $business = Business::where("slug", $bs_input['business_slug'])->get()) { //
                $business->update($bs_input);
            } else {
                $business = Business::create($bs_input);
            }

            # **user**
            # validating user full name
            $user_input = [
                "full_name" => $request->representative_full_name,
                "password" => bcrypt($request->password)
            ];
            $user = $business->user;
            if ($user != null) {
                $fullNameTaken = User::where("full_name", $request->representative_full_name)->where("id", "!=", $user->id)->exists();
            } else {
                $fullNameTaken = User::where("full_name", $request->representative_full_name)->exists();
            }

            if ($fullNameTaken) {
                $fullNameTakenError = [
                    "representative_full_name" => ["This name has already been taken"]
                ];
                return $this->sendError('Error validation', $fullNameTakenError, 400);
            }

            # business_role
            $businessRole = SelectableBusinessRole::firstOrCreate(["name" => $request->representative_role]);
            $user_input['business_role_id'] = $businessRole->id;
            # investor_type
            $investorType = SelectableInvestorType::firstOrCreate(["name" => $request->investor_type]);
            $user_input['investor_type_id'] = $investorType->id;

            $user_input['is_investor'] = true;
            # creating user
            $user = $business->user;
            if ($user != null) {
                $user->update($user_input);
            } else {
                $user = User::create($user_input);
                $business->user_id = $user->id;
            }

            # countries
            ## headquarters
            $headquarters = SelectableCountry::firstWhere("name", $request->headquarters);
            $business->headquarters_id = $headquarters->id;

            ## operating countries
            $raw_operating_countries = $request->operating_countries;
            $operating_countries = [];
            foreach ($raw_operating_countries as $raw_operating_country) {
                $operating_country = SelectableCountry::firstWhere("name", $raw_operating_country);
                if ($operating_country == null) {
                    throw new \Exception("operating_countries have an invalid country $raw_operating_country");
                }
                array_push($operating_countries, $operating_country->id);
            }
            $business->operating_countries()->sync($operating_countries);
            ## expand countries
            $raw_expand_countries = $request->expand_countries;
            $expand_countries = [];
            foreach ($raw_expand_countries as $raw_expand_country) {
                $expand_country = SelectableCountry::firstWhere("name", $raw_expand_country);
                if ($expand_country == null) {
                    throw new \Exception("expand_countries have an invalid country $raw_expand_country");
                }
                array_push($expand_countries, $expand_country->id);
            }
            $business->expand_countries()->sync($expand_countries);
            $business->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        $business = Business::where("name", $request->name)->first();
        return $this->sendResponse(new BusinessResource($business), "Part one complete");
    }

    public function investorPartTwo(Request $request)
    {
        $rangeRegex = "/^\s*([0-9]+)\s*.{1}\s*([0-9]+)\s*$/";
        $validator = Validator::make($request->all(), [
            'slug' => 'required|exists:businesses,slug',
            'logo' => 'mimes:jpg,jpeg,png',
            'banner' => 'mimes:jpg,jpeg,png',
            // 'certificate_of_incorporation' => 'required',
            'incorporation_number' => 'required|unique:businesses,incorporation_number',
            'other_services' => 'required',                         //new
            // 'sector_interests' => 'required',                       //new
            // 'make_investment_interests' => 'required',              //new
            // 'commercial_interests' => 'required',
            // 'distribution_interests' => 'required',
            // 'country_interests' => 'required',                      //new
            // 'product_interests' => 'required',                      //new
            // 'service_interests' => 'required',
            // 'technology_interests' => 'required',
            // 'value_chains_dealing_with' => 'required',
            'executive_summary' => 'required_without:executive_summary_file|string|min:500',
            'executive_summary_file' => 'required_without:executive_summary|mimes:pdf',
            // 'main_services' => 'required',
            // 'main_products' => 'required',
            // 'platform_needs' => 'required',
            // 'partnership_interests' => 'required',
            // 'imp_exp_interests' => 'required',
            // 'consulting_interests' => 'required',
            // 'investing_interests' => 'required',
            // 'size' => "required|regex:$rangeRegex",
            // 'age' => "required|regex:$rangeRegex"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            # selecting business
            $business = Business::where("slug", $request->slug)->first();

            # incorporation_number
            $bs_input = $request->only(['incorporation_number']);
            $business->update($bs_input);

            # attached files
            ## handle the logo
            if ($request->has("logo")) {
                if ($business->logo != null) {
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
                ## handle the logo
                if ($business->logo != null) {
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
            }
            ## handle the banner
            if ($request->has("banner")) {
                if ($business->banner != null) {
                    Storage::delete($business->banner);
                }
                $path = $request->file('banner')->store('banners');
                $business->banner = $path;
                $business->save();
            }
            ## handle the certificate_of_incorporation
            if ($request->has("certificate_of_incorporation")) {
                if ($business->certificate_of_incorporation != null) {
                    Storage::delete($business->certificate_of_incorporation);
                }
                $path = $request->file('certificate_of_incorporation')->store('certificates_of_incorporation');
                $business->certificate_of_incorporation = $path;
                $business->save();
            }

            # new: other_services
            if (!is_array($request->other_services)) {
                $raw_other_services = json_decode($request->other_services);
                if ($raw_other_services == null) {
                    throw new \Exception("Invalid Json Field other_services");
                }
            } else {
                $raw_other_services = $request->other_services;
            }
            $other_services = [];
            foreach ($raw_other_services as $raw_other_service) {
                $other_service = SelectableBusinessService::firstOrCreate(['name' => $raw_other_service]);
                array_push($other_services, $other_service->id);
            }
            $business->otherServices()->sync($other_services);
            $business->save();
            # new: sector_interests
            if ($request->has("sector_interests")) {
                if (!is_array($request->sector_interests)) {
                    $raw_sector_interests = json_decode($request->sector_interests);
                    if ($raw_sector_interests == null) {
                        throw new \Exception("Invalid Json Field sector_interests");
                    }
                } else {
                    $raw_sector_interests = $request->sector_interests;
                }
                $sector_interests = [];
                foreach ($raw_sector_interests as $raw_sector_interest) {
                    $sector_interest = SelectableBusinessSector::firstOrCreate(['name' => $raw_sector_interest]);
                    array_push($sector_interests, $sector_interest->id);
                }
                $business->sectorInterests()->sync($sector_interests);
                $business->save();
            }
            # new: make_investment_interests
            if ($request->has("make_investment_interests")) {
                if ($request->has('make_investment_interests')) {
                    if (!is_array($request->make_investment_interests)) {
                        $raw_make_investment_interests = json_decode($request->make_investment_interests);
                        if ($raw_make_investment_interests == null) {
                            throw new \Exception("Invalid Json Field make_investment_interests");
                        }
                    } else {
                        $raw_make_investment_interests = $request->make_investment_interests;
                    }
                    $make_investment_interests = [];
                    foreach ($raw_make_investment_interests as $raw_make_investment_interest) {
                        $make_investment_interest = SelectableMakeInvestmentInterest::firstOrCreate(['name' => $raw_make_investment_interest]);
                        array_push($make_investment_interests, $make_investment_interest->id);
                    }
                    $business->makeInvestmentInterests()->sync($make_investment_interests);
                    $business->save();
                }
            }

            # commercial_interest
            if ($request->has("commercial_interests")) {
                if (!is_array($request->commercial_interests)) {
                    $raw_commercial_interests = json_decode($request->commercial_interests);
                    if ($raw_commercial_interests == null) {
                        throw new \Exception("Invalid Json Field commercial_interests");
                    }
                } else {
                    $raw_commercial_interests = $request->commercial_interests;
                }
                $commercial_interests = [];
                foreach ($raw_commercial_interests as $raw_commercial_interest) {
                    $commercial_interest = SelectableBusinessCommercialInterest::firstOrCreate(['name' => $raw_commercial_interest]);
                    array_push($commercial_interests, $commercial_interest->id);
                }
                $business->commercialInterests()->sync($commercial_interests);
                $business->save();
            }
            # distribution_interest
            if ($request->has("distribution_interest")) {
                if (!is_array($request->distribution_interests)) {
                    $raw_distribution_interests = json_decode($request->distribution_interests);
                    if ($raw_distribution_interests == null) {
                        throw new \Exception("Invalid Json Field distribution_interests");
                    }
                } else {
                    $raw_distribution_interests = $request->distribution_interests;
                }
                $distribution_interests = [];
                foreach ($raw_distribution_interests as $raw_distribution_interest) {
                    $distribution_interest = SelectableBusinessDistributionInterest::firstOrCreate(['name' => $raw_distribution_interest]);
                    array_push($distribution_interests, $distribution_interest->id);
                }
                $business->distributionInterests()->sync($distribution_interests);
                $business->save();
            }

            # country_interests
            if ($request->has("country_interests")) {
                if (!is_array($request->country_interests)) {
                    $raw_country_interests = json_decode($request->country_interests);
                    if ($raw_country_interests == null) {
                        throw new \Exception("Invalid Json Field country_interests");
                    }
                } else {
                    $raw_country_interests = $request->country_interests;
                }
                $country_interests = [];
                foreach ($raw_country_interests as $raw_country_interest) {
                    $country_interest = SelectableCountry::firstWhere(['name' => $raw_country_interest]);
                    array_push($country_interests, $country_interest->id);
                }
                $business->countryInterests()->sync($country_interests);
                $business->save();
            }
            # product_interests
            if ($request->has("product_interests")) {
                if (!is_array($request->product_interests)) {
                    $raw_product_interests = json_decode($request->product_interests);
                    if ($raw_product_interests == null) {
                        throw new \Exception("Invalid Json Field product_interests");
                    }
                } else {
                    $raw_product_interests = $request->product_interests;
                }
                $product_interests = [];
                foreach ($raw_product_interests as $raw_product_interest) {
                    $product_interest = SelectableBusinessProduct::firstOrCreate(['name' => $raw_product_interest]);
                    array_push($product_interests, $product_interest->id);
                }
                $business->productInterests()->sync($product_interests);
                $business->save();
            }

            # service_interest
            if ($request->has("service_interest")) {
                if (!is_array($request->service_interests)) {
                    $raw_service_interests = json_decode($request->service_interests);
                    if ($raw_service_interests == null) {
                        throw new \Exception("Invalid Json Field service_interests");
                    }
                } else {
                    $raw_service_interests = $request->service_interests;
                }
                $service_interests = [];
                foreach ($raw_service_interests as $raw_service_interest) {
                    $service_interest = SelectableBusinessService::firstOrCreate(['name' => $raw_service_interest]);
                    array_push($service_interests, $service_interest->id);
                }
                $business->serviceInterests()->sync($service_interests);
                $business->save();
            }
            # technology_interest
            if ($request->has("technology_interest")) {
                if (!is_array($request->technology_interests)) {
                    $raw_technology_interests = json_decode($request->technology_interests);
                    if ($raw_technology_interests == null) {
                        throw new \Exception("Invalid Json Field technology_interests");
                    }
                } else {
                    $raw_technology_interests = $request->technology_interests;
                }
                $technology_interests = [];
                foreach ($raw_technology_interests as $raw_technology_interest) {
                    $technology_interest = SelectableTechnology::firstOrCreate(['name' => $raw_technology_interest]);
                    array_push($technology_interests, $technology_interest->id);
                }
                $business->technologyInterests()->sync($technology_interests);
                $business->save();
            }
            # value_chains_dealing_with
            if ($request->has("value_chains_dealing_with")) {
                if (!is_array($request->value_chains_dealing_with)) {
                    $raw_value_chains = json_decode($request->value_chains_dealing_with);
                    if ($raw_value_chains == null) {
                        throw new \Exception("Invalid Json Field value_chains");
                    }
                } else {
                    $raw_value_chains = $request->value_chains_dealing_with;
                }
                $value_chains = [];
                foreach ($raw_value_chains as $raw_value_chain) {
                    $value_chain = SelectableValueChain::firstOrCreate(['name' => $raw_value_chain]);
                    array_push($value_chains, $value_chain->id);
                }
                $business->valueChainsDealingWith()->sync($value_chains);
                $business->save();
            }

            # executive_summary
            if ($request->has('executive_summary')) {
                $business->executive_summary = $request->executive_summary;
                $business->save();
            }
            # executive_summary_file
            if ($request->has('executive_summary_file')) {
                # handle the executive_summary_file
                if ($business->executive_summary_file != null) {
                    Storage::delete($business->executive_summary_file);
                }

                $path = $request->file('executive_summary_file')->store('executive_summary_files');
                $business->executive_summary_file = $path;
                $business->save();
            }

            // $businessRole = SelectableBusinessRole::where("name", $request_arr['business_role'])->first();
            // if($businessRole == null){
            //     $businessRole = new SelectableBusinessRole();
            //     $businessRole->name = $request->business_role;
            //     $businessRole->save();
            // }
            // $input['business_role_id'] = $businessRole->id;
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new BusinessResource($business), "Part two complete");
    }

    public function stepOne(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:businesses',
            'website' => 'required|unique:businesses',
            'email' => 'required|email|unique:businesses',
            'logo' => 'required|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $input = $request->except(["logo"]);
        $input["registration_step"] = 1;
        # business slug
        $input["slug"] = Str::slug($input['name']);

        if (collect($input)->contains("business_slug") && $business = Business::where("slug", $input['business_slug'])->get()) { //
            $business->update($input);
        } else {
            $business = Business::create($input);
        }

        # handle the logo
        if ($business->logo != null) {
            Storage::delete($business->logo);
        }

        $path = $request->file('logo')->store('logos');
        $business->logo = $path;
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Step one complete");
    }

    public function stepTwo(Request $request)
    {

        $messages = ['password.regex' => 'The :attribute contain at least 6 characters, a letter, a symbol and a number.',];
        $passwordRegex = "/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#@%]).*$/";

        $validator = Validator::make($request->all(), [
            'business_slug' => 'required|exists:businesses,slug',
            'full_name' => 'required',
            'business_role' => 'required',
            'password' => "required|regex:$passwordRegex",
            'confirm_password' => 'required|same:password',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        dd($request);

        # modifying input
        $request_arr = $request->all();
        $input = $request->except(["confirm_password", "business_role", "business_slug"]);
        $input['password'] = bcrypt($input['password']);

        # selecting business

        $business = Business::where("slug", $request_arr['business_slug'])->first();
        # validating user full name
        $user = $business->user;
        if ($user != null) {
            $fullNameTaken = User::where("full_name", $request_arr['full_name'])->where("id", "!=", $user->id)->exists();
        } else {
            $fullNameTaken = User::where("full_name", $request_arr['full_name'])->exists();
        }

        if ($fullNameTaken) {
            $fullNameTakenError = [
                "full_name" => ["The full name has already been taken"]
            ];
            return $this->sendError('Error validation', $fullNameTakenError, 400);
        }

        DB::transaction(function () use ($request, $input, $business, $request_arr) {
            # business_role
            $businessRole = SelectableBusinessRole::where("name", $request_arr['business_role'])->first();
            if ($businessRole == null) {
                $businessRole = new SelectableBusinessRole();
                $businessRole->name = $request->business_role;
                $businessRole->save();
            }
            $input['business_role_id'] = $businessRole->id;
            // dd($input);
            # creating user
            $user = $business->user;
            if ($user != null) {
                $user->update($input);
            } else {
                $user = User::create($input);
                $business->user_id = $user->id;
                $business->save();
            }
        });

        $business->registration_step = 2;
        $business->save();
        return $this->sendResponse(new BusinessResource($business), "Step two complete");
    }

    public function stepThree(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_slug' => 'required|exists:businesses,slug',
            'business_type' => 'required|in:African,Non-African,Investor',
            'countries' => 'required|array',
            'phone_number' => 'required',
            'headquarters_id' => 'required|exists:selectable_countries,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        # modifying input
        $request_arr = $request->all();
        $input = $request->except(["business_slug", "countries"]);


        $business = Business::where("slug", $request_arr['business_slug'])->first();

        DB::transaction(function () use ($request, $input, $business, $request_arr) {
            $business->update($input);

            # countries
            $business->operating_countries()->sync($request_arr["countries"]);
            $business->save();
        });

        $business->registration_step = 3;
        $business->save();
        return $this->sendResponse(new BusinessResource($business), "Step three complete");
    }


    public function stepFour(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_slug' => 'required|exists:businesses,slug',
            'main_sector' => 'required',
            'incorporation_number' => 'required',
            'clients' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        # modifying input
        $request_arr = $request->all();
        $input = $request->except(["business_slug", "main_sector"]);

        $business = Business::where("slug", $request_arr['business_slug'])->first();

        DB::transaction(function () use ($request, $input, $business, $request_arr) {
            # main_sector
            $mainSector = SelectableBusinessSector::where("name", $request_arr['main_sector'])->first();
            if ($mainSector == null) {
                $mainSector = new SelectableBusinessSector();
                $mainSector->name = $request->main_sector;
                $mainSector->save();
            }
            $input['main_sector_id'] = $mainSector->id;

            $business->update($input);
        });

        $business->registration_step = 4;
        $business->save();
        return $this->sendResponse(new BusinessResource($business), "Step four complete");
    }


    public function stepFive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_slug' => 'required|exists:businesses,slug',
            'platform_needs' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        # modifying input
        $request_arr = $request->all();
        $input = $request->except(["business_slug"]);

        $business = Business::where("slug", $request_arr['business_slug'])->first();
        $business->update($input);

        $business->registration_step = 5;
        $business->save();
        return $this->sendResponse(new BusinessResource($business), "Step five complete");
    }

    public function stepSix(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_slug' => 'required|exists:businesses,slug',
            'venture_sector' => 'required',
            'expand_services' => 'required|array',
            'expand_countries' => 'required|array',
            'executive_summary' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        # modifying input
        $request_arr = $request->all();
        $input = $request->except(["business_slug", "expand_countries", "venture_sector"]);

        $business = Business::where("slug", $request_arr['business_slug'])->first();
        DB::transaction(function () use ($request, $input, $business, $request_arr) {
            # venture_sector
            $sector = SelectableBusinessSector::where("name", $request_arr['venture_sector'])->first();
            if ($sector == null) {
                $sector = new SelectableBusinessSector();
                $sector->name = $request->venture_sector;
                $sector->save();
            }
            $input['venture_sector_id'] = $sector->id;

            # update
            $business->update($input);

            # expand countries
            $business->expand_countries()->sync($request_arr["expand_countries"]);
            $business->save();
        });

        $business->registration_step = 6;
        $business->save();
        return $this->sendResponse(new BusinessResource($business), "Step six complete");
    }
}
