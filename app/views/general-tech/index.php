<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="general-tech-container">
    <div class="container">
        <div class="tech-header">
            <h1>General Technology Services</h1>
            <p>Expert technical support and consultation</p>
        </div>

        <div class="tech-services">
            <div class="row">
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa fa-ticket"></i>
                        </div>
                        <h3>Support Tickets</h3>
                        <p>Submit technical issues and get expert support</p>
                        <a href="/general-tech/create-ticket" class="btn btn-primary">Create Ticket</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa fa-comments"></i>
                        </div>
                        <h3>Consultation</h3>
                        <p>Book a consultation with our experts</p>
                        <a href="/general-tech/consultation" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa fa-cog"></i>
                        </div>
                        <h3>Technical Services</h3>
                        <p>Browse our range of technical services</p>
                        <a href="/services/general-tech" class="btn btn-primary">View Services</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="featured-services">
            <h2>Featured Technical Services</h2>
            <div class="row">
                <?php foreach ($featured as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="featured-card">
                        <div class="featured-icon">
                            <i class="fa <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="featured-actions">
                            <span class="price">$<?php echo htmlspecialchars($service['price']); ?></span>
                            <a href="/order/<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="recent-tickets">
            <h2>Recent Support Tickets</h2>
            <div class="tickets-list">
                <?php foreach ($recentTickets as $ticket): ?>
                <div class="ticket-item">
                    <div class="ticket-header">
                        <span class="ticket-number">#<?php echo htmlspecialchars($ticket['id']); ?></span>
                        <span class="ticket-status <?php echo htmlspecialchars($ticket['status']); ?>">
                            <?php echo htmlspecialchars($ticket['status']); ?>
                        </span>
                    </div>
                    <div class="ticket-content">
                        <h3><?php echo htmlspecialchars($ticket['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($ticket['description'], 0, 150)); ?>...</p>
                        <div class="ticket-meta">
                            <span class="priority">
                                <i class="fa fa-circle"></i>
                                <?php echo htmlspecialchars($ticket['priority']); ?>
                            </span>
                            <span class="date"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.general-tech-container {
    padding: 40px 0;
}

.tech-header {
    text-align: center;
    margin-bottom: 50px;
}

.tech-header h1 {
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
    margin-bottom: 15px;
    color: #2c3e50;
}

.featured-services h2,
.recent-tickets h2 {
    text-align: center;
    margin-bottom: 40px;
    color: #2c3e50;
}

.featured-card {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    transition: transform 0.3s;
}

.featured-card:hover {
    transform: translateY(-5px);
}

.featured-icon {
    margin-bottom: 20px;
}

.featured-icon i {
    font-size: 2.5em;
}

.featured-actions {
    margin-top: 20px;
}

.featured-actions .btn {
    background: white;
    color: #3498db;
}

.featured-actions .btn:hover {
    background: #2980b9;
    color: white;
}

.tickets-list {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.ticket-item {
    border-bottom: 1px solid #eee;
    padding: 20px 0;
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.ticket-number {
    color: #3498db;
    font-weight: bold;
}

.ticket-status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

.ticket-status.pending {
    background: #f8f9fa;
    color: #666;
}

.ticket-status.in-progress {
    background: #e6f7ff;
    color: #1890ff;
}

.ticket-status.completed {
    background: #f6ffed;
    color: #52c41a;
}

.ticket-content h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.ticket-content p {
    color: #666;
    margin-bottom: 15px;
}

.ticket-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
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

@media (max-width: 768px) {
    .general-tech-container {
        padding: 20px 0;
    }
    
    .tech-header h1 {
        font-size: 2em;
    }
    
    .service-card {
        padding: 20px;
    }
    
    .ticket-meta {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
