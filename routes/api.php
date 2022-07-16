<?php

use App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Oauth2;
use App\Http\Controllers\Wallet;
use App\Http\Controllers\Profile;
use App\Http\Controllers\Profiling;
use App\Http\Controllers\Marketplace;
use App\Http\Controllers\Selectables;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Registration;
use App\Http\Controllers\Notifications;
use App\Http\Controllers\AdvancedFilter;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductsServices;
use App\Http\Controllers\SocialNetworking;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\LiveNewsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserNewsController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\HeaderController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\User_DashboardController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\SocialLinksController;
use App\Http\Controllers\Admin\SupportReplyController;
use App\Http\Controllers\Admin\ContactWidgetController;
use App\Http\Controllers\Admin\NewsInformationController;

// use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name("login");
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});


// Route::prefix('registration')->group(function () {
//     Route::post('/african-business', [Registration\AfricanBusinessRegistration::class, 'register']);
//     Route::post('/non-african-business', [Registration\NonAfricanBusinessRegistration::class, 'register']);
//     Route::post('/investor', [Registration\InvestorRegistration::class, 'register']);
// });
// ]

Route::prefix('registration')->group(function () {
    // business
    Route::post('/1-bs-type-company-info', [Registration\Registration::class, 'businessPartOne']);
    Route::post('/1-investor-bs-type-company-info', [Registration\Registration::class, 'investorPartOne']);
    Route::post('/2-rep-info', [Registration\Registration::class, 'businessPartTwo']);
    Route::post('/3-bs-interests', [Registration\Registration::class, 'businessPartThree']);
    Route::post('/4-investor-profiling', [Registration\Registration::class, 'businessPartFour']);
    Route::post('/5-keywords-tags', [Registration\Registration::class, 'businessPartFive']);
    Route::post('/6-exec-summary', [Registration\Registration::class, 'businessPartSix']);
    Route::post('/7-company-identity', [Registration\Registration::class, 'businessPartSeven']);

    Route::post('/part-one', [Registration\Registration::class, 'oldBusinessPartOne']);
    Route::post('/part-two', [Registration\Registration::class, 'oldBusinessPartTwo']);
    Route::get('retrieve-business/{slug}', [Registration\Registration::class, 'retrieveBusiness']);
    Route::post('/investor-part-one', [Registration\Registration::class, 'oldInvestorPartOne']);
    Route::post('/investor-part-two', [Registration\Registration::class, 'investorPartTwo']);
    /*Route::post('/step-one', [Registration\Registration::class, 'stepOne']);
    Route::post('/step-two', [Registration\Registration::class, 'stepTwo']);
    Route::post('/step-three', [Registration\Registration::class, 'stepThree']);
    Route::post('/step-four', [Registration\Registration::class, 'stepFour']);
    Route::post('/step-five', [Registration\Registration::class, 'stepFive']);
    Route::post('/step-six', [Registration\Registration::class, 'stepSix']);
    Route::post('/non-african-business', [Registration\NonAfricanBusinessRegistration::class, 'register']);
    Route::post('/investor', [Registration\InvestorRegistration::class, 'register']);*/
});

