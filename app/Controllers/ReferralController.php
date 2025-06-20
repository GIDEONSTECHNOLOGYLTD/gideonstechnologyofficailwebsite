<?php

namespace App\Controllers;

use App\Core\Container;
use App\Services\ReferralService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class ReferralController extends BaseController
{
    protected $referralService;
    
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->referralService = $container->get(ReferralService::class);
    }
    
    /**
     * Display user's referral dashboard
     */
    public function dashboard(Request $request, Response $response)
    {
        $user = $this->auth->user();
        
        if (!$user) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        
        $referralCode = $this->referralService->getUserReferralCode($user->id);
        $rewards = $this->referralService->getUserRewards($user->id);
        
        $data = [
            'user' => $user,
            'referralCode' => $referralCode,
            'referralLink' => $this->generateReferralLink($referralCode),
            'rewards' => $rewards,
            'pageTitle' => 'Referral Dashboard',
            'socialLinks' => $this->getSocialShareLinks($referralCode)
        ];
        
        return $this->view->render($response, 'user/referral_dashboard.php', $data);
    }
    
    /**
     * Process a referral
     */
    public function processReferral(Request $request, Response $response, $args)
    {
        $referralCode = $args['code'] ?? '';
        
        if (!$referralCode) {
            return $response->withHeader('Location', '/register')->withStatus(302);
        }
        
        // Store referral code in session for later use during registration
        $_SESSION['referral_code'] = $referralCode;
        
        return $response->withHeader('Location', '/register?ref=' . $referralCode)->withStatus(302);
    }
    
    /**
     * Generate shareable referral link
     */
    private function generateReferralLink($code)
    {
        $baseUrl = $this->container->get('settings')['app']['url'] ?? 'https://gideonstechnology.com';
        return $baseUrl . '/refer/' . $code;
    }
    
    /**
     * Get social sharing links with the referral code
     */
    private function getSocialShareLinks($code)
    {
        $referralLink = urlencode($this->generateReferralLink($code));
        $message = urlencode('Check out Gideons Technology for premium tech services and products! Use my referral link to get started:');
        
        return [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$referralLink}",
            'twitter' => "https://twitter.com/intent/tweet?text={$message}&url={$referralLink}",
            'whatsapp' => "https://api.whatsapp.com/send?text={$message} {$referralLink}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$referralLink}",
            'email' => "mailto:?subject=" . urlencode('Gideons Technology Referral') . "&body={$message} {$referralLink}"
        ];
    }
    
    /**
     * Admin referral management
     */
    public function adminDashboard(Request $request, Response $response)
    {
        $user = $this->auth->user();
        
        if (!$user || !$user->is_admin) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        
        $pendingRewards = $this->referralService->getPendingRewards();
        
        $data = [
            'user' => $user,
            'pendingRewards' => $pendingRewards,
            'pageTitle' => 'Referral Management'
        ];
        
        return $this->view->render($response, 'admin/referral_management.php', $data);
    }
}
