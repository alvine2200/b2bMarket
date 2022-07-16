<?php

namespace App\Http\Controllers\SocialNetworking;

use App\Events\SocialNetworking\ChatMessageCreatedEvent;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Business;
use App\Models\SocialNetworking\BusinessReview;
use App\Models\SocialNetworking\ChatRoom;
use App\Models\SocialNetworking\Conversation;
use App\Models\SocialNetworking\ChatMessage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SocialNetworking\BusinessReview as BusinessReviewResource;
use App\Http\Resources\SocialNetworking\ChatRoom as ChatRoomResource;
use App\Http\Resources\SocialNetworking\ChatMessage as ChatMessageResource;
use App\Http\Resources\SocialNetworking\Conversation as ConversationResource;
use App\Http\Resources\User as UserResource;

class SocialNetworkingController extends BaseController
{
    # todo: notifications on new business created
    public function listAllBusinessReviews(Request $request){
        $reviews = BusinessReview::all();

        return $this->sendResponse(BusinessReviewResource::collection($reviews), "Business reviews listed successfully");
    }
    
    public function listBusinessReviews(Request $request, $id){
        $business = Business::find($id);

        if($business == null){
            return $this->sendError('Business does not exist', []);
        }

        $reviews = $business->reviews;

        return $this->sendResponse(BusinessReviewResource::collection($reviews), "Business reviews listed successfully");
    }
    
    public function listUserBusinessReviews(Request $request){
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $reviews = $authUser->reviews;

        return $this->sendResponse(BusinessReviewResource::collection($reviews), "Business reviews listed successfully");
    }

    public function reviewBusiness(Request $request){
        # todo: check business interactions exist before review
        $input = $request->all();
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $validator = Validator::make($input, [
            "business_id" => "required|exists:businesses,id",
            "comment" => "required|string",
            "stars" => "required|number"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = Business::find($input['business_id']);

        if(BusinessReview::where('user_id', $authUserId)->where('business_id', $input['business_id'])->exists()){
            return $this->sendError('Business already reviewed', [], 400);
        }
        
        $bsReview = BusinessReview::create([
            "user_id"=> $authUserId,
            "business_id" => $input['business_id'],
            "stars" => $input['stars'],
            "comment" => $input['comment']
        ]);

        return $this->sendResponse(new BusinessReviewResource($bsReview), "Business reviewed successfully");
    }

    public function sendChatMessageToRecipient(Request $request){
        $input = $request->all();
        $sender = User::find(Auth::user()->id);

        $validator = Validator::make($input, [
            "recipient_id" => "required|exists:users,id",
            "body" => "required|string"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $recipient_id = $input['recipient_id'];
        if ($sender->id == $recipient_id){
            return $this->sendError('Error validation', ["recipient_id" => ["You are not allowed to send a message to yourself"]], 400);
        }
        

        $conversation = Conversation::whereHas('users', function($query) use ($sender){
            $query->where('user_id', $sender->id);
        })->whereHas('users', function($query) use ($recipient_id){
            $query->where('user_id', $recipient_id);
        })->where("is_direct", true)->first();

        
        if($conversation == null){
            $conversation = Conversation::create(["is_direct"=> true]);
            $conversation->users()->sync([$sender->id, $recipient_id]);
        }

        // check if blocked
        if($conversation->is_blocked){
            return $this->sendError('You are blocked from this conversation', [], 403);
        }

        $chatMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $input['body']
        ]);


        if($request->hasfile('attachments')){
            $attachments = $request->file('attachments');
            foreach($attachments as $attachment){ 
                $path = $attachment->store('chat_attachments');
                $chatMessage->attachments()->create(["attachment"=> $path]);
            }
        }

        event(new ChatMessageCreatedEvent($chatMessage));

        return $this->sendResponse(new ChatMessageResource($chatMessage), "Chat message sent successfully");
    }
    
    public function sendChatMessageToChatRoom(Request $request){
        $input = $request->all();
        $sender = User::find(Auth::user()->id);

        $validator = Validator::make($input, [
            "chat_room_id" => "required|exists:chat_rooms,id",
            "body" => "required|string"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }
        
        # check if member of chat room
        $chatRoom = ChatRoom::find($input['chat_room_id']);
        $conversation = $chatRoom->conversation;
        if($chatRoom->users()->where('user_id', $sender->id)->doesntExist()){
            return $this->sendError('You do not have permissions to send chat in this chat room.', $validator->errors(), 403);
        }

        // check if blocked
        if($conversation->is_blocked){
            return $this->sendError('You are blocked from this conversation', [], 403);
        }

        $chatMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $input['body']
        ]);


        if($request->hasfile('attachments')){
            $attachments = $request->file('attachments');
            foreach($attachments as $attachment){ 
                $path = $attachment->store('chat_attachments');
                $chatMessage->attachments()->create(["attachment"=> $path]);
            }
        }

        event(new ChatMessageCreatedEvent($chatMessage));

        return $this->sendResponse(new ChatMessageResource($chatMessage), "Chat message sent successfully");
    }
    
    public function sendChatMessageToConversation(Request $request){
        $input = $request->all();
        $sender = User::find(Auth::user()->id);

        $validator = Validator::make($input, [
            "conversation_id" => "required|exists:conversations,id",
            "body" => "required|string"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }
        
        # check if member of chat room
        $conversation = Conversation::find($input['conversation_id']);
        // if($chatRoom->users()->where('user_id', $sender->id)->doesntExist()){
        //     return $this->sendError('You do not have permissions to send chat in this chat room.', $validator->errors());
        // }

        // check if blocked
        if($conversation->is_blocked){
            return $this->sendError('You are blocked from this conversation', [], 403);
        }

        $chatMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $input['body']
        ]);


        if($request->hasfile('attachments')){
            $attachments = $request->file('attachments');
            foreach($attachments as $attachment){ 
                $path = $attachment->store('chat_attachments');
                $chatMessage->attachments()->create(["attachment"=> $path]);
            }
        }

        event(new ChatMessageCreatedEvent($chatMessage));

        return $this->sendResponse(new ChatMessageResource($chatMessage), "Chat message sent successfully");
    }

