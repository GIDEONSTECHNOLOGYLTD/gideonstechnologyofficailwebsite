<?php

namespace App\Dependencies;

use DI\Container;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Slim\App;
use App\Services\PaymentService;
use Stripe\StripeClient;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use Yabacon\Paystack;
use Twig\Extension\DebugExtension;
use Slim\Views\TwigExtension;

/**
 * Service Provider
 * 
 * Registers application dependencies in the container
 */
class ServiceProvider
{
    /**
     * Register dependencies
     * 
     * @param Container $container DI container
     * @return void
     */
    public static function register(Container $container): void
    {
        // Initialize SES lockdown configuration if not in production
        if (getenv('APP_ENV') !== 'production') {
            self::initializeLockdown();
        }
        
        // Register Twig view renderer
        $container->set(Twig::class, function (Container $container) {
            $paths = [
                __DIR__ . '/../../templates',
                __DIR__ . '/../../public/assets',
            ];
            
            $cache = getenv('APP_ENV') === 'production' 
                ? __DIR__ . '/../../tmp/cache/twig' 
                : false;
            
            $twig = Twig::create($paths, [
                'cache' => $cache,
                'debug' => getenv('APP_ENV') !== 'production',
                'auto_reload' => getenv('APP_ENV') !== 'production',
            ]);
            
            // Add extension for named routes - we need to get the app from the container
            /** @var App $app */
            $app = $container->get(App::class);
            $twig->addExtension(new TwigExtension(
                $app->getRouteCollector()->getRouteParser(),
                $app->getBasePath()
            ));
            
            // Add debug extension if in development
            if (getenv('APP_ENV') !== 'production') {
                $twig->addExtension(new DebugExtension());
            }
            
            return $twig;
        });
        
        // Register Flash messages
        $container->set(Messages::class, function () {
            return new Messages();
        });
        
        // Register Payment Service with proper implementations
        $container->set(PaymentService::class, function (Container $container) {
            // Set up Stripe client
            $stripeKey = getenv('STRIPE_SECRET_KEY') ?: 'sk_test_sample_key';
            $stripe = new StripeClient($stripeKey);
            
            // Set up PayPal client
            $paypalClientId = getenv('PAYPAL_CLIENT_ID') ?: 'client_id_sample';
            $paypalSecret = getenv('PAYPAL_SECRET') ?: 'secret_sample';
            $paypalMode = getenv('PAYPAL_MODE') ?: 'sandbox';
            
            $paypal = new ApiContext(
                new OAuthTokenCredential($paypalClientId, $paypalSecret)
            );
            $paypal->setConfig([
                'mode' => $paypalMode,
                'log.LogEnabled' => true,
                'log.FileName' => __DIR__ . '/../../logs/paypal.log',
                'log.LogLevel' => 'DEBUG'
            ]);
            
            // Set up Paystack client
            $paystackKey = getenv('PAYSTACK_SECRET_KEY') ?: 'sk_test_sample_key';
            $paystack = new Paystack($paystackKey);
            
            return new PaymentService($stripe, $paypal, $paystack);
        });
    }
    
    /**
     * Initialize SES lockdown configuration with safe settings
     * to prevent "Removing unpermitted intrinsics" errors
     * 
     * @return void
     */
    private static function initializeLockdown(): void
    {
        $lockdownPath = __DIR__ . '/../../lockdown-config.js';
        
        if (file_exists($lockdownPath)) {
            try {
                // Make sure the file has proper permissions
                chmod($lockdownPath, 0644);
                
                // Update lockdown configuration to be more permissive
                $lockdownConfig = file_get_contents($lockdownPath);
                
                if (strpos($lockdownConfig, '__shimTransforms__: []') !== false) {
                    // Make sure all taming options are set to 'unsafe'
                    if (strpos($lockdownConfig, 'mathTaming: \'unsafe\'') === false) {
                        $lockdownConfig = preg_replace('/mathTaming: \'[^\']+\'/', 'mathTaming: \'unsafe\'', $lockdownConfig);
                    }
                    
                    // Set requireThis to false to prevent errors
                    if (strpos($lockdownConfig, 'requireThis: false') === false) {
                        $lockdownConfig = preg_replace('/requireThis: [^\n]+/', 'requireThis: false', $lockdownConfig);
                    }
                    
                    file_put_contents($lockdownPath, $lockdownConfig);
                }
                
                // Log that lockdown has been initialized
                error_log('SES Lockdown configuration initialized successfully');
            } catch (\Exception $e) {
                error_log('Error initializing SES Lockdown configuration: ' . $e->getMessage());
            }
        }
    }
}