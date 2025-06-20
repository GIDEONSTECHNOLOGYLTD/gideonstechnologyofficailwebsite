<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="tickets-container">
    <div class="container">
        <div class="tickets-header">
            <div class="header-content">
                <h2>My Support Tickets</h2>
                <p>Track your technical support requests</p>
            </div>
            <div class="header-actions">
                <a href="/general-tech/create-ticket" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Create New Ticket
                </a>
            </div>
        </div>

        <div class="tickets-filters">
            <div class="filter-group">
                <select class="form-select" id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="filter-group">
                <select class="form-select" id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="hardware">Hardware</option>
                    <option value="software">Software</option>
                    <option value="network">Network</option>
                    <option value="security">Security</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="filter-group">
                <input type="date" class="form-control" id="dateFilter">
            </div>
        </div>

        <div class="tickets-list">
            <?php if (empty($tickets)): ?>
                <div class="no-tickets">
                    <i class="fa fa-ticket"></i>
                    <p>No tickets found</p>
                    <a href="/general-tech/create-ticket" class="btn btn-primary">
                        Create Your First Ticket
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-item" data-status="<?php echo htmlspecialchars($ticket['status']); ?>" 
                     data-category="<?php echo htmlspecialchars($ticket['category']); ?>"
                     data-date="<?php echo htmlspecialchars($ticket['created_at']); ?>">
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
                            <span class="category">
                                <i class="fa fa-tag"></i>
                                <?php echo htmlspecialchars($ticket['category']); ?>
                            </span>
                            <span class="priority">
                                <i class="fa fa-circle"></i>
                                <?php echo htmlspecialchars($ticket['priority']); ?>
                            </span>
                            <span class="date"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="ticket-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="showTicketDetails(<?php echo htmlspecialchars(json_encode($ticket)); ?>)">
                            <i class="fa fa-eye"></i>
                        </button>
                        <?php if ($ticket['status'] === 'pending'): ?>
                        <button class="btn btn-sm btn-outline-danger" onclick="cancelTicket(<?php echo htmlspecialchars($ticket['id']); ?>)">
                            <i class="fa fa-times"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="ticketDetails"></div>
            </div>
        </div>
    </div>
</div>

<style>
.tickets-container {
    padding: 40px 0;
}

.tickets-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header-content h2 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.tickets-filters {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.filter-group {
    flex: 1;
    min-width: 200px;
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
    display: flex;
    align-items: flex-start;
    gap: 20px;
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
    gap: 15px;
    color: #666;
}

.category {
    display: flex;
    align-items: center;
    gap: 5px;
}

.priority {
    display: flex;
    align-items: center;
    gap: 5px;
}

.priority i {
    font-size: 0.8em;
}

.ticket-actions {
    display: flex;
    gap: 10px;
    align-self: flex-start;
}

.no-tickets {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-tickets i {
    font-size: 3em;
    margin-bottom: 15px;
    color: #ddd;
}

.pagination {
    margin-top: 30px;
}

@media (max-width: 768px) {
    .tickets-container {
        padding: 20px 0;
    }
    
    .tickets-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .tickets-filters {
        flex-direction: column;
        gap: 10px;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .ticket-item {
        flex-direction: column;
        gap: 15px;
    }
    
    .ticket-actions {
        justify-content: center;
    }
}
</style>

<script>
function showTicketDetails(ticket) {
    const details = document.getElementById('ticketDetails');
    details.innerHTML = `
        <div class="details-header">
            <div class="ticket-number">#${ticket.id}</div>
            <div class="ticket-status ${ticket.status}">${ticket.status}</div>
        </div>
        <div class="details-content">
            <div class="detail-row">
                <span>Title:</span>
                <span>${ticket.title}</span>
            </div>
            <div class="detail-row">
                <span>Description:</span>
                <span>${ticket.description}</span>
            </div>
            <div class="detail-row">
                <span>Category:</span>
                <span>${ticket.category}</span>
            </div>
            <div class="detail-row">
                <span>Priority:</span>
                <span class="priority">${ticket.priority}</span>
            </div>
            <div class="detail-row">
                <span>Created:</span>
                <span>${new Date(ticket.created_at).toLocaleString()}</span>
            </div>
            ${ticket.updated_at ? `
            <div class="detail-row">
                <span>Last Updated:</span>
                <span>${new Date(ticket.updated_at).toLocaleString()}</span>
            </div>
            ` : ''}
            ${ticket.assigned_to ? `
            <div class="detail-row">
                <span>Assigned To:</span>
                <span>${ticket.assigned_to}</span>
            </div>
            ` : ''}
            ${ticket.attachments ? `
            <div class="detail-row">
                <span>Attachments:</span>
                <div class="attachments">
                    ${ticket.attachments.map(attachment => `
                        <a href="${attachment.url}" target="_blank">${attachment.name}</a>
                    `).join('<br>')}
                </div>
            </div>
            ` : ''}
        </div>
    `;

    new bootstrap.Modal(document.getElementById('ticketDetailsModal')).show();
}

function cancelTicket(ticketId) {
    if (confirm('Are you sure you want to cancel this ticket?')) {
        // Here you would typically make an API call to cancel the ticket
        // For now, we'll just show a success message
        alert('Ticket has been cancelled');
        window.location.reload();
    }
}

// Add filter functionality
const statusFilter = document.getElementById('statusFilter');
const categoryFilter = document.getElementById('categoryFilter');
const dateFilter = document.getElementById('dateFilter');
const ticketItems = document.querySelectorAll('.ticket-item');

function applyFilters() {
    const status = statusFilter.value;
    const category = categoryFilter.value;
    const date = dateFilter.value;
    
    ticketItems.forEach(item => {
        const statusMatch = status === 'all' || item.dataset.status === status;
        const categoryMatch = category === 'all' || item.dataset.category === category;
        const dateMatch = !date || new Date(item.dataset.date) >= new Date(date);
        
        item.style.display = statusMatch && categoryMatch && dateMatch ? 'flex' : 'none';
    });
}

statusFilter.addEventListener('change', applyFilters);
categoryFilter.addEventListener('change', applyFilters);
dateFilter.addEventListener('change', applyFilters);
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