    public function sendChatMessage(Request $request){
        $input = $request->all();

        $RECIPIENT = 'RECIPIENT';
        $CHAT_ROOM = 'CHAT_ROOM';
        $CONVERSATION = 'CONVERSATION';

        if($input["chat_room_id"] == null && $input["conversation_id"] == null){
            $validator = Validator::make($input, [
                "recipient_id" => "required",
                "body" => "required|string"
            ]);
            $mode = $RECIPIENT;
        }
        else if($input["recipient_id"] == null && $input["conversation_id"] == null){
            $validator = Validator::make($input, [
                "chat_room_id" => "required",
                "body" => "required|string"
            ]);
            $mode = $CHAT_ROOM;
        }
        else{ 
            $validator = Validator::make($input, [
                "conversation_id" => "required",
                "body" => "required|string"
            ]);
            $mode = $CONVERSATION;
        }

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $sender = User::find(Auth::user()->id);

        if($mode==$RECIPIENT){
            $recipient_id = $input['recipient_id'];
            $conversation = Conversation::whereHas('users', function($query) use ($sender){
                $query->where('user_id', $sender->id);
            })->whereHas('users', function($query) use ($recipient_id){
                $query->where('user_id', $recipient_id);
            })->where("is_direct", true)->get();

            if($conversation == null){
                $conversation = Conversation::create("is_direct", true);
                $conversation->users->sync([$sender->id, $recipient_id]);
            }
        }
        else if($mode == $CHAT_ROOM){
            $chatRoom = ChatRoom::find($input['chat_room_id']);
            $conversation = $chatRoom->conversation;
        }
        else{
            $conversation = Conversation::find($input['conversation_id']);
        }


        $chatMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $input['body']
        ]);

        return $this->sendResponse(new ChatMessageResource($chatMessage), "Chat message sent successfully");
    }

    public function listUserConversations(Request $request){
        $authUser = User::find(Auth::user()->id);

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

        return $this->sendResponse(ConversationResource::collection($conversations), "User Conversations listed successfully");
    }
    
    public function listArchivedUserConversations(Request $request){
        $authUser = User::find(Auth::user()->id);

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
                            ->whereNotNull("archivedAt");

        return $this->sendResponse(ConversationResource::collection($conversations), "User Archived Conversations listed successfully");
    }

    private function doReadConversation($conversation){
        $query = $conversation->chatMessages();
        # cleared at
        $clearedAt = $conversation->clearedAt;
        if($clearedAt != null){
            $query = $query->where("created_at", '>' , $clearedAt);
        }
        # deleted at
        $deletedAt = $conversation->deletedAt;
        if($deletedAt != null){
            $query = $query->where("created_at", '>' , $deletedAt);
        }
        # archived at
        $archivedAt = $conversation->archivedAt;
        if($archivedAt != null){
            $query = $query->where("created_at", '>' , $archivedAt);
        }

        $chatMessages = $query->get();

        return $chatMessages;
    }

    public function readConversation(Request $request, $id){
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $conversation = Conversation::find($id);

        if(!$conversation->users->contains($authUser) && ($conversation->chatRoom != null && !$conversation->chatRoom->users->contains($authUser))){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        if($conversation == null){
            return $this->sendError('Conversation not found.', [], 400);
        }

        $chatMessages = $this->doReadConversation($conversation);

        # update last read
        if($conversation->is_direct){
            $conversation->users()->updateExistingPivot($authUserId, ['last_read'=> now()]);
        }
        else{
            $conversation->chatRoom->users()->updateExistingPivot($authUserId, ['last_read'=> now()]);
        }

        return $this->sendResponse(ChatMessageResource::collection($chatMessages), "Conversation read");
    }

    private function doReadArchivedConversation($conversation){
        $query = $conversation->chatMessages();
        # cleared at
        $clearedAt = $conversation->clearedAt;
        if($clearedAt != null){
            $query = $query->where("created_at", '>' , $clearedAt);
        }
        # deleted at
        $deletedAt = $conversation->deletedAt;
        if($deletedAt != null){
            $query = $query->where("created_at", '>' , $deletedAt);
        }
        # archived at
        $archivedAt = $conversation->archivedAt;
        if($archivedAt != null){
            $query = $query->where("created_at", '<' , $archivedAt);
            $chatMessages = $query->get();
        }
        else{ 
            $chatMessages = collect([]);
        }

        return $chatMessages;
    }
    
    public function readArchivedConversation(Request $request, $id){
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $conversation = Conversation::find($id);

        if(!$conversation->users->contains($authUser) && ($conversation->chatRoom != null && !$conversation->chatRoom->users->contains($authUser))){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        if($conversation == null){
            return $this->sendError('Conversation not found.', [], 400);
        }

        $chatMessages = $this->doReadArchivedConversation($conversation);

        # update last read
        if($conversation->is_direct){
            $conversation->users()->updateExistingPivot($authUserId, ['last_read'=> now()]);
        }
        else{
            $conversation->chatRoom->users()->updateExistingPivot($authUserId, ['last_read'=> now()]);
        }

        return $this->sendResponse(ChatMessageResource::collection($chatMessages), "Conversation read");
    }

    
    public function readChatRoomMsgs(Request $request, $id){
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $chatRoom = ChatRoom::whereHas('users', function($query) use ($authUserId){
            $query->where('user_id', $authUserId);
        })->where("id", $id)->first();

        if($chatRoom == null){
            return $this->sendError('Make sure you are part of the chat room first.', [], 400);
        }

        $conversation = $chatRoom->conversation;
        if($conversation == null){
            return $this->sendResponse([], "Conversation read");
        }

        $chatMessages = $this->doReadConversation($conversation);

        # update last read
        $conversation->chatRoom->users()->updateExistingPivot($authUserId, ['last_read'=> now()]);

        return $this->sendResponse(ChatMessageResource::collection($chatMessages), "Conversation read");
    }
    
    public function readArchivedChatRoomMsgs(Request $request, $id){
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $chatRoom = ChatRoom::whereHas('users', function($query) use ($authUserId){
            $query->where('user_id', $authUserId);
        })->where("id", $id)->first();

        if($chatRoom == null){
            return $this->sendError('Make sure you are part of the chat room first.', [], 400);
        }

        $conversation = $chatRoom->conversation;
        if($conversation == null){
            return $this->sendResponse([], "Conversation read");
        }

        $chatMessages = $this->doReadArchivedConversation($conversation);

        return $this->sendResponse(ChatMessageResource::collection($chatMessages), "Archived Chat Room Conversation read");
    }
    
    public function readDirectMsgs(Request $request, $id){
        $authUserId = Auth::user()->id;

        $conversation = Conversation::whereHas('users', function($query) use ($authUserId){
            $query->where('user_id', $authUserId);
        })->whereHas('users', function($query) use ($id){
            $query->where('user_id', $id);
        })->first();

        if($conversation == null){
            return $this->sendResponse([], "Conversation read");
        }

        $chatMessages = $this->doReadConversation($conversation);

        # update last read
        $conversation->users()->updateExistingPivot($authUserId, ['last_read'=> now()]);

        return $this->sendResponse(ChatMessageResource::collection($chatMessages), "Conversation read");
    }
    
    public function readArchivedDirectMsgs(Request $request, $id){
        $authUserId = Auth::user()->id;

        $conversation = Conversation::whereHas('users', function($query) use ($authUserId){
            $query->where('user_id', $authUserId);
        })->whereHas('users', function($query) use ($id){
            $query->where('user_id', $id);
        })->first();

        if($conversation == null){
            return $this->sendResponse([], "Conversation read");
        }

        $chatMessages = $this->doReadArchivedConversation($conversation);

        return $this->sendResponse(ChatMessageResource::collection($chatMessages), "Archived Conversation read");
    }


    public function listChatRooms()
    {
        $user = User::find(Auth::user()->id);
        $chatRooms = $user->chat_rooms()->where("is_acknowledged", true)->get();

        return $this->sendResponse(ChatRoomResource::collection($chatRooms), "My chat rooms listed successfully");
    }
    
    public function listChatRoomUsers(Request $request, $id)
    {
        $chatRoom = ChatRoom::find($id);

        # confirm if user is in chat room
        $authUserId = Auth::user()->id;
        if($chatRoom->users()->where(['user_id' => $authUserId])->doesntExist()){
            return $this->sendError('You do not have permissions to view users in chat room.', [], 400);
        }
        $users = $chatRoom->users;

        return $this->sendResponse(UserResource::collection($users), "My chat rooms listed successfully");
    }

    public function createChatRoom(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "name" => "required|unique:chat_rooms"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUserId = Auth::user()->id;

        $conversation = Conversation::create(["is_direct" => false]);
        $chatRoom = ChatRoom::create([
            "conversation_id" => $conversation->id,
            "name" => $input['name'],
            "created_by" => $authUserId
        ]);


        $chatRoom->users()->attach($authUserId,[
            "is_acknowledged" => true,
            "is_admin" => true,
        ]);

        return $this->sendResponse(new ChatRoomResource($chatRoom), "Chat room created successfully");
    }

    public function addUserToChatRoom(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "chat_room_id" => "required|exists:chat_rooms,id",
            "user_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $chatRoom = ChatRoom::find($input["chat_room_id"]);

        #check if user in chat room and is admin
        if($chatRoom->users()->where("user_id", $authUserId)->where("is_admin", true)->doesntExist()){ 
            return $this->sendError('You do not have permissions to add user to this chat room.', $validator->errors(), 403);
        }

        #check if user already a member of chat room
        if($chatRoom->users()->where("user_id", $input["user_id"])->doesntExist()){
            $chatRoom->users()->attach($input["user_id"], ["is_acknowledged" => true]);
        }

        return $this->sendResponse(new ChatRoomResource($chatRoom), "User added to chat room successfully");
    }
    
    public function removeUserToChatRoom(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "chat_room_id" => "required|exists:chat_rooms,id",
            "user_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUser = User::find(Auth::user()->id);

        $chatRoom = ChatRoom::find($input["chat_room_id"]);

        #check if user in chat room and is admin
        if($chatRoom->users()->where("user_id", $authUser->id)->where("is_admin", true)->doesntExist()){ 
            return $this->sendError('You do not have permissions to remove user from this chat room.', $validator->errors(), 403);
        }

        $chatRoom->users()->detach($input["user_id"]);

        return $this->sendResponse(new ChatRoomResource($chatRoom), "User removed from chat room successfully");
    }

    public function joinChatRoomByLink(Request $request, $link){
        if(ChatRoom::where("link", $link)->doesntExist()){
            return $this->sendError('Chat room with specified link not found', []);
        }

        $authUserId = Auth::user()->id;

        $chatRoom = ChatRoom::where("link", $link)->first();
        if($chatRoom->users()->where("user_id", $authUserId)->doesntExist()){
            $chatRoom->users()->attach($authUserId);
        }

        return $this->sendResponse(new ChatRoomResource($chatRoom), "Successfully joined chat room by link");
    }

    public function approveChatRoomUser(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "chat_room_id" => "required|exists:chat_rooms,id",
            "user_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $chatRoom = ChatRoom::find($input["chat_room_id"]);
        #check if user in chat room and is admin
        if($chatRoom->users()->where("user_id", Auth::user()->id)->where("is_admin", true)->doesntExist()){ 
            return $this->sendError('You do not have permissions to approve user into this chat room.', $validator->errors(), 403);
        }

        $chatRoom->users()->updateExistingPivot($input['user_id'], ['is_acknowledged' => true]);

        return $this->sendResponse(new ChatRoomResource($chatRoom), "Successfully approved user");
    }
    
    public function promoteChatRoomUser(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "chat_room_id" => "required|exists:chat_rooms,id",
            "user_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $chatRoom = ChatRoom::find($input["chat_room_id"]);
        #check if user in chat room and is admin
        if($chatRoom->users()->where("user_id", Auth::user()->id)->where("is_admin", true)->doesntExist()){ 
            return $this->sendError('You do not have permissions to add user to this chat room.', $validator->errors(), 403);
        }

        $chatRoom->users()->updateExistingPivot($input['user_id'], ['is_admin' => true]);

        return $this->sendResponse(new ChatRoomResource($chatRoom), "Successfully promoted user");
    }
    
    public function demoteChatRoomUser(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "chat_room_id" => "required|exists:chat_rooms,id",
            "user_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $chatRoom = ChatRoom::find($input["chat_room_id"]);
        #check if user in chat room and is admin
        if($chatRoom->users()->where("user_id", Auth::user()->id)->where("is_admin", true)->doesntExist()){ 
            return $this->sendError('You do not have permissions to demove user in this chat room.', $validator->errors(), 403);
        }

        $chatRoom->users()->updateExistingPivot($input['user_id'], ['is_admin' => false]);

        return $this->sendResponse(new ChatRoomResource($chatRoom), "Successfully demoted user");
    }

    public function clearConversation(Request $request, $id){
        $toValidate = ["conversation_id" => $id];
        $validator = Validator::make($toValidate, [
            "conversation_id" => "exists:conversations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $user = User::find(Auth::user()->id);
        $conversation = Conversation::find($id);

        $userInConversation = false;
        $usersRelationToUpdate = null;
        if(($conversation->chatRoom != null && $conversation->chatRoom->users->contains($user))){
            $userInConversation = true;
            $usersRelationToUpdate = $conversation->chatRoom->users();
        }

        if($conversation->users->contains($user)){
           $userInConversation = true;
           $usersRelationToUpdate = $conversation->users();
        }

        if(!$userInConversation){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        $usersRelationToUpdate->updateExistingPivot($user, ["cleared_at"=>now()]);

        return $this->sendResponse([], "Successfully cleared conversation");
    }
    
    public function deleteConversation(Request $request, $id){
        $toValidate = ["conversation_id" => $id];
        $validator = Validator::make($toValidate, [
            "conversation_id" => "exists:conversations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $user = User::find(Auth::user()->id);
        $conversation = Conversation::find($id);

        $userInConversation = false;
        $usersRelationToUpdate = null;
        if(($conversation->chatRoom != null && $conversation->chatRoom->users->contains($user))){
            $userInConversation = true;
            $usersRelationToUpdate = $conversation->chatRoom->users();
        }

        if($conversation->users->contains($user)){
           $userInConversation = true;
           $usersRelationToUpdate = $conversation->users();
        }

        if(!$userInConversation){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        $usersRelationToUpdate->updateExistingPivot($user, ['last_read'=> now(), "cleared_at"=>now(), "deleted_at"=>now()]);

        return $this->sendResponse([], "Successfully deleted conversation");
    }
    
    public function archiveConversation(Request $request, $id){
        $toValidate = ["conversation_id" => $id];
        $validator = Validator::make($toValidate, [
            "conversation_id" => "exists:conversations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $user = User::find(Auth::user()->id);
        $conversation = Conversation::find($id);

        $userInConversation = false;
        $usersRelationToUpdate = null;
        if(($conversation->chatRoom != null && $conversation->chatRoom->users->contains($user))){
            $userInConversation = true;
            $usersRelationToUpdate = $conversation->chatRoom->users();
        }

        if($conversation->users->contains($user)){
           $userInConversation = true;
           $usersRelationToUpdate = $conversation->users();
        }

        if(!$userInConversation){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        $usersRelationToUpdate->updateExistingPivot($user, ["archived_at"=>now()]);

        return $this->sendResponse([], "Successfully archived conversation");
    }
    
    public function undoArchiveConversation(Request $request, $id){
        $toValidate = ["conversation_id" => $id];
        $validator = Validator::make($toValidate, [
            "conversation_id" => "exists:conversations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $user = User::find(Auth::user()->id);
        $conversation = Conversation::find($id);

        $userInConversation = false;
        $usersRelationToUpdate = null;
        if(($conversation->chatRoom != null && $conversation->chatRoom->users->contains($user))){
            $userInConversation = true;
            $usersRelationToUpdate = $conversation->chatRoom->users();
        }

        if($conversation->users->contains($user)){
           $userInConversation = true;
           $usersRelationToUpdate = $conversation->users();
        }

        if(!$userInConversation){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        $usersRelationToUpdate->updateExistingPivot($user, ['last_read'=> now(), "cleared_at"=>null, "archived_at"=>null]);

        return $this->sendResponse([], "Successfully retrieved conversation from archive");
    }

    public function blockDirectConversation(Request $request, $id){
        $toValidate = ["conversation_id" => $id];
        $validator = Validator::make($toValidate, [
            "conversation_id" => "exists:conversations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }
        
        $user = User::find(Auth::user()->id);
        $conversation = Conversation::find($id);
        
        if(!$conversation->is_direct){
            return $this->sendError('This is not a direct conversation', [], 400);
        }

        $userInConversation = false;
        $usersRelationToUpdate = null;
        if($conversation->users->contains($user)){
           $userInConversation = true;
           $usersRelationToUpdate = $conversation->users();
        }

        if(!$userInConversation){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        $usersToBlock = $usersRelationToUpdate->where("users.id","!=",$user->id)->get();
        foreach($usersToBlock as $userToBlock){
            $usersRelationToUpdate->updateExistingPivot($userToBlock, ["blocked_by"=>$user->id]);
        }

        return $this->sendResponse([], "Successfully blocked direct conversation");
    }

    public function unblockDirectConversation(Request $request, $id){
        $toValidate = ["conversation_id" => $id];
        $validator = Validator::make($toValidate, [
            "conversation_id" => "exists:conversations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }
        
        $user = User::find(Auth::user()->id);
        $conversation = Conversation::find($id);
        
        if(!$conversation->is_direct){
            return $this->sendError('This is not a direct conversation', [], 400);
        }

        $userInConversation = false;
        $usersRelationToUpdate = null;
        if($conversation->users->contains($user)){
           $userInConversation = true;
           $usersRelationToUpdate = $conversation->users();
        }

        if(!$userInConversation){
            return $this->sendError('Error validation', ["conversation_id" => ["Sorry, you are not part of the conversation"]], 400);
        }

        $usersToUnblock = $usersRelationToUpdate->where("users.id","!=",$user->id)->wherePivot("blocked_by",$user->id)->get();

        if($usersToUnblock->count() == 0){
            return $this->sendError('You have not blocked this conversation', [], 400);
        }

        foreach($usersToUnblock as $userToUnblock){
            $usersRelationToUpdate->updateExistingPivot($userToUnblock, ["blocked_by"=>null]);
        }

        return $this->sendResponse([], "Successfully unblocked direct conversation");
    }
}
