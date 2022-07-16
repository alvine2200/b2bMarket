<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductsServices\PurchaseOrder as PurchaseOrderResource;
use App\Http\Resources\Wallet\WalletAccount as WalletAccountResource;
use App\Models\ProductsServices\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
// use App\Resources\Wallet\Wallet as WalletResource;
use App\Models\Wallet\Wallet;
use App\Models\Wallet\WalletAccount;
use Illuminate\Support\Facades\Validator;

class WalletController extends BaseController
{
    //
    public function display()
    {
        $authUser = User::find(Auth::user()->id);
        $displayData = [];

        # income vs expenses
        $boughtOrderQuery = PurchaseOrder::where("user_id", $authUser->id);

        $business = $authUser->business;
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

        $displayData["expenses"] = $expenseData;
        $displayData["income"] = $incomeData;

        # linked accounts
        $walletAccounts = $authUser->wallet->accounts;
        $displayData["walletAccounts"] = WalletAccountResource::collection($walletAccounts);

        $orderQuery = PurchaseOrder::where("user_id", $authUser->id)
            ->orWhereHas("products", function ($query) use ($business) {
                $query->where("business_id", $business->id);
            })
            ->orderByDesc("id");

        # recent transactions
        $recentOrders = $orderQuery
            ->limit(5)
            ->get();
        $displayData["recentOrders"] = PurchaseOrderResource::collection($recentOrders);

        # all transactions
        $allOrders = $orderQuery
            ->limit(5)
            ->get();
        $displayData["allOrders"] = PurchaseOrderResource::collection($allOrders);


        return $this->sendResponse($displayData, "Wallet display retrieved successfully");
    }

    public function balance()
    {
        $authUserId = Auth::user()->id;

        $wallet = Wallet::firstOrCreate(["user_id" => $authUserId]);

        return $this->sendResponse(["balance" => $wallet->balance], "Balance retrieved successfully");
    }

    public function linkAccount(Request $request)
    {
        $input = $request->all();
        $authUserId = Auth::user()->id;

        $validator = Validator::make($input, [
            "name" => "required",
            "account_number" => "unique:wallet_accounts,account_number",
            "balance" => "required|numeric",
            "description" => "required|string",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $wallet = Wallet::firstOrCreate([
            "user_id" => $authUserId,
        ]);

        $input["wallet_id"] = $wallet->id;

        $walletAccount = WalletAccount::create($input);

        $walletAccountBalance = (float) $input["balance"];
        $wallet->balance += $walletAccountBalance;
        $wallet->save();

        return $this->sendResponse(new WalletAccountResource($walletAccount), "Wallet account linked successfully");
    }

    public function listAccounts(Request $request)
    {
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $wallet = $authUser->wallet;
        if ($wallet == null) {
            return $this->sendResponse([], "Wallet accounts listed successfully");
        }

        $walletAccounts = $authUser->wallet->accounts;
        return $this->sendResponse(WalletAccountResource::collection($walletAccounts), "Wallet accounts listed successfully");
    }

    public function retrieveAccount(Request $request, $id)
    {
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $wallet = $authUser->wallet;
        $walletAccount = WalletAccount::where("wallet_id", $wallet->id)->where("id", $id)->first();

        if ($walletAccount == null) {
            return $this->sendError('Wallet account not found.', [], 404);
        }

        return $this->sendResponse(new WalletAccountResource($walletAccount), "Wallet account retrieved successfully");
    }
    
    public function deleteAccount(Request $request, $id)
    {
        $authUserId = Auth::user()->id;
        $authUser = User::find($authUserId);

        $wallet = $authUser->wallet;
        $walletAccount = WalletAccount::where("wallet_id", $wallet->id)->where("id", $id)->first();

        if ($walletAccount == null) {
            return $this->sendError('Wallet account not found.', [], 404);
        }

        $walletAccount->delete();

        return $this->sendResponse([], "Wallet account deleted successfully");
    }

    public function loadAccount(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            "wallet_account_id" => "required|exists:wallet_accounts,id",
            "amount" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $walletAccount = WalletAccount::find($input["wallet_account_id"]);
        $walletAccount->balance += (float)$input["amount"];
        $walletAccount->save();

        $wallet = $walletAccount->wallet;
        $wallet->balance += (float) $input["amount"];
        $wallet->save();

        return $this->sendResponse(new WalletAccountResource($walletAccount), "Wallet account linked successfully");
    }
}
