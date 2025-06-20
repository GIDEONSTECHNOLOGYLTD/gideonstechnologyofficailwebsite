<?php

namespace App\Models;

use App\Core\Model;

class ReferralReward extends Model
{
    protected $table = 'referral_rewards';
    
    protected $fillable = [
        'user_id', 
        'referred_user_id',
        'action_type',
        'reward_amount',
        'status'
    ];
    
    /**
     * Get the user that earned this reward
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the referred user who triggered this reward
     */
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
