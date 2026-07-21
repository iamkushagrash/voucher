<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Initial Settings
        Setting::set('aeronpay_mode', 'mock');
        Setting::set('aeronpay_base_url', 'https://api.aeronpay.in/api/serviceapi-prod');
        Setting::set('aeronpay_client_id', '');
        Setting::set('aeronpay_client_secret', '');

        // 2. Initial Sample Verified Merchants
        $m1 = Merchant::create([
            'client_referenceId' => 'APAY1713790318782',
            'name'               => 'Apex Digital Outlet (Delhi)',
            'mobile'             => '9876543210',
            'email'              => 'apex.digital@aeronreseller.in',
            'aadhaar_number'     => '548912348901',
            'pan'                => 'ABCDE1234F',
            'bank_account'       => '91802004512398',
            'ifsc'               => 'HDFC0000128',
            'latitude'           => '28.6139',
            'longitude'          => '77.2090',
            'status'             => 'VERIFIED',
            'refid'              => 'REF847291',
            'hash'               => md5('APAY1713790318782'),
            'wallet_balance'     => 15000.00,
        ]);

        $m2 = Merchant::create([
            'client_referenceId' => 'APAY1713790319999',
            'name'               => 'Royal Vouchers & Forex (Mumbai)',
            'mobile'             => '9988776655',
            'email'              => 'royal.vouchers@aeronreseller.in',
            'aadhaar_number'     => '987654321098',
            'pan'                => 'XYZPQ9876M',
            'bank_account'       => '001201509823',
            'ifsc'               => 'ICIC0000012',
            'latitude'           => '19.0760',
            'longitude'          => '72.8777',
            'status'             => 'VERIFIED',
            'refid'              => 'REF392019',
            'hash'               => md5('APAY1713790319999'),
            'wallet_balance'     => 8500.00,
        ]);

        // 3. Initial Sample Voucher Transactions
        Transaction::create([
            'merchant_id'        => $m1->id,
            'client_referenceId' => 'APAY17371911838139',
            'order_id'           => 'ANBLU14772648267058',
            'tlid'               => 'APAY17371911838139',
            'code'               => 'AWQ',
            'provider_name'      => 'Flipkart E-Gift Card',
            'amount'             => 2500.00,
            'fname'              => 'Rakesh',
            'lname'              => 'Mittal',
            'email'              => 'rakesh.mittal@outlook.in',
            'mobile'             => '9999988888',
            'gift_message'       => 'Best wishes on your anniversary!',
            'card_no'            => '600373667274627461',
            'pin'                => '388462',
            'card_exp'           => '2027-01-18',
            'status'             => 'SUCCESS',
            'message'            => 'Gift Card Order Successful',
        ]);

        Transaction::create([
            'merchant_id'        => $m2->id,
            'client_referenceId' => 'APAY17371911839999',
            'order_id'           => 'ANBLU14772648269999',
            'tlid'               => 'APAY17371911839999',
            'code'               => 'AMZ',
            'provider_name'      => 'Amazon Pay E-Gift Card',
            'amount'             => 1000.00,
            'fname'              => 'Priya',
            'lname'              => 'Sharma',
            'email'              => 'priya.sharma@gmail.com',
            'mobile'             => '9811122233',
            'gift_message'       => 'Happy Birthday!',
            'card_no'            => '601492019283741234',
            'pin'                => '819203',
            'card_exp'           => '2027-04-10',
            'status'             => 'SUCCESS',
            'message'            => 'Gift Card Order Successful',
        ]);
    }
}
