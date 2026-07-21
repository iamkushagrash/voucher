<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\AeronPayService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $aeronPay;

    public function __construct(AeronPayService $aeronPay)
    {
        $this->aeronPay = $aeronPay;
    }

    public function index()
    {
        $totalMerchants = Merchant::count();
        $verifiedMerchants = Merchant::where('status', 'VERIFIED')->count();
        $totalWalletBalance = Merchant::sum('wallet_balance');
        $totalTransactions = Transaction::where('status', 'SUCCESS')->count();
        $totalVoucherVolume = Transaction::where('status', 'SUCCESS')->sum('amount');

        $merchants = Merchant::latest()->get();
        $transactions = Transaction::with('merchant')->latest()->take(10)->get();

        $settings = [
            'aeronpay_mode' => Setting::get('aeronpay_mode', 'mock'),
            'aeronpay_client_id' => Setting::get('aeronpay_client_id', ''),
            'aeronpay_client_secret' => Setting::get('aeronpay_client_secret', ''),
            'aeronpay_base_url' => Setting::get('aeronpay_base_url', 'https://api.aeronpay.in/api/serviceapi-prod'),
        ];

        return view('admin.dashboard', compact(
            'totalMerchants',
            'verifiedMerchants',
            'totalWalletBalance',
            'totalTransactions',
            'totalVoucherVolume',
            'merchants',
            'transactions',
            'settings'
        ));
    }

    public function onboardMerchant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'aadhaar_number' => 'required|string|max:12',
            'pan' => 'required|string|max:10',
            'bank_account' => 'required|string|max:30',
            'ifsc' => 'required|string|max:15',
        ]);

        $clientRefId = 'APAY' . time() . rand(100, 999);

        $payload = [
            'client_referenceId' => $clientRefId,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'aadhaar_number' => $request->aadhaar_number,
            'pan' => strtoupper($request->pan),
            'bank_account' => $request->bank_account,
            'ifsc' => strtoupper($request->ifsc),
            'latitude' => $request->latitude ?? '28.6139',
            'longitude' => $request->longitude ?? '77.2090',
            'consent' => 'Y'
        ];

        $response = $this->aeronPay->merchantSignup($payload);

        if (isset($response['status']) && ($response['status'] === 'SUCCESS' || $response['status'] === true || isset($response['refid']))) {
            $merchant = Merchant::create([
                'client_referenceId' => $clientRefId,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'aadhaar_number' => $request->aadhaar_number,
                'pan' => strtoupper($request->pan),
                'bank_account' => $request->bank_account,
                'ifsc' => strtoupper($request->ifsc),
                'latitude' => $payload['latitude'],
                'longitude' => $payload['longitude'],
                'status' => 'PENDING_OTP',
                'refid' => $response['refid'] ?? ('REF' . rand(100000, 999999)),
                'hash' => $response['hash'] ?? md5($clientRefId . time()),
                'wallet_balance' => 0.00,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'e-KYC Initialized. OTP sent to merchant mobile.',
                'merchant' => $merchant,
                'refid' => $merchant->refid,
                'hash' => $merchant->hash,
                'client_referenceId' => $merchant->client_referenceId,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Merchant Signup Failed from AeronPay API.',
            'response' => $response
        ], 400);
    }

    public function validateMerchantOtp(Request $request)
    {
        $request->validate([
            'client_referenceId' => 'required|string',
            'otp' => 'required|string',
            'refid' => 'required|string',
            'hash' => 'required|string',
        ]);

        $merchant = Merchant::where('client_referenceId', $request->client_referenceId)->firstOrFail();

        $response = $this->aeronPay->merchantSignupValidate([
            'client_referenceId' => $request->client_referenceId,
            'otp' => $request->otp,
            'refid' => $request->refid,
            'hash' => $request->hash,
        ]);

        if (isset($response['status']) && ($response['status'] === 'SUCCESS' || ($response['statusCode'] ?? null) == 200)) {
            $merchant->update([
                'status' => 'VERIFIED',
                'wallet_balance' => 1000.00
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Merchant e-KYC Verified successfully! ₹1,000 wallet balance credited.',
                'merchant' => $merchant
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'OTP Validation Failed.',
            'response' => $response
        ], 400);
    }

    public function addWalletBalance(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'amount' => 'required|numeric|min:100',
        ]);

        $merchant = Merchant::findOrFail($request->merchant_id);
        $merchant->wallet_balance += $request->amount;
        $merchant->save();

        return response()->json([
            'success' => true,
            'message' => "Successfully credited ₹" . number_format($request->amount, 2) . " to {$merchant->name}'s wallet.",
            'new_balance' => $merchant->wallet_balance
        ]);
    }

    public function updateSettings(Request $request)
    {
        Setting::set('aeronpay_mode', $request->aeronpay_mode ?? 'mock');
        Setting::set('aeronpay_client_id', $request->aeronpay_client_id ?? '');
        Setting::set('aeronpay_client_secret', $request->aeronpay_client_secret ?? '');
        Setting::set('aeronpay_base_url', $request->aeronpay_base_url ?? 'https://api.aeronpay.in/api/serviceapi-prod');

        return response()->json([
            'success' => true,
            'message' => 'AeronPay Configuration updated successfully!'
        ]);
    }
}
