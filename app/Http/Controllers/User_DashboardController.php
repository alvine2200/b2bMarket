<?php

namespace App\Http\Controllers;

use App\Http\Resources\Marketplace\ConnectedBusiness as ConnectedBusinessResource;
use App\Http\Resources\ProductsServices\PurchaseOrder as PurchaseOrderResource;
use App\Http\Resources\ProductsServices\SummarizedPurchaseOrder;
use App\Http\Resources\SocialNetworking\Conversation as ConversationResource;
use App\Models\Business;
use App\Models\ProductsServices\Invoice;
use App\Models\ProductsServices\PurchaseOrder;
use App\Models\SocialNetworking\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User_DashboardController extends BaseController
{
    private function get_businesses_connection_age($business1, $business2){
        $user1 = $business1->user;
        $forwardConnectionDate = $user1->businessLikeDislike()->wherePivot('is_like', true)->wherePivot('business_id', $business2->id)->first()->pivot->updated_at;
        
        $user2 = $business2->user;
        $reverseConnectionDate = $user2->businessLikeDislike()->wherePivot('is_like', true)->wherePivot('business_id', $business1->id)->first()->pivot->updated_at;
        
        if($forwardConnectionDate->gte($reverseConnectionDate)){
            $connectionDate = $forwardConnectionDate;
        }
        else{ 
            $connectionDate = $reverseConnectionDate;
        }
        
        return $connectionDate->diffForHumans();
    }
    
    private function get_connection_age($bs_id1, $bs_id2){
        $business1 = Business::find($bs_id1);
        $business2 = Business::find($bs_id2);

        return $this->get_businesses_connection_age($business1, $business2);
    }

    public function dashboard(){
        $authUser = User::find(Auth::user()->id);
        $dashData = [];

        # connected businesses
        $business = $authUser->business;
        $likedBusinesses = $authUser->businessLikeDislike()->wherePivot('is_like', true)->get();
        // $likedBusinessIds = $likedBusinesses->pluck("id"); 

        $usersWhichLiked = $business->userLike()->wherePivot('is_like', true)->with("business")->get();
        $bsIds = $usersWhichLiked->pluck('business.id');

        $connected = $likedBusinesses->filter(function ($bs, $key) use ($bsIds) {
            return $bsIds->contains($bs['id']);
        });

        $connectedCollection = ConnectedBusinessResource::collection($connected);
        $arrConnectedCollection = json_decode($connectedCollection->toJson(), true);

        foreach($arrConnectedCollection as $key=>$connection) {
            $age = $this->get_connection_age($business->id, $connection["id"]);
            $connection["connection_age"] = $age;
            $arrConnectedCollection[$key] = $connection;
        }
        $dashData["connectedBusinesses"] = $arrConnectedCollection;

        # income vs expenses\
        $boughtOrderQuery = PurchaseOrder::where("user_id", $authUser->id);

        $soldOrderQuery = PurchaseOrder::whereHas("products", function ($query) use ($business) {
            $query->where("business_id", $business->id);
        });

        $curDate = now()->startOfMonth();

        $expenseData = collect([]);
        $incomeData = collect([]);
        for ($i = 0; $i < 12; $i++) {
            $curMonth = $curDate->month;
            $curYear = $curDate->year;

            $bought = $boughtOrderQuery
                ->whereMonth("created_at", $curMonth)
                ->whereYear("created_at", $curYear)
                ->get();
            $expense = $bought->sum('total');
            $expenseData->push(["month" => $curMonth, "year" => $curYear, "amount" => $expense]);

            $sold = $soldOrderQuery
                ->whereMonth("created_at", $curMonth)
                ->whereYear("created_at", $curYear)
                ->get();
            $income = $sold->sum('total');
            $incomeData->push(["month" => $curMonth, "year" => $curYear, "amount" => $income]);

            $curDate = $curDate->subMonths(1);
        }

        $dashData["expensesChart"] = $expenseData;
        $dashData["incomeChart"] = $incomeData;

        $orderQuery = PurchaseOrder::where("user_id", $authUser->id)
            ->orWhereHas("products", function ($query) use ($business) {
                $query->where("business_id", $business->id);
            })
            ->orderByDesc("id");
        
        # total orders
        $totalOrders = $orderQuery->count();
        $dashData["totalOrders"] = $totalOrders;

        # total invoices
        $totalInvoices = Invoice::whereHas("order", function($q) use ($authUser, $business){
            $q->where("user_id", $authUser->id);
            $q->orWhereHas("products", function ($query) use ($business) {
                $query->where("business_id", $business->id);
            });
        })->count();
        $dashData["totalInvoices"] = $totalInvoices;
        
        # total connections
        $dashData["totalMatches"] = count($arrConnectedCollection);

        # total products
        $totalProducts = $business->products->count();
        $dashData["totalProducts"] = $totalProducts;
        
        # total profile views
        $totalProfileViews = $business->usersViewedProfile->count();
        $dashData["totalProfileViews"] = $totalProfileViews;

        # recent orders
        $recentOrders = $orderQuery
            ->limit(5)
            ->get();
        $dashData["recentOrders"] = SummarizedPurchaseOrder::collection($recentOrders);


        # top 5 conversations
        $directQuery = Conversation::whereHas('users', function($s_query) use ($authUser){
            $s_query->where('user_id', $authUser->id);
        });

        $directConversations = $directQuery->get();


        $indirectQuery = Conversation::whereHas('chatRoom', function($s_query) use ($authUser){ 
            $s_query->whereHas('users', function($s_query) use ($authUser){
                $s_query->where('user_id', $authUser->id);
            });
        });

        $indirectConversations = $indirectQuery->get();

        $conversations = $directConversations
                            ->merge($indirectConversations)
                            ->sortByDesc('lastActivity')
                            ->where("isDeleted", False)
                            ->where("isArchived", False);

        $dashData["top5Conversations"] = ConversationResource::collection($conversations->take(5));

        # unread
        $unreadCount = $conversations->sum("unread");
        $dashData["unreadCount"] = $unreadCount;

        return $this->sendResponse($dashData, 'dashboard data retrieved successfully');
    }


}
