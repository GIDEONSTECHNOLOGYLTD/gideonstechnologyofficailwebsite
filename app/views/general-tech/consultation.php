<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="consultation-container">
    <div class="container">
        <div class="consultation-header">
            <h2>Technical Consultation</h2>
            <p>Book a consultation with our expert technical consultants</p>
        </div>

        <div class="consultation-filters">
            <div class="filter-group">
                <select class="form-select" id="expertiseFilter">
                    <option value="all">All Expertise</option>
                    <option value="hardware">Hardware</option>
                    <option value="software">Software</option>
                    <option value="network">Network</option>
                    <option value="security">Security</option>
                    <option value="devops">DevOps</option>
                </select>
            </div>
            <div class="filter-group">
                <select class="form-select" id="availabilityFilter">
                    <option value="all">All Availability</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>

        <div class="consultants-list">
            <?php if (empty($consultants)): ?>
                <div class="no-consultants">
                    <i class="fa fa-comments"></i>
                    <p>No consultants available at this time</p>
                    <p>Please check back later or contact support</p>
                </div>
            <?php else: ?>
                <?php foreach ($consultants as $consultant): ?>
                <div class="consultant-card" data-expertise="<?php echo htmlspecialchars($consultant['expertise']); ?>"
                     data-availability="<?php echo htmlspecialchars($consultant['availability']); ?>">
                    <div class="consultant-info">
                        <div class="consultant-avatar">
                            <img src="<?php echo htmlspecialchars($consultant['avatar']); ?>" alt="<?php echo htmlspecialchars($consultant['name']); ?>">
                        </div>
                        <div class="consultant-details">
                            <h3><?php echo htmlspecialchars($consultant['name']); ?></h3>
                            <div class="consultant-expertise">
                                <i class="fa fa-tag"></i>
                                <?php echo htmlspecialchars($consultant['expertise']); ?>
                            </div>
                            <div class="consultant-rating">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fa fa-star <?php echo $i < $consultant['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <span class="rating-number"><?php echo $consultant['rating']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="consultant-schedule">
                        <div class="schedule-header">
                            <h4>Available Slots</h4>
                            <span class="price">$<?php echo htmlspecialchars($consultant['rate']); ?>/hr</span>
                        </div>
                        <div class="schedule-list">
                            <?php foreach ($consultant['available_slots'] as $slot): ?>
                            <div class="schedule-slot" 
                                 data-date="<?php echo htmlspecialchars($slot['date']); ?>"
                                 data-time="<?php echo htmlspecialchars($slot['time']); ?>">
                                <span class="slot-time"><?php echo htmlspecialchars($slot['time']); ?></span>
                                <span class="slot-date"><?php echo htmlspecialchars($slot['date']); ?></span>
                                <button class="btn btn-sm btn-primary" 
                                        onclick="bookConsultation(<?php echo htmlspecialchars(json_encode($consultant)); ?>, 
                                                               '<?php echo htmlspecialchars($slot['date']); ?>',
                                                               '<?php echo htmlspecialchars($slot['time']); ?>')">
                                    Book Now
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Book Consultation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="bookingForm">
                            <input type="hidden" id="consultantId" name="consultant_id">
                            <input type="hidden" id="scheduledDate" name="scheduled_date">
                            <input type="hidden" id="scheduledTime" name="scheduled_time">
                            
                            <div class="form-group">
                                <label for="topic">Consultation Topic</label>
                                <textarea class="form-control" id="topic" name="topic" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <select class="form-select" id="duration" name="duration" required>
                                    <option value="30">30 minutes</option>
                                    <option value="60">1 hour</option>
                                    <option value="90">1.5 hours</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-calendar-plus"></i> Confirm Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.consultation-container {
    padding: 40px 0;
}

.consultation-header {
    text-align: center;
    margin-bottom: 40px;
}

.consultation-header h2 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.consultation-filters {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.consultants-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.consultant-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.consultant-card:hover {
    transform: translateY(-5px);
}

.consultant-info {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.consultant-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
}

.consultant-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.consultant-details h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.consultant-expertise {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #3498db;
    margin-bottom: 10px;
}

.consultant-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #f1c40f;
}

.rating-number {
    color: #666;
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.schedule-header h4 {
    color: #2c3e50;
    margin: 0;
}

.price {
    color: #e74c3c;
    font-weight: bold;
}

.schedule-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.schedule-slot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 5px;
    transition: all 0.3s;
}

.schedule-slot:hover {
    background: #f8f9fa;
}

.slot-time {
    font-weight: bold;
}

.no-consultants {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-consultants i {
    font-size: 3em;
    margin-bottom: 15px;
    color: #ddd;
}

@media (max-width: 768px) {
    .consultation-container {
        padding: 20px 0;
    }
    
    .consultation-filters {
        flex-direction: column;
        gap: 10px;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .consultant-info {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .consultant-expertise,
    .consultant-rating {
        justify-content: center;
    }
    
    .schedule-header {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
}
</style>

<script>
function bookConsultation(consultant, date, time) {
    document.getElementById('consultantId').value = consultant.id;
    document.getElementById('scheduledDate').value = date;
    document.getElementById('scheduledTime').value = time;
    
    // Set default topic based on consultant's expertise
    document.getElementById('topic').value = `Consultation about ${consultant.expertise}`;
    
    new bootstrap.Modal(document.getElementById('bookingModal')).show();
}

// Add filter functionality
const expertiseFilter = document.getElementById('expertiseFilter');
const availabilityFilter = document.getElementById('availabilityFilter');
const consultantCards = document.querySelectorAll('.consultant-card');

function applyFilters() {
    const expertise = expertiseFilter.value;
    const availability = availabilityFilter.value;
    
    consultantCards.forEach(card => {
        const expertiseMatch = expertise === 'all' || card.dataset.expertise === expertise;
        const availabilityMatch = availability === 'all' || card.dataset.availability === availability;
        
        card.style.display = expertiseMatch && availabilityMatch ? 'grid' : 'none';
    });
}

expertiseFilter.addEventListener('change', applyFilters);
availabilityFilter.addEventListener('change', applyFilters);

// Form submission
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            // Here you would typically make an API call to book the consultation
            // For now, we'll just show a success message
            alert('Consultation booked successfully');
            window.location.href = '/general-tech/consultations';
        } catch (error) {
            alert('Error booking consultation: ' + error.message);
        }
    });
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
