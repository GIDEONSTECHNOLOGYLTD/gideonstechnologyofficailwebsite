<?php

namespace App\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Respect\Validation\Validator as v;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            $settings = Setting::all()->keyBy('key');
            
            return $this->render($response, 'admin/settings/index.php', [
                'title' => 'Application Settings',
                'settings' => $settings,
                'active_menu' => 'settings'
            ]);
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Settings Load Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to load settings.');
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
    }

    /**
     * Update settings
     */
    public function update(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        try {
            $validation = $this->validateSettings($data);
            
            if (!$validation['valid']) {
                $this->flash('error', implode(' ', $validation['errors']));
                return $response->withHeader('Location', '/admin/settings')->withStatus(302);
            }
            
            // Update general settings
            $this->updateSetting('site_name', $data['site_name'] ?? '');
            $this->updateSetting('site_email', $data['site_email'] ?? '');
            $this->updateSetting('site_phone', $data['site_phone'] ?? '');
            $this->updateSetting('site_address', $data['site_address'] ?? '');
            $this->updateSetting('site_currency', $data['site_currency'] ?? 'USD');
            $this->updateSetting('items_per_page', (int)($data['items_per_page'] ?? 10));
            
            // Update email settings
            $this->updateEmailSettings($data);
            
            // Update payment settings
            $this->updatePaymentSettings($data);
            
            // Update maintenance mode
            $this->updateSetting('maintenance_mode', isset($data['maintenance_mode']) ? 1 : 0);
            
            $this->flash('success', 'Settings updated successfully');
            return $response->withHeader('Location', '/admin/settings')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Settings Update Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to update settings');
            return $response->withHeader('Location', '/admin/settings')->withStatus(302);
        }
    }
    
    /**
     * Validate settings data
     */
    private function validateSettings(array $data): array
    {
        $errors = [];
        
        $validators = [
            'site_name' => v::notEmpty()->length(2, 100),
            'site_email' => v::notEmpty()->email(),
            'site_phone' => v::optional(v::phone()),
            'site_currency' => v::in(['USD', 'EUR', 'GBP', 'NGN']),
            'items_per_page' => v::intVal()->min(5)->max(100),
            'mail_host' => v::when(
                !empty($data['mail_driver']) && $data['mail_driver'] !== 'mail',
                v::notEmpty(),
                v::optional(v::stringType())
            ),
            'mail_port' => v::when(
                !empty($data['mail_driver']) && $data['mail_driver'] !== 'mail',
                v::notEmpty()->intVal()->min(1)->max(65535),
                v::optional(v::intVal())
            ),
            'mail_username' => v::when(
                !empty($data['mail_driver']) && $data['mail_driver'] !== 'mail',
                v::notEmpty(),
                v::optional(v::stringType())
            ),
            'mail_password' => v::when(
                !empty($data['mail_driver']) && $data['mail_driver'] !== 'mail' && !empty($data['mail_password']),
                v::notEmpty(),
                v::optional(v::stringType())
            ),
            'mail_encryption' => v::when(
                !empty($data['mail_driver']) && $data['mail_driver'] !== 'mail',
                v::in(['tls', 'ssl', '']),
                v::optional(v::stringType())
            ),
        ];
        
        foreach ($validators as $field => $validator) {
            if (isset($data[$field]) && !$validator->validate($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is invalid';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Update a setting in the database
     */
    private function updateSetting(string $key, $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );
    }
    
    /**
     * Update email settings
     */
    private function updateEmailSettings(array $data): void
    {
        $emailSettings = [
            'mail_driver' => $data['mail_driver'] ?? 'mail',
            'mail_host' => $data['mail_host'] ?? '',
            'mail_port' => $data['mail_port'] ?? 587,
            'mail_username' => $data['mail_username'] ?? '',
            'mail_password' => !empty($data['mail_password']) ? $data['mail_password'] : null,
            'mail_encryption' => $data['mail_encryption'] ?? 'tls',
            'mail_from_address' => $data['mail_from_address'] ?? $data['site_email'] ?? '',
            'mail_from_name' => $data['mail_from_name'] ?? $data['site_name'] ?? '',
        ];
        
        // Don't update password if not provided
        if (empty($data['mail_password'])) {
            unset($emailSettings['mail_password']);
        }
        
        foreach ($emailSettings as $key => $value) {
            $this->updateSetting($key, $value);
        }
    }
    
    /**
     * Update payment settings
     */
    private function updatePaymentSettings(array $data): void
    {
        $paymentSettings = [
            'stripe_enabled' => isset($data['stripe_enabled']) ? 1 : 0,
            'stripe_public_key' => $data['stripe_public_key'] ?? '',
            'stripe_secret_key' => $data['stripe_secret_key'] ?? '',
            'stripe_webhook_secret' => $data['stripe_webhook_secret'] ?? '',
            'paypal_enabled' => isset($data['paypal_enabled']) ? 1 : 0,
            'paypal_client_id' => $data['paypal_client_id'] ?? '',
            'paypal_secret' => $data['paypal_secret'] ?? '',
            'paypal_mode' => $data['paypal_mode'] ?? 'sandbox',
        ];
        
        foreach ($paymentSettings as $key => $value) {
            $this->updateSetting($key, $value);
        }
    }
}
