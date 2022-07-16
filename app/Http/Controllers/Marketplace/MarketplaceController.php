<?php

namespace App\Http\Controllers\Marketplace;

use App\Events\BusinessMatchedEvent;
use App\Events\BusinessLikedEvent;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Marketplace\Business as MarketplaceBusinessResource;
use App\Http\Resources\Marketplace\ConnectedBusiness as ConnectedBusinessResource;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

class MarketplaceController extends BaseController
{
    private function get_businesses_connection_age($business1, $business2)
    {
        $user1 = $business1->user;
        $forwardConnectionDate = $user1->businessLikeDislike()->wherePivot('is_like', true)->wherePivot('business_id', $business2->id)->first()->pivot->updated_at;

        $user2 = $business2->user;
        $reverseConnectionDate = $user2->businessLikeDislike()->wherePivot('is_like', true)->wherePivot('business_id', $business1->id)->first()->pivot->updated_at;

        if ($forwardConnectionDate->gte($reverseConnectionDate)) {
            $connectionDate = $forwardConnectionDate;
        } else {
            $connectionDate = $reverseConnectionDate;
        }

        return $connectionDate->diffForHumans();
    }

    private function get_connection_age($bs_id1, $bs_id2)
    {
        $business1 = Business::find($bs_id1);
        $business2 = Business::find($bs_id2);

        return $this->get_businesses_connection_age($business1, $business2);
    }

    public function currentBusiness(Request $request){ 
        $user = User::find(Auth::user()->id);
        $business = $user->business;

        $resource = new MarketplaceBusinessResource($business);
        return $this->sendResponse($resource, "current business retrieved successfully");

    }

    public function businesses(Request $request)
    {
        $suffixMatchKey = '';
        $query = Business::query();
        // handle name filter
        $query->where('user_id', "!=", Auth::user()->id);
        if ($name = $request->query("name")) { //
            $query->where('name', "like", "%$name%");
            $suffixMatchKey .= "$name";
        }
        // handle business type
        $suffixMatchKey .= "-";
        if ($business_type = $request->query("business_type")) { //
            $query->where('business_type', $business_type);
            $suffixMatchKey .= "$business_type";
        }
        // handle sector
        $suffixMatchKey .= "-";
        if ($sector = $request->query("sector")) { //
            $query->where(function ($s_query) use ($sector) {
                $s_query->whereHas("mainSector", function ($ss_query) use ($sector) {
                    $ss_query->where('name', $sector);
                });

                $s_query->orWhereHas('otherSectors', function ($ss_query) use ($sector) {
                    $ss_query->where('name', $sector);
                });

                $s_query->orWhereHas('sectorInterests', function ($ss_query) use ($sector) {
                    $ss_query->where('name', $sector);
                });
            });
            $suffixMatchKey .= "$sector";
        }
        $suffixMatchKey .= "-";
        // handle liked or disliked
        $liked_businesses = Auth::user()->businessLikeDislike->pluck('id')->toArray();
        $query->whereNotIn("id", $liked_businesses);
        $suffixMatchKey .= "-";
        // handle saved
        $saved_businesses = Auth::user()->savedBusinesses->pluck('id')->toArray();
        $query->whereNotIn("id", $saved_businesses);
        $suffixMatchKey .= "-";
        // handle followed
        $followed_businesses = Auth::user()->followedBusinesses->pluck('id')->toArray();
        $query->whereNotIn("id", $followed_businesses);
        $suffixMatchKey .= "-";


        $businesses = $this->matchingBusinesses("marketplace", $suffixMatchKey, $query);

        return $this->sendResponse($businesses, "Retrieved businesses successfully");
    }

    public function allBusinesses(Request $request)
    {
        $suffixMatchKey = '';
        $query = Business::query();

        $businesses = $this->matchingBusinesses("marketplace", $suffixMatchKey, $query);

        return $this->sendResponse($businesses, "Retrieved businesses successfully");
    }

    public function likeBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $otherUser = $business->user;

