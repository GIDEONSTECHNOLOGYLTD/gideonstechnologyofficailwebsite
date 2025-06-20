<?php
namespace App\Services;

use App\Models\Device;

class NotificationService {
    private $config;
    private $device;

    public function __construct(Device $device, array $config) {
        $this->device = $device;
        $this->config = $config;
    }

    public function send($userId, $message, $data = []) {
        $devices = $this->device->getUserDevices($userId);
        $success = true;

        foreach ($devices as $device) {
            try {
                $this->sendToDevice($device, $message, $data);
            } catch (\Exception $e) {
                $success = false;
                if ($e->getMessage() === 'InvalidRegistration') {
                    $this->device->deactivate($device['device_token']);
                }
            }
        }

        return $success;
    }

    private function sendToDevice($device, $message, $data) {
        $serverKey = $this->config['fcm_server_key'];
        
        $fields = [
            'to' => $device['device_token'],
            'notification' => [
                'title' => $this->config['app_name'],
                'body' => $message,
                'sound' => 'default'
            ],
            'data' => $data
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('FCM request failed');
        }

        $response = json_decode($result, true);
        if (isset($response['results'][0]['error'])) {
            throw new \Exception($response['results'][0]['error']);
        }

        return true;
    }
}