Route::prefix('selectables')->group(function () {
    // Route::resource('/selectable-business-roles', Selectables\SelectableBusinessRoleController::class);
    // Route::post('/bulk-selectable-countries', [Selectables\SelectableCountryController::class, 'bulkStore']);
    // Route::post('/update-bulk-selectable-countries', [Selectables\SelectableCountryController::class, 'bulkUpdate']);
    // Route::resource('/selectable-countries', Selectables\SelectableCountryController::class);
    // Route::resource('/selectable-genders', Selectables\SelectableGenderController::class);
    // Route::resource('/selectable-business-keywords', Selectables\SelectableBusinessKeywordController::class);
    // Route::resource('/selectable-business-interests', Selectables\SelectableBusinessInterestController::class);
    Route::get('/selectable-business-sectors', [Selectables\SelectableController::class, 'businessSectors']);
    Route::get('/selectable-roles', [Selectables\SelectableController::class, 'roles']);
    Route::get('/selectable-countries', [Selectables\SelectableController::class, 'countries']);
    Route::get('/selectable-african-countries', [Selectables\SelectableController::class, 'africanCountries']);
    Route::get('/selectable-nonafrican-countries', [Selectables\SelectableController::class, 'nonAfricanCountries']);
    Route::get('/selectable-services', [Selectables\SelectableController::class, 'services']);
    Route::get('/selectable-products', [Selectables\SelectableController::class, 'products']);
    Route::get('/selectable-platform-needs', [Selectables\SelectableController::class, 'platformNeeds']);
    Route::get('/selectable-partnership-interests', [Selectables\SelectableController::class, 'partnershipInterests']);
    Route::get('/selectable-commercial-interests', [Selectables\SelectableController::class, 'commercialInterests']);
    Route::get('/selectable-distribution-interests', [Selectables\SelectableController::class, 'distributionInterests']);
    Route::get('/selectable-imp-exp-interests', [Selectables\SelectableController::class, 'impExpInterests']);
    Route::get('/selectable-consulting-interests', [Selectables\SelectableController::class, 'consultingInterests']);
    Route::get('/selectable-investing-interests', [Selectables\SelectableController::class, 'investingInterests']);
    Route::get('/selectable-technology-interests', [Selectables\SelectableController::class, 'technologyInterests']);
    Route::get('/selectable-value-chains', [Selectables\SelectableController::class, 'valueChains']);
    Route::get('/selectable-investor-types', [Selectables\SelectableController::class, "investorTypes"]);
    Route::get('/selectable-continents', [Selectables\SelectableController::class, "continents"]);
});

// todo: decide if to keep this
// Route::resource('selectable-business-types', 'SelectableBusinessTypeController');

Route::get('unauthenticated', function () {
    return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
});

Route::get("clear-marketplace-like-dislike", [Marketplace\MarketplaceController::class, "clearLikeDislike"]);