        $user = User::find(Auth::user()->id);
        $myBusiness = $user->business;

        $user->businessLikeDislike()->sync([$business->id => ['is_like' => true]], false);
        $this->setForgetMatchesFlag();

        // check if new connection
        $isConnection = $otherUser->businessLikeDislike()
            ->wherePivot('business_id', $myBusiness->id)
            ->wherePivot('is_like', true)
            ->exists();

        // if ($isConnection) {
        //     $connectedBusinesses = [$myBusiness, $business];
        //     event(new BusinessMatchedEvent($connectedBusinesses));
        // }

        // $associatedBusinesses = ["initiator"=>$myBusiness, "liked"=>$business];
        // event(new BusinessLikedEvent($associatedBusinesses));

        return $this->sendResponse([], "You liked a business");
    }

    public function dislikeBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $user = User::find(Auth::user()->id);

        $user->businessLikeDislike()->sync([$business->id => ['is_like' => false]], false);
        $this->setForgetMatchesFlag();
        return $this->sendResponse([], "You disliked a business");
    }

    public function undoLikeDislikeBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $user = User::find(Auth::user()->id);

        $user->businessLikeDislike()->detach($business->id);
        $this->setForgetMatchesFlag();
        return $this->sendResponse([], "Business like or dislike undone");
    }

    public function saveBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $user = User::find(Auth::user()->id);

        $user->savedBusinesses()->sync([$business->id], false);
        $this->setForgetMatchesFlag();
        return $this->sendResponse([], "You saved a business");
    }

    public function unsaveBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $user = User::find(Auth::user()->id);

        $user->savedBusinesses()->detach($business->id);
        $this->setForgetMatchesFlag();
        return $this->sendResponse([], "You removed a business from saved businesses");
    }

    public function followBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $user = User::find(Auth::user()->id);

        $user->followedBusinesses()->sync([$business->id], false);
        $this->setForgetMatchesFlag();
        return $this->sendResponse([], "You followed a business");
    }

    public function unfollowBusiness(Request $request, $slug)
    {
        $toValidate = ['slug' => $slug];
        $validator = Validator::make($toValidate, [
            'slug' => 'exists:businesses,slug',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Error Validation', $validator->errors(), 400);
        }

        $business = Business::firstWhere("slug", $slug);
        $user = User::find(Auth::user()->id);

        $user->followedBusinesses()->detach($business->id);
        $this->setForgetMatchesFlag();
        return $this->sendResponse([], "You unfollowed a business");
    }

    public function connectedBusinesses(Request $request)
    {
        # user likes business and business has user that liked the business
        $user = User::find(Auth::user()->id);

        // user to bs to user

        // user to bs

        $business = $user->business;
        $likedBusinesses = $user->businessLikeDislike()->wherePivot('is_like', true)->get();
        // $likedBusinessIds = $likedBusinesses->pluck("id"); 

        $usersWhichLiked = $business->userLike()->wherePivot('is_like', true)->with("business")->get();
        $bsIds = $usersWhichLiked->pluck('business.id');

        $connected = $likedBusinesses->filter(function ($bs, $key) use ($bsIds) {
            return $bsIds->contains($bs['id']);
        });

        $connectedCollection = ConnectedBusinessResource::collection($connected);
        $arrConnectedCollection = json_decode($connectedCollection->toJson(), true);

        foreach ($arrConnectedCollection as $key => $connection) {
            $age = $this->get_connection_age($business->id, $connection["id"]);
            $connection["connection_age"] = $age;
            $arrConnectedCollection[$key] = $connection;
        }

        // dd($resource);
        return $this->sendResponse($arrConnectedCollection, "Connected businesses retrieved successfully");
    }

    public function clearLikeDislike()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->businessLikeDislike()->detach();
            // yield($user->businessLikeDislike()->count());
            $this->setForgetMatchesFlag($user->id);
        }

        return $this->sendResponse([], "cleared likes and dislikes");
    }

    public function recommendations()
    {
    }
}
