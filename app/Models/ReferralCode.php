<?php

namespace App\Models;

use App\Core\Model;

class ReferralCode extends Model
{
    protected $table = 'referral_codes';
    
    protected $fillable = [
        'user_id', 
        'code', 
        'expires_at',
        'uses',
        'max_uses'
    ];
    
    /**
     * Get the user that owns the referral code
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the referred users who used this code
     */
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by', 'user_id');
    }
}
