<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralCode;
use App\Models\ReferralReward;

class ReferralService extends BaseService
{
    /**
     * Generate a unique referral code for a user
     * 
     * @param User $user
     * @return string
     */
    public function generateReferralCode(User $user)
    {
        $baseCode = strtoupper(substr($user->first_name, 0, 2) . substr($user->last_name, 0, 2));
        $uniqueCode = $baseCode . strtoupper(substr(md5($user->id . time()), 0, 6));
        
        $referralCode = new ReferralCode();
        $referralCode->user_id = $user->id;
        $referralCode->code = $uniqueCode;
        $referralCode->save();
        
        return $uniqueCode;
    }
    
    /**
     * Get user's referral code, create if not exists
     * 
     * @param int $userId
     * @return string
     */
    public function getUserReferralCode($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return null;
        }
        
        $referralCode = ReferralCode::where('user_id', $userId)->first();
        
        if (!$referralCode) {
            return $this->generateReferralCode($user);
        }
        
        return $referralCode->code;
    }
    
    /**
     * Track conversion from referral
     * 
     * @param string $referralCode
     * @param User $newUser
     * @return bool
     */
    public function trackReferral($referralCode, $newUser)
    {
        $referrer = ReferralCode::where('code', $referralCode)->first();
        
        if (!$referrer) {
            return false;
        }
        
        $newUser->referred_by = $referrer->user_id;
        $newUser->save();
        
        $this->calculateReward($referrer->user_id, $newUser->id, 'signup');
        
        return true;
    }
    
    /**
     * Calculate and add reward for referrer
     * 
     * @param int $referrerId
     * @param int $referredId
     * @param string $actionType
     * @return void
     */
    public function calculateReward($referrerId, $referredId, $actionType)
    {
        $rewardAmount = $this->getRewardAmount($actionType);
        
        $reward = new ReferralReward();
        $reward->user_id = $referrerId;
        $reward->referred_user_id = $referredId;
        $reward->action_type = $actionType;
        $reward->reward_amount = $rewardAmount;
        $reward->status = 'pending';
        $reward->save();
    }
    
    /**
     * Get reward amount based on action type
     * 
     * @param string $actionType
     * @return float
     */
    private function getRewardAmount($actionType)
    {
        $rewardStructure = [
            'signup' => 5.00,         // $5 for new user signup
            'first_purchase' => 10.00, // $10 for first purchase
            'service_booking' => 15.00 // $15 for service booking
        ];
        
        return $rewardStructure[$actionType] ?? 0;
    }
    
    /**
     * Get user's total rewards
     * 
     * @param int $userId
     * @return array
     */
    public function getUserRewards($userId)
    {
        $totalReward = ReferralReward::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('reward_amount');
            
        $pendingReward = ReferralReward::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('reward_amount');
            
        $referralCount = User::where('referred_by', $userId)->count();
        
        return [
            'total' => $totalReward,
            'pending' => $pendingReward,
            'count' => $referralCount
        ];
    }
}
