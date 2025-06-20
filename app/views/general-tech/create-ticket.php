<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="ticket-container">
    <div class="container">
        <div class="ticket-header">
            <h2>Create Support Ticket</h2>
            <p>Submit your technical issue and get expert support</p>
        </div>

        <div class="ticket-form">
            <form action="/general-tech/submit-ticket" method="POST" id="ticketForm">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="hardware">Hardware Issues</option>
                        <option value="software">Software Issues</option>
                        <option value="network">Network Issues</option>
                        <option value="security">Security Issues</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="6" required></textarea>
                </div>

                <div class="form-group">
                    <label for="attachments">Attachments (Optional)</label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    <small class="form-text text-muted">
                        You can attach screenshots, error logs, or any relevant files
                    </small>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="urgent" name="urgent">
                        <label class="form-check-label" for="urgent">
                            This is an urgent issue
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-ticket"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.ticket-container {
    padding: 40px 0;
}

.ticket-header {
    text-align: center;
    margin-bottom: 40px;
}

.ticket-header h2 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.ticket-form {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #2c3e50;
}

.form-control {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.form-select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.form-select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.form-text {
    color: #666;
    font-size: 0.9em;
}

.form-check {
    margin-top: 10px;
}

.form-check-input {
    margin-right: 10px;
}

@media (max-width: 768px) {
    .ticket-container {
        padding: 20px 0;
    }
    
    .ticket-form {
        padding: 20px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ticketForm = document.getElementById('ticketForm');
    const attachmentsInput = document.getElementById('attachments');
    const urgentCheckbox = document.getElementById('urgent');
    const prioritySelect = document.getElementById('priority');

    // Update priority based on urgent checkbox
    urgentCheckbox.addEventListener('change', function() {
        if (this.checked) {
            prioritySelect.value = 'urgent';
            prioritySelect.disabled = true;
        } else {
            prioritySelect.disabled = false;
        }
    });

    // Form validation
    ticketForm.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();

        if (!title || !description) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }

        if (description.length < 10) {
            e.preventDefault();
            alert('Please provide a more detailed description');
            return;
        }
    });

    // Handle file attachments
    attachmentsInput.addEventListener('change', function(e) {
        const files = this.files;
        if (files.length > 5) {
            alert('You can only upload up to 5 files');
            this.value = '';
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5 * 1024 * 1024) { // 5MB limit
                alert('File size must be less than 5MB');
                this.value = '';
                return;
            }
        }
    });
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
