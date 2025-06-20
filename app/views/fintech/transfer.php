<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="transfer-container">
    <div class="container">
        <div class="transfer-header">
            <h2>Send Money</h2>
            <p>Transfer funds to another account</p>
        </div>

        <div class="transfer-form">
            <form action="/fintech/process-transfer" method="POST" id="transferForm">
                <div class="form-group">
                    <label for="recipient">Recipient Account</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="recipient" name="recipient" required>
                        <button type="button" class="btn btn-outline-secondary" id="checkRecipient">
                            <i class="fa fa-check"></i> Check
                        </button>
                    </div>
                    <small id="recipientHelp" class="form-text text-muted">
                        Enter recipient's account number or email
                    </small>
                </div>

                <div class="form-group">
                    <label for="amount">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="amount" name="amount" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reference">Reference</label>
                    <input type="text" class="form-control" id="reference" name="reference" placeholder="Optional reference">
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="3" placeholder="Optional message to recipient"></textarea>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="saveRecipient" name="saveRecipient">
                        <label class="form-check-label" for="saveRecipient">
                            Save this recipient for future transfers
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="transferButton">
                        <i class="fa fa-exchange"></i> Send Money
                    </button>
                </div>
            </form>
        </div>

        <div class="recent-recipients" id="recentRecipients">
            <h3>Recent Recipients</h3>
            <div class="recipients-list">
                <!-- Recent recipients will be populated via JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
.transfer-container {
    padding: 40px 0;
}

.transfer-header {
    text-align: center;
    margin-bottom: 40px;
}

.transfer-header h2 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.transfer-form {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 10px;
}

.input-group-text {
    background: #f8f9fa;
}

.form-text {
    color: #666;
}

.recent-recipients {
    margin-top: 40px;
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.recipients-list {
    margin-top: 20px;
}

.recipient-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.recipient-item:hover {
    background: #f8f9fa;
}

.recipient-info {
    margin-left: 10px;
}

.recipient-name {
    font-weight: bold;
}

.recipient-account {
    color: #666;
    font-size: 0.9em;
}

@media (max-width: 768px) {
    .transfer-container {
        padding: 20px 0;
    }
    
    .transfer-form {
        padding: 20px;
    }
    
    .recent-recipients {
        padding: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load recent recipients
    loadRecentRecipients();

    // Check recipient button click
    document.getElementById('checkRecipient').addEventListener('click', function() {
        const recipientInput = document.getElementById('recipient');
        const recipientValue = recipientInput.value.trim();
        
        if (!recipientValue) {
            alert('Please enter a recipient');
            return;
        }

        // Here you would typically make an API call to validate the recipient
        // For now, we'll just show a success message
        alert('Recipient validated successfully');
    });

    // Form submission
    document.getElementById('transferForm').addEventListener('submit', function(e) {
        const amount = document.getElementById('amount').value;
        const recipient = document.getElementById('recipient').value;

        if (!amount || !recipient) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }

        if (amount < 0.01) {
            e.preventDefault();
            alert('Amount must be greater than $0.01');
            return;
        }
    });

    // Save recipient checkbox change
    document.getElementById('saveRecipient').addEventListener('change', function() {
        const saveButton = this;
        if (saveButton.checked) {
            const recipient = document.getElementById('recipient').value;
            if (recipient) {
                saveRecipient(recipient);
            }
        }
    });
});

function loadRecentRecipients() {
    // Here you would typically make an API call to get recent recipients
    // For now, we'll just add some sample data
    const recipientsList = document.querySelector('.recipients-list');
    const sampleRecipients = [
        { name: 'John Doe', account: '1234567890' },
        { name: 'Jane Smith', account: '0987654321' },
        { name: 'Mike Johnson', account: '1122334455' }
    ];

    sampleRecipients.forEach(recipient => {
        const item = document.createElement('div');
        item.className = 'recipient-item';
        item.innerHTML = `
            <div class="recipient-info">
                <span class="recipient-name">${recipient.name}</span>
                <span class="recipient-account">${recipient.account}</span>
            </div>
        `;
        item.addEventListener('click', function() {
            document.getElementById('recipient').value = recipient.account;
        });
        recipientsList.appendChild(item);
    });
}

function saveRecipient(recipient) {
    // Here you would typically make an API call to save the recipient
    // For now, we'll just show a success message
    alert('Recipient saved successfully');
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
