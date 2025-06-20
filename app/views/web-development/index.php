<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="web-development-container">
    <div class="container">
        <div class="web-header">
            <h1>Web Development Services</h1>
            <p>Transform your ideas into powerful web applications</p>
        </div>

        <div class="service-highlights">
            <div class="row">
                <div class="col-md-3">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fa fa-code"></i>
                        </div>
                        <h3>Custom Development</h3>
                        <p>Tailored solutions for your unique needs</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fa fa-mobile"></i>
                        </div>
                        <h3>Responsive Design</h3>
                        <p>Perfect on all devices</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fa fa-shield"></i>
                        </div>
                        <h3>Secure Code</h3>
                        <p>Protection against threats</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <h3>Fast Delivery</h3>
                        <p>Quick turnaround times</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="featured-services">
            <h2>Featured Services</h2>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fa <?php echo htmlspecialchars($service['icon']); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="service-features">
                        <?php foreach ($service['features'] as $feature): ?>
                        <span class="feature-item">
                            <i class="fa fa-check"></i>
                            <?php echo htmlspecialchars($feature); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="service-actions">
                        <span class="price">Starting from $<?php echo htmlspecialchars($service['price']); ?></span>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quoteModal">
                            <i class="fa fa-envelope"></i> Get Quote
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="portfolio-section">
            <h2>Our Portfolio</h2>
            <div class="portfolio-filters">
                <button class="btn btn-outline-primary active" data-filter="all">All Projects</button>
                <?php foreach ($categories as $category): ?>
                <button class="btn btn-outline-primary" data-filter="<?php echo htmlspecialchars($category['slug']); ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="portfolio-grid">
                <?php foreach ($recentProjects as $project): ?>
                <div class="portfolio-item" data-category="<?php echo htmlspecialchars($project['category_slug']); ?>">
                    <div class="portfolio-image">
                        <img src="<?php echo htmlspecialchars($project['thumbnail']); ?>" 
                             alt="<?php echo htmlspecialchars($project['name']); ?>">
                        <div class="portfolio-overlay">
                            <h3><?php echo htmlspecialchars($project['name']); ?></h3>
                            <p><?php echo htmlspecialchars($project['category_name']); ?></p>
                            <a href="/web-development/project/<?php echo htmlspecialchars($project['id']); ?>" 
                               class="btn btn-primary">
                                View Project
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quote Request Modal -->
        <div class="modal fade" id="quoteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request a Quote</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="quoteForm" onsubmit="submitQuote(event)">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="project_type" class="form-label">Project Type</label>
                                <select class="form-select" id="project_type" name="project_type" required>
                                    <option value="">Select Project Type</option>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service['id']); ?>">
                                        <?php echo htmlspecialchars($service['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Project Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="budget" class="form-label">Budget Range</label>
                                    <input type="number" class="form-control" id="budget" name="budget" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="timeline" class="form-label">Timeline</label>
                                    <select class="form-select" id="timeline" name="timeline" required>
                                        <option value="">Select Timeline</option>
                                        <option value="2">2-4 weeks</option>
                                        <option value="4">4-8 weeks</option>
                                        <option value="8">8-12 weeks</option>
                                        <option value="12">12+ weeks</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-paper-plane"></i> Submit Request
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
.web-development-container {
    padding: 40px 0;
}

.web-header {
    text-align: center;
    margin-bottom: 50px;
}

.web-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.highlight-card {
    text-align: center;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.highlight-card:hover {
    transform: translateY(-5px);
}

.highlight-icon {
    font-size: 2.5em;
    color: #3498db;
    margin-bottom: 20px;
}

.highlight-card h3 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
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
    color: #2c3e50;
    margin-bottom: 15px;
}

.service-features {
    margin: 20px 0;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #666;
    margin-bottom: 10px;
}

.portfolio-section {
    margin-top: 50px;
}

.portfolio-filters {
    margin-bottom: 30px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.portfolio-item {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
}

.portfolio-image {
    position: relative;
    height: 250px;
}

.portfolio-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.portfolio-overlay {
    position: absolute;
    bottom: -100%;
    left: 0;
    right: 0;
    background: rgba(52, 152, 219, 0.9);
    color: white;
    padding: 20px;
    transition: bottom 0.3s;
}

.portfolio-item:hover .portfolio-overlay {
    bottom: 0;
}

.portfolio-item:hover .portfolio-image img {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .web-development-container {
        padding: 20px 0;
    }
    
    .highlight-card {
        padding: 20px;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .portfolio-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function submitQuote(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    
    fetch('/web-development/request-quote', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Quote request submitted successfully. We will contact you soon.');
            document.getElementById('quoteForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('quoteModal')).hide();
        } else {
            alert(data.message || 'Error submitting quote request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting quote request');
    });
}

// Portfolio filtering
const portfolioItems = document.querySelectorAll('.portfolio-item');
const portfolioFilters = document.querySelectorAll('[data-filter]');

portfolioFilters.forEach(filter => {
    filter.addEventListener('click', () => {
        portfolioFilters.forEach(f => f.classList.remove('active'));
        filter.classList.add('active');
        
        const category = filter.dataset.filter;
        portfolioItems.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
