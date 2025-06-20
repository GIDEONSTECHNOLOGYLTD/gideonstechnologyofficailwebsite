<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="repair-services-container">
    <div class="container">
        <div class="repair-header">
            <h1>Device Repair Services</h1>
            <p>Expert repair services for all your devices</p>
        </div>

        <div class="service-categories">
            <div class="row">
                <div class="col-md-4">
                    <div class="service-card" onclick="window.location.href='/repair-services/book-appointment?category=mobile'">
                        <div class="service-icon">
                            <i class="fa fa-mobile"></i>
                        </div>
                        <h3>Mobile Repair</h3>
                        <p>Screen repair, battery replacement, and more</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card" onclick="window.location.href='/repair-services/book-appointment?category=computer'">
                        <div class="service-icon">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <h3>Computer Repair</h3>
                        <p>Hardware repair, software installation, and troubleshooting</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card" onclick="window.location.href='/repair-services/book-appointment?category=tablet'">
                        <div class="service-icon">
                            <i class="fa fa-tablet"></i>
                        </div>
                        <h3>Tablet Repair</h3>
                        <p>Screen repair, battery replacement, and more</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="featured-services">
            <h2>Featured Repair Services</h2>
            <div class="row">
                <?php foreach ($featured as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="service-actions">
                            <span class="price">$<?php echo htmlspecialchars($service['price']); ?></span>
                            <a href="/repair-services/book-appointment?service=<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="recent-appointments">
            <h2>Recent Appointments</h2>
            <div class="appointments-list">
                <?php foreach ($recentAppointments as $appointment): ?>
                <div class="appointment-item">
                    <div class="appointment-header">
                        <span class="appointment-status <?php echo htmlspecialchars($appointment['status']); ?>">
                            <?php echo htmlspecialchars($appointment['status']); ?>
                        </span>
                        <span class="appointment-date"><?php echo date('M d, Y', strtotime($appointment['scheduled_date'])); ?></span>
                    </div>
                    <div class="appointment-content">
                        <h3><?php echo htmlspecialchars($appointment['service_name']); ?></h3>
                        <p class="device-info">
                            <i class="fa fa-mobile"></i>
                            <?php echo htmlspecialchars($appointment['device']); ?>
                        </p>
                        <p class="issue-description"><?php echo htmlspecialchars(substr($appointment['issue'], 0, 100)); ?>...</p>
                        <div class="appointment-meta">
                            <span class="priority">
                                <i class="fa fa-circle"></i>
                                <?php echo htmlspecialchars($appointment['priority']); ?>
                            </span>
                            <span class="time"><?php echo htmlspecialchars($appointment['scheduled_time']); ?></span>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <a href="/repair-services/appointment-status/<?php echo htmlspecialchars($appointment['id']); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-info-circle"></i>
                        </a>
                        <?php if ($appointment['status'] === 'pending'): ?>
                        <button class="btn btn-sm btn-outline-danger" onclick="cancelAppointment(<?php echo htmlspecialchars($appointment['id']); ?>)">
                            <i class="fa fa-times"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.repair-services-container {
    padding: 40px 0;
}

.repair-header {
    text-align: center;
    margin-bottom: 50px;
}

.repair-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.service-card {
    background: white;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-icon {
    font-size: 2.5em;
    color: #3498db;
    margin-bottom: 20px;
}

.service-card h3 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.featured-services h2,
.recent-appointments h2 {
    text-align: center;
    margin-bottom: 40px;
    color: #2c3e50;
}

.appointments-list {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.appointment-item {
    border-bottom: 1px solid #eee;
    padding: 20px 0;
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.appointment-status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

.appointment-status.pending {
    background: #f8f9fa;
    color: #666;
}

.appointment-status.in-progress {
    background: #e6f7ff;
    color: #1890ff;
}

.appointment-status.completed {
    background: #f6ffed;
    color: #52c41a;
}

.appointment-content h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.device-info {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #3498db;
    margin-bottom: 10px;
}

.issue-description {
    color: #666;
    margin-bottom: 15px;
}

.appointment-meta {
    display: flex;
    gap: 15px;
    color: #666;
}

.priority {
    display: flex;
    align-items: center;
    gap: 5px;
}

.priority i {
    font-size: 0.8em;
}

.appointment-actions {
    display: flex;
    gap: 10px;
    align-self: flex-start;
}

@media (max-width: 768px) {
    .repair-services-container {
        padding: 20px 0;
    }
    
    .service-card {
        padding: 20px;
    }
    
    .appointment-item {
        flex-direction: column;
        gap: 15px;
    }
    
    .appointment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .appointment-meta {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        // Here you would typically make an API call to cancel the appointment
        // For now, we'll just show a success message
        alert('Appointment has been cancelled');
        window.location.reload();
    }
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
