<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\AeronPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    protected $aeronPay;

    public function __construct(AeronPayService $aeronPay)
    {
        $this->aeronPay = $aeronPay;
    }

    public function index(Request $request)
    {
        $merchantId = session('active_merchant_id');
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            $merchant = Merchant::where('status', 'VERIFIED')->first();
            if (!$merchant) {
                $merchant = Merchant::first();
            }
            if ($merchant) {
                session(['active_merchant_id' => $merchant->id]);
            }
        }

        $allMerchants = Merchant::where('status', 'VERIFIED')->get();
        $categoriesResponse = $this->aeronPay->getGiftCategories('giftcard');
        $giftCards = $categoriesResponse['data'] ?? [];

        $transactions = $merchant 
            ? Transaction::where('merchant_id', $merchant->id)->latest()->take(15)->get()
            : collect([]);

        return view('merchant.dashboard', compact('merchant', 'allMerchants', 'giftCards', 'transactions'));
    }

    public function switchMerchant(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id'
        ]);

        session(['active_merchant_id' => $request->merchant_id]);
        return redirect()->route('merchant.dashboard')->with('success', 'Switched Active Reseller Outlet.');
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:10',
            'fname' => 'required|string|max:50',
            'lname' => 'required|string|max:50',
            'email' => 'required|email',
            'mobile' => 'required|string|max:15',
            'giftMessage' => 'nullable|string|max:100',
        ]);

        $merchantId = session('active_merchant_id');
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'No active verified merchant selected. Please select a reseller outlet.'
            ], 400);
        }

        if ($merchant->wallet_balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient Wallet Balance. Current balance: ₹' . number_format($merchant->wallet_balance, 2) . '. Please top-up via Admin Panel.'
            ], 400);
        }

        $clientRefId = 'APAY' . time() . rand(100, 999);

        $purchasePayload = [
            'code'               => $request->code,
            'fname'              => $request->fname,
            'lname'              => $request->lname,
            'client_referenceId' => $clientRefId,
            'amount'             => $request->amount,
            'email'              => $request->email,
            'mobile'             => $request->mobile,
            'giftMessage'        => $request->giftMessage ?? 'Congratulations on your Gift Voucher!',
        ];

        DB::beginTransaction();

        try {
            $apiResponse = $this->aeronPay->giftCardPurchase($purchasePayload);

            $status = (isset($apiResponse['status']) && $apiResponse['status'] === 'SUCCESS') ? 'SUCCESS' : 'FAILED';
            $voucherData = $apiResponse['voucher'] ?? [];

            $transaction = Transaction::create([
                'merchant_id'        => $merchant->id,
                'client_referenceId' => $clientRefId,
                'order_id'           => $apiResponse['order_id'] ?? null,
                'tlid'               => $apiResponse['tlid'] ?? null,
                'code'               => $request->code,
                'provider_name'      => $apiResponse['provider_name'] ?? $apiResponse['biller'] ?? $request->code,
                'amount'             => $request->amount,
                'fname'              => $request->fname,
                'lname'              => $request->lname,
                'email'              => $request->email,
                'mobile'             => $request->mobile,
                'gift_message'       => $purchasePayload['giftMessage'],
                'card_no'            => $voucherData['cardno'] ?? null,
                'pin'                => $voucherData['pin'] ?? null,
                'card_exp'           => $voucherData['cardexp'] ?? null,
                'status'             => $status,
                'message'            => $apiResponse['message'] ?? $apiResponse['txstatus_desc'] ?? 'Order Executed',
            ]);

            if ($status === 'SUCCESS') {
                $merchant->wallet_balance -= $request->amount;
                $merchant->save();
            }

            DB::commit();

            return response()->json([
                'success'       => ($status === 'SUCCESS'),
                'message'       => $transaction->message,
                'transaction'   => $transaction,
                'new_balance'   => $merchant->wallet_balance,
                'api_response'  => $apiResponse,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getReceipt($id)
    {
        $transaction = Transaction::with('merchant')->findOrFail($id);
        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }
}
