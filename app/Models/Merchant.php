<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_referenceId',
        'name',
        'mobile',
        'email',
        'aadhaar_number',
        'pan',
        'bank_account',
        'ifsc',
        'latitude',
        'longitude',
        'consent',
        'status',
        'refid',
        'hash',
        'wallet_balance',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
