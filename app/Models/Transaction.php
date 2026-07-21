<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'client_referenceId',
        'order_id',
        'tlid',
        'code',
        'provider_name',
        'amount',
        'fname',
        'lname',
        'email',
        'mobile',
        'gift_message',
        'card_no',
        'pin',
        'card_exp',
        'status',
        'message',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