Route::middleware('auth:api')->group(function () {
    Route::prefix("profiling")->group(function () {
        Route::post("/set-headquarters", [Profiling\ProfilingController::class, "setHeadquarters"]);
        Route::post("/set-countries-where-active", [Profiling\ProfilingController::class, "setCountriesWhereActive"]);
        Route::post("/set-main-sector", [Profiling\ProfilingController::class, "setMainSector"]);
        Route::post("/set-other-sectors", [Profiling\ProfilingController::class, "setOtherSectors"]);
        Route::post("/set-incorporation-number", [Profiling\ProfilingController::class, "setIncorporationNumber"]);
        Route::post("/upload-company-logo", [Profiling\ProfilingController::class, "uploadCompanyLogo"]);
        Route::post("/upload-company-banner", [Profiling\ProfilingController::class, "uploadCompanyBanner"]);
        Route::post("/upload-certificate-of-incorporation", [Profiling\ProfilingController::class, "uploadCertificateOfIncorporation"]);
        Route::post("/set-executive-summary", [Profiling\ProfilingController::class, "setExecutiveSummary"]);
        Route::post("/set-business-interests", [Profiling\ProfilingController::class, "setBusinessInterests"]);
        Route::post("/set-business-keywords", [Profiling\ProfilingController::class, "setBusinessKeywords"]);
    });

    Route::prefix("marketplace")->group(function () {
        Route::get("businesses", [Marketplace\MarketplaceController::class, "businesses"]);
        Route::get("all-businesses", [Marketplace\MarketplaceController::class, "allBusinesses"]);
        Route::get("like-business/{slug}", [Marketplace\MarketplaceController::class, "likeBusiness"]);
        Route::get("dislike-business/{slug}", [Marketplace\MarketplaceController::class, "dislikeBusiness"]);
        Route::get("undo-like-dislike-business/{slug}", [Marketplace\MarketplaceController::class, "undoLikeDislikeBusiness"]);
        Route::get("save-business/{slug}", [Marketplace\MarketplaceController::class, "saveBusiness"]);
        Route::get("unsave-business/{slug}", [Marketplace\MarketplaceController::class, "unsaveBusiness"]);
        Route::get("follow-business/{slug}", [Marketplace\MarketplaceController::class, "followBusiness"]);
        Route::get("unfollow-business/{slug}", [Marketplace\MarketplaceController::class, "unfollowBusiness"]);
        Route::get("connected-businesses", [Marketplace\MarketplaceController::class, "connectedBusinesses"]);
    });

    Route::prefix("social-networking")->group(function () {
        Route::get("/clear-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "clearConversation"]);
        Route::get("/delete-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "deleteConversation"]);
        Route::get("/archive-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "archiveConversation"]);
        Route::get("/undo-archive-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "undoArchiveConversation"]);
        Route::get("/block-direct-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "blockDirectConversation"]);
        Route::get("/unblock-direct-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "unblockDirectConversation"]);
        // Route::get("/block-chat-user/{id}", [SocialNetworking\SocialNetworkingController::class, "blockChatUser"]);
        // Route::get("/chat-room-conversation/{id}/block-user/{user_id}", [SocialNetworking\SocialNetworkingController::class, "blockDirectConversation"]);

        Route::prefix("/chat-messages")->group(function () {
            Route::post("/to-recipient", [SocialNetworking\SocialNetworkingController::class, "sendChatMessageToRecipient"]);
            Route::post("/to-chat-room", [SocialNetworking\SocialNetworkingController::class, "sendChatMessageToChatRoom"]);
            Route::post("/to-conversation", [SocialNetworking\SocialNetworkingController::class, "sendChatMessageToConversation"]);
            Route::get("/conversations", [SocialNetworking\SocialNetworkingController::class, "listUserConversations"]);
            Route::get("/archived-conversations", [SocialNetworking\SocialNetworkingController::class, "listArchivedUserConversations"]);
            Route::get("/read-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "readConversation"]);
            Route::get("/read-archived-conversation/{id}", [SocialNetworking\SocialNetworkingController::class, "readArchivedConversation"]);
            Route::get("/read-chat-room-msgs/{id}", [SocialNetworking\SocialNetworkingController::class, "readChatRoomMsgs"]);
            Route::get("/read-archived-chat-room-msgs/{id}", [SocialNetworking\SocialNetworkingController::class, "readArchivedChatRoomMsgs"]);
            Route::get("/read-direct-msgs/{id}", [SocialNetworking\SocialNetworkingController::class, "readDirectMsgs"]);
            Route::get("/read-archived-direct-msgs/{id}", [SocialNetworking\SocialNetworkingController::class, "readArchivedDirectMsgs"]);
        });

        Route::prefix("chat-rooms")->group(function () {
            Route::get("/list", [SocialNetworking\SocialNetworkingController::class, "listChatRooms"]);
            Route::get("/{id}/list-users", [SocialNetworking\SocialNetworkingController::class, "listChatRoomUsers"]);
            Route::post("/create", [SocialNetworking\SocialNetworkingController::class, "createChatRoom"]);
            Route::post("/add-user", [SocialNetworking\SocialNetworkingController::class, "addUserToChatRoom"]);
            Route::post("/remove-user", [SocialNetworking\SocialNetworkingController::class, "removeUserToChatRoom"]);
            Route::get("/join-by-link/{link}", [SocialNetworking\SocialNetworkingController::class, "joinChatRoomByLink"]);
            Route::post("/approve-user", [SocialNetworking\SocialNetworkingController::class, "approveChatRoomUser"]);
            Route::post("/promote-user", [SocialNetworking\SocialNetworkingController::class, "promoteChatRoomUser"]);
            Route::post("/demote-user", [SocialNetworking\SocialNetworkingController::class, "demoteChatRoomUser"]);
        });

        Route::prefix("business-reviews")->group(function () {
            Route::get("", [SocialNetworking\SocialNetworkingController::class, "listAllBusinessReviews"]);
            Route::get("/{id}", [SocialNetworking\SocialNetworkingController::class, "listBusinessReviews"]);
            Route::post("create", [SocialNetworking\SocialNetworkingController::class, "reviewBusiness"]);
        });

        Route::get("user-reviews", [SocialNetworking\SocialNetworkingController::class, "listUserBusinessReviews"]);

    });

    Route::prefix("notifications")->group(function(){
        Route::get("", [Notifications\NotificationController::class, "getNotifications"]);
        Route::get("read/{id}", [Notifications\NotificationController::class, "readSingleNotification"]);
        Route::get("read-all", [Notifications\NotificationController::class, "readAllNotifications"]);
    });

    Route::resource('users', Controllers\UserController::class);

    Route::prefix("advanced-filter")->group(function () {
        Route::get("businesses", [AdvancedFilter\AdvancedFilterController::class, "businesses"]);
        Route::get("recent", [AdvancedFilter\AdvancedFilterController::class, "recent"]);
    });

    //products crud uncomment and connect to specific routes
    // Route::resource('products', ProductsController::class);

    Route::prefix("products")->group(function () {
        Route::get("", [ProductsServices\ProductsServicesController::class, "listProducts"]);
        Route::get("/other-business/{slug}", [ProductsServices\ProductsServicesController::class, "listOtherBusinessProducts"]);
        Route::post("", [ProductsServices\ProductsServicesController::class, "createProduct"]);
        Route::get("/save/{id}", [ProductsServices\ProductsServicesController::class, "saveProduct"]);
        Route::get("/undo-save/{id}", [ProductsServices\ProductsServicesController::class, "unsaveProduct"]);
        Route::get("/saved-products", [ProductsServices\ProductsServicesController::class, "listSavedProducts"]);
    });

    Route::prefix("cart")->group(function () {
        Route::get("", [ProductsServices\CartController::class, "displayCart"]);
        Route::post("add-product", [ProductsServices\CartController::class, "addProduct"]);
        Route::post("update-product-qty", [ProductsServices\CartController::class, "updateProductQty"]);
        Route::get("remove-product/{id}", [ProductsServices\CartController::class, "removeProduct"]);
        Route::get("clear-cart", [ProductsServices\CartController::class, "clearCart"]);
        Route::get("proceed-to-shipping", [ProductsServices\CartController::class, "proceedToShipping"]);
    });

    Route::prefix("order")->group(function () {
        Route::post("shipping-information", [ProductsServices\OrderController::class, "addShippingInformation"]);
        Route::get("shipping-information", [ProductsServices\OrderController::class, "listShippingInformation"]);
        Route::get("set-shipping-information/{id}", [ProductsServices\OrderController::class, "setShippingInformation"]);
        Route::get("set-payment-mode", [ProductsServices\OrderController::class, "setPaymentMode"]);
        Route::get("set-delivery-mode", [ProductsServices\OrderController::class, "setDeliveryMode"]);
        Route::get("complete", [ProductsServices\OrderController::class, "completeOrder"]);
        Route::get("", [ProductsServices\OrderController::class, "getOrder"]);
        Route::post("pay/{id}", [ProductsServices\OrderController::class, "payOrder"]);
        Route::get("/bought", [ProductsServices\OrderController::class, "boughtOrders"]);
        Route::get("/sold", [ProductsServices\OrderController::class, "soldOrders"]);
    });

    Route::prefix("payments")->group(function () {
        Route::get("sent", [ProductsServices\PaymentController::class, "sentPayments"]);
        // Route::get("received", [ProductsServices\PaymentController::class, "listReceivedPayments"]);
    });

    // Route::resource('products', ProductsController::class);


    Route::prefix("profile")->group(function () {
        Route::get("", [Profile\ProfileController::class, "userProfile"]);
        Route::get("other-profile/{slug}", [Profile\ProfileController::class, "otherProfile"]);
        Route::post("update", [Profile\ProfileController::class, "updateProfile"]);
        Route::post("team-members", [Profile\ProfileController::class, "newTeamMember"]);
        Route::get("team-members", [Profile\ProfileController::class, "teamMembers"]);
        Route::get("{slug}/team-members", [Profile\ProfileController::class, "otherTeamMembers"]);
        Route::delete("team-members/{slug}", [Profile\ProfileController::class, "deleteTeamMember"]);
        Route::post("update-executive-summary", [Profile\ProfileController::class, "updateExecutiveSummary"]);
        Route::get("clients", [Profile\ProfileController::class, "getClients"]);
        Route::get("{slug}/clients", [Profile\ProfileController::class, "getOtherClients"]);
        Route::post("clients", [Profile\ProfileController::class, "addClient"]);
        Route::delete("clients/{id}", [Profile\ProfileController::class, "deleteClient"]);
        Route::get("call-to-actions", [Profile\ProfileController::class, "getCallToActions"]);
        Route::get("{slug}/call-to-actions", [Profile\ProfileController::class, "getOtherCallToActions"]);
        Route::post("call-to-actions", [Profile\ProfileController::class, "addCallToAction"]);
        Route::delete("call-to-actions/{id}", [Profile\ProfileController::class, "deleteCallToAction"]);
        Route::get("call-to-actions/{id}/request-for-quote", [Profile\ProfileController::class, "requestForQuote"]);
        Route::get("call-to-actions/{id}/schedule-call", [Profile\ProfileController::class, "scheduleCall"]);
        // Route::get("recent", [AdvancedFilter\AdvancedFilterController::class, "recent"]);
    });

    Route::prefix("wallet")->group(function () {
        Route::get("display", [Wallet\WalletController::class, "display"]);
        Route::get("balance", [Wallet\WalletController::class, "balance"]);
        Route::post("link-account", [Wallet\WalletController::class, "linkAccount"]);
        Route::get("accounts", [Wallet\WalletController::class, "listAccounts"]);
        Route::get("accounts/{id}", [Wallet\WalletController::class, "retrieveAccount"]);
        Route::delete("accounts/{id}", [Wallet\WalletController::class, "deleteAccount"]);
        Route::post("load-account", [Wallet\WalletController::class, "loadAccount"]);
    });






    //Admin-gets all users
    Route::controller(UsersController::class)->group(function () {
        Route::get('all_users', 'get_users');
        Route::get('all_investors', 'get_investors');
        Route::get('all_african_business', 'get_african_business');
        Route::get('all_non_african_business', 'get_non_african_business');
    });
    //user support
    Route::controller(SupportController::class)->group(function(){
        Route::post('/create_ticket','create_ticket');
        Route::get('user_tickets/{id}','user_tickets');
        Route::get('/show_ticket/{id}','show_ticket');
        Route::post('/update_ticket/{id}','update_ticket');
        Route::delete('/cancel_ticket/{id}','cancel_ticket');
        Route::put('/user_resolve_ticket/{id}','resolve_ticket');
    });

    //usernews
    Route::controller(UserNewsController::class)->group(function () {
        Route::post('create_news', 'store');
        Route::get('list_news', 'index');
        Route::post('update_news/{id}', 'update');
        Route::delete('clear_news/{id}', 'destroy');
    });

    Route::get('user_dashboard', [User_DashboardController::class,'dashboard']); //return user dashboard home page

    Route::get("current-business", [Marketplace\MarketplaceController::class, "currentBusiness"]);
});


  //Admin middleware starts here
Route::middleware(['auth:api', AdminMiddleware::class])->group(function () {

  Route::controller(FooterController::class)->group(function (){
    Route::post('post_footers','add_footers');
    Route::get('show_footers','show_footers');
    Route::get('edit_footers/{id}','edit_footers');
    Route::put('update_footers/{id}','update_footers');
    Route::delete('delete_footers/{id}','delete_footers');
  });

  Route::controller(PagesController::class)->group(function (){
        Route::post('add_page', 'add_pages');
        Route::get('show_page','show_pages');
        Route::get('edit_page/{id}','edit_pages');
        Route::put('update_page/{id}','update_pages');
        Route::put('publish_page/{id}','publish_pages');
        Route::delete('delete_page/{id}','delete_pages');
        Route::put('unpublish_page/{id}','unpublish_pages');
    });

    Route::controller(SocialLinksController::class)->group(function (){

        Route::post('add_social','add_social_links');
        Route::get('show_social','show_social_links');
        Route::get('edit_social/{id}','edit_social_links');
        Route::put('update_social/{id}','update_social_links');
        Route::delete('delete_social/{id}','delete_social_links');

    });

    Route::controller(ContactWidgetController::class)->group(function (){
        Route::post('add_contacts_widget','post_contact');
        Route::get('show_contacts_widget','show_contact');
        Route::get('edit_contacts_widget/{id}','edit_contact');
        Route::put('update_contacts_widget/{id}','update_contact');
        Route::delete('delete_contacts_widget/{id}','delete_contact');
    });

    Route::controller(AboutController::class)->group(function (){
        Route::post('post_about','add');
        Route::get('show_about','show');
        Route::get('edit_about/{id}','edit');
        Route::put('update_about/{id}','update');
        Route::delete('delete_about/{id}','delete');
    });

    Route::controller(SupportReplyController::class)->group(function () {
        Route::get('all_tickets','get_all_tickets');
        Route::post('reply_ticket/{id}','reply_to_ticket');
        Route::put('resolve_ticket/{id}','resolve_ticket');

     });

    //admin news and information
    Route::controller(NewsInformationController::class)->group(function (){
        Route::get('all_news','get_all_news');
        Route::get('user_news/{id}','show_user_news');
        Route::delete('delete_news/{id}','delete_news');
        Route::put('approve_news/{id}','approve_news');
        Route::post('post_news','post_news');
        Route::get('display_news','display_news');
    });
    Route::controller(SettingsController::class)->group(function (){
        Route::post('general_settings','settings');
        Route::get('show_settings','show_settings');
    });

    Route::controller(SystemController::class)->group(function (){
        Route::get('system','display_system');
        Route::post('system_details','change_system_details');

    });


      Route::controller(HeaderController::class)->group(function (){
        Route::post('post_headers','add_headers');
        Route::get('show_headers','show_headers');
        Route::get('edit_headers/{id}','edit_headers');
        Route::put('update_headers/{id}','update_headers');
        Route::delete('delete_headers/{id}','delete_headers');
    });

        Route::controller(PageContentController::class)->group(function (){
            Route::post('add_content','add');
            Route::post('save_draft','save');
            Route::get('show_content','show');
            Route::get('show_draft','show_draft');
            Route::get('edit_content/{id}','edit');
            Route::delete('delete_content/{id}','delete');
            Route::put('update_content/{id}','update');

    });

});







//for getting world news one doesn't need to be authenticated
Route::get('live_news', [LiveNewsController::class, 'live_news']);
Route::get('trending_news', [LiveNewsController::class, 'trendingNews']);
Route::post('topics', [LiveNewsController::class, 'topics']);


Route::post('place_order', [OrderController::class, 'place_order']);
//for admin alone, can view all orders
Route::get('view_order', [OrderController::class, 'view_order']);
//person's orders
Route::get('view_complete_orders', [OrderController::class, 'view_my_orders']);

Route::get('generate_invoice', [OrderController::class, 'generate_invoice']);


//paypal routes

Route::get('createpaypal', [PayPalController::class, 'createpaypal'])->name('createpaypal');

Route::get('processPaypal', [PayPalController::class, 'processPaypal'])->name('processPaypal');

Route::get('processSuccess', [PayPalController::class, 'processSuccess'])->name('processSuccess');

Route::get('processCancel', [PayPalController::class, 'processCancel'])->name('processCancel');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
