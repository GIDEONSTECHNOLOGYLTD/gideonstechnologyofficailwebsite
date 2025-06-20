<?php

namespace App\Controllers;

use App\Models\Service;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Mail\ServiceRequestNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RepairServicesController extends Controller
{
    public function index() {
        try {
            $services = $this->servicesModel->getServicesByCategory('repair');
            $featured = $this->servicesModel->getFeaturedServicesByCategory('repair');
            $recentAppointments = $this->appointmentsModel->getRecentAppointments(5);
            
            require_once APP_PATH . '/views/repair-services/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return redirect('/error');
            exit();
        }
    }

    public function bookAppointment() {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
            exit();
        }

        try {
            $services = $this->servicesModel->getServicesByCategory('repair');
            $availableTimes = $this->appointmentsModel->getAvailableTimes();
            
            require_once APP_PATH . '/views/repair-services/book-appointment.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return redirect('/error');
            exit();
        }
    }

    public function submitAppointment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            return redirect('/error');
            exit();
        }

        try {
            $serviceId = $_POST['service_id'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $description = $_POST['description'] ?? '';
            $device = $_POST['device'] ?? '';
            $issue = $_POST['issue'] ?? '';

            if (!$serviceId || !$date || !$time || !$description || !$device || !$issue) {
                throw new \Exception('All fields are required');
            }

            $appointmentId = $this->appointmentsModel->createAppointment([
                'user_id' => $_SESSION['user_id'],
                'service_id' => $serviceId,
                'scheduled_date' => $date,
                'scheduled_time' => $time,
                'description' => $description,
                'device' => $device,
                'issue' => $issue,
                'status' => 'pending'
            ]);

            $_SESSION['success'] = 'Appointment booked successfully';
            return redirect('/repair-services/appointments');
            exit();
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return redirect('/repair-services/book-appointment');
            exit();
        }
    }

    public function appointments() {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                return json_encode(['error' => 'User not authenticated']);
            }
            $appointments = $this->appointmentService->getUserAppointments($userId);
            return json_encode(['appointments' => $appointments]);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}