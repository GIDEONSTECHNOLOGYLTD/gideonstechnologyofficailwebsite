<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="transactions-container">
    <div class="container">
        <div class="transactions-header">
            <h2>Transaction History</h2>
            <div class="filters">
                <div class="filter-group">
                    <select class="form-select" id="typeFilter">
                        <option value="all">All Transactions</option>
                        <option value="credit">Credits</option>
                        <option value="debit">Debits</option>
                    </select>
                </div>
                <div class="filter-group">
                    <input type="date" class="form-control" id="dateFilter">
                </div>
                <div class="filter-group">
                    <button class="btn btn-primary" id="applyFilters">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="transactions-list">
            <?php if (empty($transactions)): ?>
                <div class="no-transactions">
                    <i class="fa fa-history"></i>
                    <p>No transactions found</p>
                </div>
            <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                <div class="transaction-item" data-type="<?php echo htmlspecialchars($transaction['type']); ?>">
                    <div class="transaction-info">
                        <div class="transaction-type">
                            <i class="fa <?php echo htmlspecialchars($transaction['type_icon']); ?>"></i>
                            <span><?php echo htmlspecialchars($transaction['type']); ?></span>
                        </div>
                        <div class="transaction-details">
                            <p class="transaction-description"><?php echo htmlspecialchars($transaction['description']); ?></p>
                            <p class="transaction-date"><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="transaction-amount">
                        <span class="amount <?php echo $transaction['amount'] < 0 ? 'negative' : 'positive'; ?>">
                            $<?php echo number_format(abs($transaction['amount']), 2); ?>
                        </span>
                    </div>
                    <div class="transaction-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="showDetails(<?php echo htmlspecialchars(json_encode($transaction)); ?>)">
                            <i class="fa fa-info-circle"></i>
                        </button>
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

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="transactionDetails"></div>
            </div>
        </div>
    </div>
</div>

<style>
.transactions-container {
    padding: 40px 0;
}

.transactions-header {
    margin-bottom: 30px;
}

.transactions-header h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.filters {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.transactions-list {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.transaction-item {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: all 0.3s;
}

.transaction-item:hover {
    background: #f8f9fa;
}

.transaction-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.transaction-type {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 10px;
    border-radius: 5px;
}

.transaction-type i {
    font-size: 1.2em;
}

.transaction-details {
    flex: 1;
}

.transaction-description {
    margin: 0;
    font-weight: 500;
}

.transaction-date {
    color: #666;
    font-size: 0.9em;
    margin: 5px 0 0;
}

.transaction-amount {
    text-align: right;
    font-weight: bold;
}

.amount.positive {
    color: #2ecc71;
}

.amount.negative {
    color: #e74c3c;
}

.transaction-actions {
    text-align: right;
}

.no-transactions {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-transactions i {
    font-size: 3em;
    margin-bottom: 10px;
    color: #ddd;
}

.pagination {
    margin-top: 30px;
}

@media (max-width: 768px) {
    .transactions-container {
        padding: 20px 0;
    }
    
    .filters {
        flex-direction: column;
        gap: 10px;
    }
    
    .filter-group {
        width: 100%;
    }
}
</style>

<script>
function showDetails(transaction) {
    const details = document.getElementById('transactionDetails');
    details.innerHTML = `
        <div class="details-header">
            <h4>${transaction.type}</h4>
            <span class="amount ${transaction.amount < 0 ? 'negative' : 'positive'}">
                $${Math.abs(transaction.amount).toFixed(2)}
            </span>
        </div>
        <div class="details-content">
            <div class="detail-row">
                <span>Date:</span>
                <span>${new Date(transaction.created_at).toLocaleString()}</span>
            </div>
            <div class="detail-row">
                <span>Description:</span>
                <span>${transaction.description}</span>
            </div>
            ${transaction.recipient ? `
            <div class="detail-row">
                <span>Recipient:</span>
                <span>${transaction.recipient}</span>
            </div>
            ` : ''}
            ${transaction.reference ? `
            <div class="detail-row">
                <span>Reference:</span>
                <span>${transaction.reference}</span>
            </div>
            ` : ''}
            <div class="detail-row">
                <span>Status:</span>
                <span class="status-${transaction.status}">${transaction.status}</span>
            </div>
        </div>
    `;

    new bootstrap.Modal(document.getElementById('transactionDetailsModal')).show();
}

// Add filter functionality
const typeFilter = document.getElementById('typeFilter');
const dateFilter = document.getElementById('dateFilter');
const applyFilters = document.getElementById('applyFilters');
const transactionItems = document.querySelectorAll('.transaction-item');

applyFilters.addEventListener('click', () => {
    const type = typeFilter.value;
    const date = dateFilter.value;
    
    transactionItems.forEach(item => {
        const typeMatch = type === 'all' || item.dataset.type === type;
        const dateMatch = !date || new Date(item.querySelector('.transaction-date').textContent) >= new Date(date);
        
        item.style.display = typeMatch && dateMatch ? 'grid' : 'none';
    });
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
