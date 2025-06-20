<?php
namespace App\Services;

use App\Models\RepairRequest;
use App\Services\NotificationService;
use App\Services\EmailService;

class RepairNotificationService {
    private $repairRequest;
    private $notification;
    private $email;
    private $statusMessages = [
        'in_progress' => 'Your repair request is now being processed.',
        'diagnosed' => 'Device diagnosis complete. We will contact you with details.',
        'awaiting_parts' => 'Parts have been ordered for your repair.',
        'ready' => 'Your device is repaired and ready for pickup.',
        'completed' => 'Repair service completed. Thank you for choosing us!'
    ];

    public function __construct() {
        $this->repairRequest = new RepairRequest();
        $this->notification = new NotificationService();
        $this->email = new EmailService();
    }

    public function notifyStatusChange($requestId, $newStatus) {
        $request = $this->repairRequest->findById($requestId);
        if (!$request) {
            throw new \Exception('Repair request not found');
        }

        $message = $this->statusMessages[$newStatus] ?? 'Your repair status has been updated.';
        
        // Send email notification
        $this->email->send(
            $request['email'],
            'Repair Status Update',
            $message
        );

        // If user has mobile app, send push notification
        if (isset($request['user_id'])) {
            $this->notification->send(
                $request['user_id'],
                $message,
                [
                    'type' => 'repair_update',
                    'request_id' => $requestId,
                    'status' => $newStatus
                ]
            );
        }

        return true;
    }
}