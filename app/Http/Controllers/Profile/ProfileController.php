<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Profile\Client as ProfileClientResource;
use App\Http\Resources\Profile\TeamMember;
use App\Http\Resources\Profile\User as ProfileUserResource;
use App\Models\Profile\CallToAction;
use App\Http\Resources\Profile\CallToAction as CallToActionResource;
use App\Models\Selectables\SelectableBusinessRole;
use App\Models\User;
use App\Models\Business;
use App\Models\Profile\Client;
use App\Models\Profile\ProfileView;
use App\Models\Selectables\SelectableBusinessProduct;
use App\Models\Selectables\SelectableBusinessSector;
use App\Models\Selectables\SelectableBusinessService;
use App\Models\Selectables\SelectableCountry;
use App\Models\Selectables\SelectableTechnology;
use App\Models\Selectables\SelectableValueChain;
use App\Models\SocialNetworking\ChatMessage;
use App\Models\SocialNetworking\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseController
{
    //
    public function userProfile(){
        $authUser = User::find(Auth::user()->id);

        return $this->sendResponse(new ProfileUserResource($authUser), "Retrieved profile successfully");
    }

    public function otherProfile(Request $request, $slug){
        $authUserId = Auth::user()->id;

        $business = Business::firstWhere("slug", $slug);
        if ($business == null){
            return $this->sendError("Business with slug '$slug' not found", [], 404);
        }
        $user = $business->user;

        if($authUserId != $user->id){
            # add profile view
            $business->usersViewedProfile()->attach($authUserId);
        }

        return $this->sendResponse(new ProfileUserResource($user), "Retrieved profile successfully");
    }

    public function updateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'logo' => 'mimes:jpg,png,jpeg,gif,svg|max:10240',
            'banner' => 'mimes:jpg,png,jpeg,gif,svg|max:10240',
            'headquarters' => 'exists:selectable_countries,name'
        ]);
        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUser = User::find(Auth::user()->id);
        $business = $authUser->business;

        DB::beginTransaction();
        try{
            # name
            if($request->has("name")){
                $business->name = $request->name;
            }
            # headquarters
            if($request->has("headquarters")){
                $headquarters = SelectableCountry::firstWhere("name", $request->headquarters);
                $business->headquarters_id = $headquarters->id;
            }
            # website
            if($request->has("website")){
                $business->website = $request->website;
            }
            # links
            ## linked in
            if($request->has("linkedin_link")){
                $business->linkedin_link = $request->linkedin_link;
            }
            ## facebook
            if($request->has("facebook_link")){
                $business->facebook_link = $request->facebook_link;
            }
            ## twitter in
            if($request->has("twitter_link")){
                $business->twitter_link = $request->twitter_link;
            }
            # logo
            if($request->has('logo')){
                if($business->logo != null){
                    Storage::delete($business->logo);
                }
                $path = $request->file('logo')->store('logos');
                $business->logo = $path;
                $business->save();
            }
            # banner
            if($request->has('banner')){
                if($business->banner != null){
                    Storage::delete($business->banner);
                }
                $path = $request->file('banner')->store('banners');
                $business->banner = $path;
                $business->save();
            }
            # operating_countries
            if($request->has('operating_countries')){
                if(!is_array($request->operating_countries)){
                    $raw_operating_countries = json_decode($request->operating_countries);
                    if($raw_operating_countries == null){
                        throw new \Exception("Invalid Json Field operating_countries");
                    }
                }
                else{
                    $raw_operating_countries = $request->operating_countries;
                }
                $operating_countries = [];
                foreach($raw_operating_countries as $raw_operating_country){
                    $operating_country = SelectableCountry::firstWhere("name", $raw_operating_country);
                    if($operating_country == null){
                        throw new \Exception("operating_countries have an invalid country $raw_operating_country");
                    }
                    array_push($operating_countries, $operating_country->id);
                }
                $business->operating_countries()->sync($operating_countries);
            }
            # expand_countries
            if($request->has('expand_countries')){
                if(!is_array($request->expand_countries)){
                    $raw_expand_countries = json_decode($request->expand_countries);
                    if($raw_expand_countries == null){
                        throw new \Exception("Invalid Json Field expand_countries");
                    }
                }
                else{
                    $raw_expand_countries = $request->expand_countries;
                }
                $expand_countries = [];
                foreach($raw_expand_countries as $raw_expand_country){
                    $expand_country = SelectableCountry::firstWhere("name", $raw_expand_country);
                    if($expand_country == null){
                        throw new \Exception("expand_countries have an invalid country $raw_expand_country");
                    }
                    array_push($expand_countries, $expand_country->id);
                }
                $business->expand_countries()->sync($expand_countries);
            }
            # main_sector
            if($request->has('main_sector')){
                $mainSector = SelectableBusinessSector::firstOrCreate(["name"=> $request->main_sector])->first();
                $bs_input['main_sector_id'] = $mainSector->id;
            }
            # main_services
            if($request->has('main_services')){

                if(!is_array($request->main_services)){
                    $raw_main_services = json_decode($request->main_services);
                    if($raw_main_services == null){
                        throw new \Exception("Invalid Json Field main_services");
                    }
                }
                else{
                    $raw_main_services = $request->main_services;
                }
                $main_services = [];
                foreach($raw_main_services as $raw_main_service){
                    $main_service = SelectableBusinessService::firstOrCreate(['name' => $raw_main_service]);
                    array_push($main_services, $main_service->id);
                }
                $business->mainServices()->sync($main_services);
            }
            # main_products
            if($request->has("main_products")){
                if(!is_array($request->main_products)){
                    $raw_main_products = json_decode($request->main_products);
                    if($raw_main_products == null){
                        throw new \Exception("Invalid Json Field main_products");
                    }
                }
                else{
                    $raw_main_products = $request->main_products;
                }
                $main_products = [];
                foreach($raw_main_products as $raw_main_product){
                    $main_product = SelectableBusinessProduct::firstOrCreate(['name' => $raw_main_product]);
                    array_push($main_products, $main_product->id);
                }
                $business->mainProducts()->sync($main_products);
            }

            # service_interest
            if($request->has('service_interests')){

                if(!is_array($request->service_interests)){
                    $raw_service_interests = json_decode($request->service_interests);
                    if($raw_service_interests == null){
                        throw new \Exception("Invalid Json Field service_interests");
                    }
                }
                else{
                    $raw_service_interests = $request->service_interests;
                }
                $service_interests = [];
                foreach($raw_service_interests as $raw_service_interest){
                    $service_interest = SelectableBusinessService::firstOrCreate(['name' => $raw_service_interest]);
                    array_push($service_interests, $service_interest->id);
                }
                $business->serviceInterests()->sync($service_interests);
            }
            # technology_interests
            if($request->has('technology_interests')){
                if(!is_array($request->technology_interests)){
                    $raw_technology_interests = json_decode($request->technology_interests);
                    if($raw_technology_interests == null){
                        throw new \Exception("Invalid Json Field technology_interests");
                    }
                }
                else{
                    $raw_technology_interests = $request->technology_interests;
                }
                $technology_interests = [];
                foreach($raw_technology_interests as $raw_technology_interest){
                    $technology_interest = SelectableTechnology::firstOrCreate(['name' => $raw_technology_interest]);
                    array_push($technology_interests, $technology_interest->id);
                }
                $business->technologyInterests()->sync($technology_interests);
            }
            # value_chains_dealing_with
            if($request->has("value_chains_dealing_with")){ //
                if(!is_array($request->value_chains_dealing_with)){
                    $raw_value_chains = json_decode($request->value_chains_dealing_with);
                    if($raw_value_chains == null){
                        throw new \Exception("Invalid Json Field value_chains");
                    }
                }
                else{
                    $raw_value_chains = $request->value_chains_dealing_with;
                }
                $value_chains = [];
                foreach($raw_value_chains as $raw_value_chain){
                    $value_chain = SelectableValueChain::firstOrCreate(['name' => $raw_value_chain]);
                    array_push($value_chains, $value_chain->id);
                }
                $business->valueChainsDealingWith()->sync($value_chains);
            }
            # executive summary
            if($request->has('executive_summary')){
                $business->executive_summary = $request->executive_summary;
            }

            $business->save();
            DB::commit();
        }
        catch(\Throwable $e){
            DB::rollBack();
            return $this->sendError("Server Error", $e->getMessage(), 501);
        }

        return $this->sendResponse(new ProfileUserResource($authUser), "Updated profile successfully");
    }

    public function newTeamMember(Request $request){
        $authUser = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'full_name' =>'required|string|unique:users,full_name',
            'role'=>'required|string',
            'profile_image'=>'required|image|mimes:jpg,png,jpeg,gif,svg|max:10240',
            'quote' =>'required|string',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $authUser->business;

        $user_input = $request->except(["profile_image"]);
        $user_input['is_team_member'] = true;
        $user_input['teambusiness_id'] = $business->id;

        # upload user profile
        $profile_image_path = $request->file('profile_image')->store('profile_images');
        $user_input['profile_image'] = $profile_image_path;

        # business role
        $businessRole = SelectableBusinessRole::firstOrCreate(["name" => $request->role]);
        $user_input['business_role_id'] = $businessRole->id;

        # create user
        $user = User::create($user_input);

        return $this->sendResponse(new TeamMember($user), 'Team member created successfully');
    }

    public function teamMembers(Request $request){
        $authUser = User::find(Auth::user()->id);

        $business = $authUser->business;
        $teamMembers = $business->teamMembers;

        return $this->sendResponse(TeamMember::collection($teamMembers), "Retrieved team members successfully");
    }

    public function otherTeamMembers(Request $request, $slug){
        $business = Business::firstWhere("slug", $slug);
        if ($business == null){
            return $this->sendError("Business with slug '$slug' not found", [], 404);
        }

        $teamMembers = $business->teamMembers;

        return $this->sendResponse(TeamMember::collection($teamMembers), "Retrieved team members successfully");
    }

    public function deleteTeamMember(Request $request, $slug){
        $toValidate = ["slug"=>$slug];
        $validator = Validator::make($toValidate, [
            'slug' =>'exists:users,slug',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $user = User::where('slug', $slug);

        if($user->delete()) {
            return $this->sendResponse([] ,'Team member deleted successfully', 204);
        }

        return $this->sendError('Error in deleting team member', [], 400);
    }

    public function updateExecutiveSummary(Request $request){
        $validator = Validator::make($request->all(), [
            'executive_summary' =>'required|string|min:70',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUser = User::find(Auth::user()->id);

        $business = $authUser->business;
        $business->executive_summary = $request->executive_summary;
        $business->save();

        $authUser->refresh();

        return $this->sendResponse(new ProfileUserResource($authUser), "Updated Executive Summary successfully");
    }

    public function getClients(Request $request){
        $authUser = User::find(Auth::user()->id);
        $business = $authUser->business;
        $clients = $authUser->business->clients;

        return $this->sendResponse(ProfileClientResource::collection($clients), "Retrieved clients successfully");
    }

    public function getOtherClients(Request $request, $slug){
        $business = Business::firstWhere("slug", $slug);
        if ($business == null){
            return $this->sendError("Business with slug '$slug' not found", [], 404);
        }
        $clients = $business->clients;

        return $this->sendResponse(ProfileClientResource::collection($clients), "Retrieved clients successfully");
    }

    public function addClient(Request $request){
        $authUser = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'name' =>'required|string|unique:clients,name',
            'image'=>'required|image|mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $client_input = $request->only("name");
        $businessId = $authUser->business->id;

        $image_path = $request->file('image')->store('clients');
        $client_input['image'] = $image_path;
        $client_input['business_id'] = $businessId;

        $client = Client::create($client_input);

        return $this->sendResponse(new ProfileClientResource($client), 'Client created successfully');
    }

    public function deleteClient(Request $request, $id){
        $toValidate = ["id"=>$id];
        $validator = Validator::make($toValidate, [
            'id' =>'exists:clients,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $client = Client::find($id);

        if($client->delete()) {
            return $this->sendResponse([] ,'Client deleted successfully', 204);
        }

        return $this->sendError('Error in deleting Client', [], 400);
    }

    public function getCallToActions(Request $request){
        $authUser = User::find(Auth::user()->id);
        $callToActions = $authUser->callToActions;

        return $this->sendResponse(CallToActionResource::collection($callToActions), "Retrieved call to actions successfully");
    }

    public function getOtherCallToActions(Request $request, $slug){
        $business = Business::firstWhere("slug", $slug);
        if ($business == null){
            return $this->sendError("Business with slug '$slug' not found", [], 404);
        }
        $user = $business->user;
        $callToActions = $user->callToActions;

        return $this->sendResponse(CallToActionResource::collection($callToActions), "Retrieved call to actions successfully");
    }

    public function addCallToAction(Request $request){
        $authUser = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'title' =>'required|string|unique:call_to_actions,title',
            'content' =>'required|string',
            'image'=>'required|image|mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $cta_input = $request->except("image");
        $userId = $authUser->id;

        $image_path = $request->file('image')->store('call_to_actions');
        $cta_input['image'] = $image_path;
        $cta_input['user_id'] = $userId;

        $cta = CallToAction::create($cta_input);

        return $this->sendResponse(new CallToActionResource($cta), 'Call to action created successfully');
    }

    public function deleteCallToAction(Request $request, $id){
        $toValidate = ["id"=>$id];
        $validator = Validator::make($toValidate, [
            'id' =>'exists:call_to_actions,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $cta_input = CallToAction::find($id);

        if($cta_input->delete()) {
            return $this->sendResponse([] ,'Call to action deleted successfully', 204);
        }

        return $this->sendError('Error in deleting Call to action', [], 400);
    }

    public function requestForQuote(Request $request, $id){
        $callToAction = CallToAction::find($id);
        if($callToAction==null){
            return $this->sendError("Call to action with id $id not found", [], 404);
        }

        $recipientId = $callToAction->user->id;
        $sender = User::find(Auth::user()->id);

        if($recipientId == $sender->id){
            return $this->sendError("You cannot send a quote request to yourself",[], 400);
        }

        $conversation = Conversation::whereHas('users', function($query) use ($sender){
            $query->where('user_id', $sender->id);
        })->whereHas('users', function($query) use ($recipientId){
            $query->where('user_id', $recipientId);
        })->where("is_direct", true)->first();

        if($conversation == null){
            $conversation = Conversation::create("is_direct", true);
            $conversation->users->sync([$sender->id, $recipientId]);
        }

        $msgBody = "Hello my name is $sender->full_name from ".$sender->business->name.". We would like to request a quote for '$callToAction->title'.";

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $msgBody
        ]);

        return $this->sendResponse([], "Request for quote sent successfully");
    }

    public function scheduleCall(Request $request, $id){
        $callToAction = CallToAction::find($id);
        if($callToAction==null){
            return $this->sendError("Call to action with id $id not found", [], 404);
        }

        $recipientId = $callToAction->user->id;
        $sender = User::find(Auth::user()->id);

        if($recipientId == $sender->id){
            return $this->sendError("You cannot schedule a call to yourself",[], 400);
        }

        $conversation = Conversation::whereHas('users', function($query) use ($sender){
            $query->where('user_id', $sender->id);
        })->whereHas('users', function($query) use ($recipientId){
            $query->where('user_id', $recipientId);
        })->where("is_direct", true)->first();

        if($conversation == null){
            $conversation = Conversation::create("is_direct", true);
            $conversation->users->sync([$sender->id, $recipientId]);
        }

        $msgBody = "Hello my name is $sender->full_name from ".$sender->business->name.". We would like to request a call schedule for '$callToAction->title'.";

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $msgBody
        ]);

        return $this->sendResponse([], "Call schedule request sent successfully");
    }
}
