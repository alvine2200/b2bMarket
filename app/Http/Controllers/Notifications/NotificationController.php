<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Notifications\Notifications as NotificationsResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    //
    public function getNotifications(Request $request){
        $authUser = User::find(Auth::user()->id);
        $notifications = $authUser->notifications;

        $limitedNotifications = $notifications;
        if($request->count != null && is_numeric($request->count)){
            $limitedNotifications = $notifications->take((int) $request->count);    
        }
        
        $resp = ["count" => $notifications->count(), "notifications" => NotificationsResource::collection($limitedNotifications)];
        

        return $this->sendResponse($resp, "Retrieved notifications successfully");
    }
    
    public function readSingleNotification(Request $request, $id){
        $authUser = User::find(Auth::user()->id);
        $authUser->notifications()->where("id", $id)->delete();

        return $this->sendResponse([], "Read notification");
    }
    
    public function readAllNotifications(){
        $authUser = User::find(Auth::user()->id);
        $authUser->notifications()->delete();

        return $this->sendResponse([], "Read all notifications");
    }
}
