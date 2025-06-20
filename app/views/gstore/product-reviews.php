<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="product-reviews-container">
    <div class="container">
        <div class="reviews-header">
            <h1>Product Reviews</h1>
            <p>Share your experience with <?php echo htmlspecialchars($product['name']); ?></p>
        </div>

        <div class="product-info">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-details">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <div class="product-rating">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fa fa-star <?php echo $i < $product['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                    <?php endfor; ?>
                    <span class="rating-count"><?php echo $product['rating_count']; ?> reviews</span>
                </div>
                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
            </div>
        </div>

        <div class="reviews-summary">
            <div class="rating-distribution">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                <div class="rating-bar">
                    <span><?php echo $i; ?> stars</span>
                    <div class="progress">
                        <div class="progress-bar" 
                             role="progressbar" 
                             style="width: <?php echo ($product['ratings'][$i] ?? 0) * 100 / $product['rating_count']; ?>%">
                        </div>
                    </div>
                    <span class="count"><?php echo $product['ratings'][$i] ?? 0; ?></span>
                </div>
                <?php endfor; ?>
            </div>

            <div class="write-review">
                <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
                    <i class="fa fa-pencil"></i> Write a Review
                </button>
                <?php else: ?>
                <a href="/login" class="btn btn-primary">
                    <i class="fa fa-sign-in"></i> Login to Write a Review
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="reviews-list">
            <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            <img src="<?php echo htmlspecialchars($review['avatar']); ?>" 
                                 alt="<?php echo htmlspecialchars($review['name']); ?>">
                        </div>
                        <div class="reviewer-details">
                            <h3><?php echo htmlspecialchars($review['name']); ?></h3>
                            <div class="reviewer-rating">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fa fa-star <?php echo $i < $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="review-actions">
                        <?php if (isset($_SESSION['user_id']) && $review['user_id'] == $_SESSION['user_id']): ?>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteReview(<?php echo $review['id']; ?>)">
                            <i class="fa fa-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="review-content">
                    <p><?php echo nl2br(htmlspecialchars($review['content'])); ?></p>
                    <?php if (!empty($review['images'])): ?>
                    <div class="review-images">
                        <?php foreach ($review['images'] as $image): ?>
                        <div class="review-image">
                            <img src="<?php echo htmlspecialchars($image); ?>" 
                                 alt="Review image">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="review-meta">
                    <div class="review-helpful">
                        <button class="btn btn-link" onclick="toggleHelpful(<?php echo $review['id']; ?>)">
                            <i class="fa fa-thumbs-up"></i>
                            <?php echo $review['helpful_count']; ?> found this helpful
                        </button>
                    </div>
                    <div class="review-verified">
                        <?php if ($review['verified_purchase']): ?>
                        <span class="badge bg-success">
                            Verified Purchase
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Write Review Modal -->
        <div class="modal fade" id="writeReviewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Write a Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reviewForm" onsubmit="submitReview(event)">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="">Select Rating</option>
                                    <option value="5">5 stars</option>
                                    <option value="4">4 stars</option>
                                    <option value="3">3 stars</option>
                                    <option value="2">2 stars</option>
                                    <option value="1">1 star</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Review</label>
                                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="images" class="form-label">Add Photos (Optional)</label>
                                <input type="file" class="form-control" id="images" name="images[]" 
                                       accept="image/*" multiple>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="verifiedPurchase" name="verified_purchase">
                                    <label class="form-check-label" for="verifiedPurchase">
                                        I purchased this product
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-paper-plane"></i> Submit Review
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
.product-reviews-container {
    padding: 40px 0;
}

.reviews-header {
    text-align: center;
    margin-bottom: 50px;
}

.reviews-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.product-info {
    display: flex;
    gap: 40px;
    margin-bottom: 50px;
}

.product-image {
    width: 300px;
    height: 300px;
    border-radius: 10px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.product-details h2 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f1c40f;
    margin-bottom: 15px;
}

.rating-count {
    color: #666;
}

.product-price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.5em;
}

.reviews-summary {
    background: white;
    border-radius: 10px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.rating-distribution {
    margin-bottom: 30px;
}

.rating-bar {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.progress {
    flex: 1;
    height: 10px;
    background-color: #eee;
    border-radius: 5px;
}

.progress-bar {
    background-color: #3498db;
    border-radius: 5px;
}

.write-review {
    text-align: center;
}

.review-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.reviewer-info {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.reviewer-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
}

.reviewer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.reviewer-details h3 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.reviewer-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #f1c40f;
    margin-bottom: 5px;
}

.review-date {
    color: #666;
    font-size: 0.9em;
}

.review-content {
    margin-bottom: 20px;
}

.review-images {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
    margin-top: 15px;
}

.review-image {
    width: 100px;
    height: 100px;
    border-radius: 5px;
    overflow: hidden;
}

.review-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.review-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

.review-helpful {
    color: #666;
}

.review-helpful i {
    color: #3498db;
    cursor: pointer;
}

.review-verified {
    color: #27ae60;
}

.badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

@media (max-width: 768px) {
    .product-info {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .product-image {
        width: 200px;
        height: 200px;
    }
    
    .reviews-summary {
        padding: 20px;
    }
    
    .review-item {
        padding: 15px;
    }
    
    .review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .review-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<script>
function submitReview(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    
    fetch('/gstore/submit-review', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Review submitted successfully');
            window.location.reload();
        } else {
            alert(data.message || 'Error submitting review');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting review');
    });
}

function toggleHelpful(reviewId) {
    fetch(`/gstore/toggle-helpful/${reviewId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const button = document.querySelector(`[onclick="toggleHelpful(${reviewId})"]`);
            button.textContent = `✓ ${data.helpful_count} found this helpful`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating helpful count');
    });
}

function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review?')) {
        fetch(`/gstore/delete-review/${reviewId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting review');
        });
    }
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
