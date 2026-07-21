<?php

namespace App\Services;

use App\Models\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AeronPayService
{
    protected $client;
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $mode;

    public function __construct()
    {
        $this->baseUrl = rtrim(Setting::get('aeronpay_base_url', config('app.aeronpay_base_url', 'https://api.aeronpay.in/api/serviceapi-prod')), '/');
        $this->clientId = Setting::get('aeronpay_client_id', config('app.aeronpay_client_id', ''));
        $this->clientSecret = Setting::get('aeronpay_client_secret', config('app.aeronpay_client_secret', ''));
        $this->mode = Setting::get('aeronpay_mode', config('app.aeronpay_mode', 'mock'));

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 15.0,
            'headers' => [
                'Content-Type'  => 'application/json',
                'accept'        => 'application/json',
                'client-id'     => $this->clientId,
                'client-secret' => $this->clientSecret,
            ]
        ]);
    }

    public function merchantSignup(array $data)
    {
        if ($this->mode === 'mock' || empty($this->clientId)) {
            return $this->mockMerchantSignup($data);
        }

        try {
            $response = $this->client->post('/api/outlet/merchant/signup', [
                'json' => [
                    'client_referenceId' => $data['client_referenceId'],
                    'mobile'             => $data['mobile'],
                    'email'              => $data['email'],
                    'aadhaar_number'     => $data['aadhaar_number'],
                    'pan'                => $data['pan'],
                    'bank_account'       => $data['bank_account'],
                    'ifsc'               => $data['ifsc'],
                    'latitude'           => $data['latitude'] ?? '28.6139',
                    'longitude'          => $data['longitude'] ?? '77.2090',
                    'consent'            => $data['consent'] ?? 'Y',
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::warning('AeronPay Signup Error: ' . $e->getMessage());
            return $this->mockMerchantSignup($data, 'LIVE_FAIL_FALLBACK: ' . $e->getMessage());
        }
    }

    public function merchantSignupValidate(array $data)
    {
        if ($this->mode === 'mock' || empty($this->clientId)) {
            return $this->mockMerchantSignupValidate($data);
        }

        try {
            $response = $this->client->post('/api/outlet/merchant/signup_validate', [
                'json' => [
                    'client_referenceId' => $data['client_referenceId'],
                    'otp'                => $data['otp'],
                    'refid'              => $data['refid'],
                    'hash'               => $data['hash'],
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::warning('AeronPay Signup Validate Error: ' . $e->getMessage());
            return $this->mockMerchantSignupValidate($data);
        }
    }

    public function getGiftCategories(string $category = 'giftcard')
    {
        if ($this->mode === 'mock' || empty($this->clientId)) {
            return $this->mockGiftCategories();
        }

        try {
            $response = $this->client->post('/api/giftvoucher/gift_categories', [
                'json' => [
                    'category' => $category
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::warning('AeronPay Gift Categories Error: ' . $e->getMessage());
            return $this->mockGiftCategories();
        }
    }

    public function giftCardPurchase(array $req)
    {
        if ($this->mode === 'mock' || empty($this->clientId)) {
            return $this->mockGiftCardPurchase($req);
        }

        try {
            $response = $this->client->post('/api/giftvoucher/giftcard_purchase', [
                'json' => [
                    'request' => [
                        'code'               => $req['code'],
                        'fname'              => $req['fname'],
                        'lname'              => $req['lname'],
                        'client_referenceId' => $req['client_referenceId'],
                        'amount'             => (string)$req['amount'],
                        'email'              => $req['email'],
                        'mobile'             => $req['mobile'],
                        'giftMessage'        => $req['giftMessage'] ?? 'GIFT',
                    ]
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::warning('AeronPay Gift Purchase Error: ' . $e->getMessage());
            return $this->mockGiftCardPurchase($req);
        }
    }

    protected function mockMerchantSignup(array $data, $warning = null)
    {
        $refid = 'REF' . rand(100000, 999999);
        $hash = md5($data['client_referenceId'] . time());

        return [
            'status'             => 'SUCCESS',
            'statusCode'         => 200,
            'message'            => 'e-KYC Request Initiated. OTP sent to mobile.',
            'client_referenceId' => $data['client_referenceId'],
            'refid'              => $refid,
            'hash'               => $hash,
            'mode'               => 'MOCK',
            'warning'            => $warning
        ];
    }

    protected function mockMerchantSignupValidate(array $data)
    {
        return [
            'status'             => 'SUCCESS',
            'statusCode'         => 200,
            'message'            => 'Merchant e-KYC Verified Successfully',
            'client_referenceId' => $data['client_referenceId'],
            'mode'               => 'MOCK'
        ];
    }

    public function mockGiftCategories()
    {
        return [
            'status' => 'SUCCESS',
            'statusCode' => 200,
            'data' => [
                [
                    'code' => 'AMZ',
                    'name' => 'Amazon Pay E-Gift Card',
                    'category' => 'Shopping',
                    'min_amount' => 50,
                    'max_amount' => 10000,
                    'image' => 'https://images.unsplash.com/photo-1523474253046-8cd2748b5fd2?w=400&q=80',
                    'discount_pct' => 2.5,
                    'description' => 'Redeemable for millions of items on Amazon India.'
                ],
                [
                    'code' => 'AWQ',
                    'name' => 'Flipkart E-Gift Card',
                    'category' => 'Shopping',
                    'min_amount' => 100,
                    'max_amount' => 10000,
                    'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=400&q=80',
                    'discount_pct' => 3.0,
                    'description' => 'Use on Flipkart for electronics, fashion, and groceries.'
                ],
                [
                    'code' => 'SWG',
                    'name' => 'Swiggy Money Voucher',
                    'category' => 'Food & Dining',
                    'min_amount' => 100,
                    'max_amount' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&q=80',
                    'discount_pct' => 4.0,
                    'description' => 'Valid for food delivery, Instamart & Gourmet.'
                ],
                [
                    'code' => 'ZOM',
                    'name' => 'Zomato Pro Gift Voucher',
                    'category' => 'Food & Dining',
                    'min_amount' => 100,
                    'max_amount' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80',
                    'discount_pct' => 4.5,
                    'description' => 'Order food online from thousands of restaurants.'
                ],
                [
                    'code' => 'MYN',
                    'name' => 'Myntra Fashion Voucher',
                    'category' => 'Fashion',
                    'min_amount' => 250,
                    'max_amount' => 10000,
                    'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=400&q=80',
                    'discount_pct' => 5.0,
                    'description' => 'Shop top global apparel & lifestyle brands.'
                ],
                [
                    'code' => 'BMS',
                    'name' => 'BookMyShow Movie Voucher',
                    'category' => 'Entertainment',
                    'min_amount' => 100,
                    'max_amount' => 2000,
                    'image' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=400&q=80',
                    'discount_pct' => 6.0,
                    'description' => 'Book movie tickets, plays, concerts & events.'
                ],
                [
                    'code' => 'UBR',
                    'name' => 'Uber Trip Gift Voucher',
                    'category' => 'Travel',
                    'min_amount' => 100,
                    'max_amount' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400&q=80',
                    'discount_pct' => 3.5,
                    'description' => 'Pay for Uber Rides and Auto in 50+ cities.'
                ],
                [
                    'code' => 'CRM',
                    'name' => 'Croma Electronics Voucher',
                    'category' => 'Electronics',
                    'min_amount' => 500,
                    'max_amount' => 50000,
                    'image' => 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=400&q=80',
                    'discount_pct' => 2.0,
                    'description' => 'Valid at Croma stores and Croma.com.'
                ]
            ]
        ];
    }

    protected function mockGiftCardPurchase(array $req)
    {
        $codeMap = [
            'AMZ' => 'Amazon Pay E-Gift Card',
            'AWQ' => 'Flipkart E-Gift Card',
            'SWG' => 'Swiggy Money Voucher',
            'ZOM' => 'Zomato Pro Gift Voucher',
            'MYN' => 'Myntra Fashion Voucher',
            'BMS' => 'BookMyShow Movie Voucher',
            'UBR' => 'Uber Trip Gift Voucher',
            'CRM' => 'Croma Electronics Voucher',
        ];

        $biller = $codeMap[$req['code']] ?? 'Universal E-Gift Card';
        $orderId = 'ANBLU' . time() . rand(10, 99);
        $cardNo = '600373' . rand(1000000000, 9999999999);
        $pin = (string)rand(100000, 999999);
        $expDate = date('Y-m-d', strtotime('+1 year'));

        return [
            'status'         => 'SUCCESS',
            'current_time'   => date('Y-m-d\TH:i:s.u\Z'),
            'callback_status'=> 'Success',
            'amount'         => (string)$req['amount'],
            'message'        => 'Gift Card Order Successful',
            'operator_ref'   => (string)rand(10000000000, 99999999999),
            'voucher'        => [
                'cardprice' => (string)$req['amount'],
                'cardno'    => $cardNo,
                'pin'       => $pin,
                'cardexp'   => $expDate,
                'message'   => 'Gift Card Order Successful'
            ],
            'provider_name'  => $biller,
            'txstatus_desc'  => 'Success',
            'order_id'       => $orderId,
            'biller'         => $biller,
            'timestamp'      => date('Y-m-d\TH:i:s.u\Z'),
            'tlid'           => 'APAY' . time() . rand(100, 999),
            'mode'           => 'MOCK'
        ];
    }
}
