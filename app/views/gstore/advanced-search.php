<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="advanced-search-container">
    <div class="container">
        <div class="search-header">
            <h1>Advanced Search</h1>
            <p>Find exactly what you're looking for</p>
        </div>

        <div class="search-filters">
            <form id="searchForm" onsubmit="performSearch(event)">
                <!-- Basic Search -->
                <div class="search-section">
                    <h3>Basic Search</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="keywords" class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="keywords" name="keywords" 
                                   placeholder="Search by product name, description, or features">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categories" class="form-label">Categories</label>
                            <select class="form-select" id="categories" name="categories[]" multiple>
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="search-section">
                    <h3>Price Range</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="minPrice" class="form-label">Minimum Price</label>
                            <input type="number" class="form-control" id="minPrice" name="minPrice" 
                                   min="0" step="0.01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="maxPrice" class="form-label">Maximum Price</label>
                            <input type="number" class="form-control" id="maxPrice" name="maxPrice" 
                                   min="0" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Brand and Condition -->
                <div class="search-section">
                    <h3>Brand & Condition</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="brands" class="form-label">Brands</label>
                            <select class="form-select" id="brands" name="brands[]" multiple>
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo htmlspecialchars($brand['id']); ?>">
                                    <?php echo htmlspecialchars($brand['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Condition</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="new" name="condition[]" value="new">
                                <label class="form-check-label" for="new">New</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="used" name="condition[]" value="used">
                                <label class="form-check-label" for="used">Used</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="refurbished" name="condition[]" value="refurbished">
                                <label class="form-check-label" for="refurbished">Refurbished</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specifications -->
                <div class="search-section">
                    <h3>Specifications</h3>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="processor" class="form-label">Processor</label>
                            <select class="form-select" id="processor" name="processor[]" multiple>
                                <option value="">Any Processor</option>
                                <?php foreach ($processors as $processor): ?>
                                <option value="<?php echo htmlspecialchars($processor['id']); ?>">
                                    <?php echo htmlspecialchars($processor['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ram" class="form-label">RAM</label>
                            <select class="form-select" id="ram" name="ram[]" multiple>
                                <option value="">Any RAM</option>
                                <option value="4">4GB</option>
                                <option value="8">8GB</option>
                                <option value="16">16GB</option>
                                <option value="32">32GB</option>
                                <option value="64">64GB</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="storage" class="form-label">Storage</label>
                            <select class="form-select" id="storage" name="storage[]" multiple>
                                <option value="">Any Storage</option>
                                <option value="256">256GB</option>
                                <option value="512">512GB</option>
                                <option value="1000">1TB</option>
                                <option value="2000">2TB</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="search-section">
                    <h3>Sort Results</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sortBy" class="form-label">Sort By</label>
                            <select class="form-select" id="sortBy" name="sortBy">
                                <option value="relevance">Relevance</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="rating">Rating: High to Low</option>
                                <option value="newest">Newest First</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="resultsPerPage" class="form-label">Results Per Page</label>
                            <select class="form-select" id="resultsPerPage" name="resultsPerPage">
                                <option value="12">12 per page</option>
                                <option value="24">24 per page</option>
                                <option value="48">48 per page</option>
                                <option value="96">96 per page</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="search-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fa fa-refresh"></i> Reset Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="search-results" id="searchResults">
            <h2>Search Results</h2>
            <div class="results-grid">
                <!-- Results will be populated here via AJAX -->
            </div>
            <div class="pagination">
                <!-- Pagination will be populated here -->
            </div>
        </div>
    </div>
</div>

<style>
.advanced-search-container {
    padding: 40px 0;
}

.search-header {
    text-align: center;
    margin-bottom: 50px;
}

.search-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.search-filters {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 40px;
}

.search-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.search-section:last-child {
    border-bottom: none;
    margin-bottom: 20px;
    padding-bottom: 0;
}

.search-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.search-actions {
    display: flex;
    gap: 20px;
    margin-top: 30px;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.search-result-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.search-result-item:hover {
    transform: translateY(-5px);
}

.search-result-image {
    width: 100%;
    height: 200px;
    margin-bottom: 20px;
    border-radius: 5px;
    overflow: hidden;
}

.search-result-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.search-result-details h3 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.search-result-price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.2em;
    margin-bottom: 15px;
}

.search-result-meta {
    color: #666;
    font-size: 0.9em;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 30px;
}

.pagination button {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
    cursor: pointer;
}

.pagination button:hover {
    background: #f8f9fa;
}

.pagination button.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

@media (max-width: 768px) {
    .search-filters {
        padding: 20px;
    }
    
    .search-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .search-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
    }
    
    .results-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function performSearch(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('/gstore/search', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResults(data.results);
            updatePagination(data.pagination);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error performing search');
    });
}

function resetFilters() {
    document.getElementById('searchForm').reset();
    performSearch(event);
}

function displayResults(results) {
    const resultsGrid = document.querySelector('.results-grid');
    resultsGrid.innerHTML = results.map(result => `
        <div class="search-result-item" data-id="${result.id}">
            <div class="search-result-image">
                <img src="${result.image}" alt="${result.name}">
            </div>
            <div class="search-result-details">
                <h3>${result.name}</h3>
                <div class="search-result-price">
                    $${result.price.toFixed(2)}
                    ${result.discount ? `<span class="original-price">$${result.original_price.toFixed(2)}</span>` : ''}
                </div>
                <div class="search-result-meta">
                    <span class="brand">${result.brand}</span>
                    <span class="condition">${result.condition}</span>
                    <span class="rating">
                        ${Array(result.rating).fill('<i class="fa fa-star text-warning"></i>').join('')}
                        ${Array(5 - result.rating).fill('<i class="fa fa-star text-muted"></i>').join('')}
                    </span>
                </div>
                <div class="search-result-actions">
                    <button class="btn btn-outline-primary" onclick="addToCart(${result.id})">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline-secondary" onclick="addToWishlist(${result.id})">
                        <i class="fa fa-heart"></i> Add to Wishlist
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function updatePagination(pagination) {
    const paginationContainer = document.querySelector('.pagination');
    paginationContainer.innerHTML = `
        ${pagination.pages.map((page, index) => `
            <button class="${page === pagination.currentPage ? 'active' : ''}" 
                    onclick="goToPage(${page})">
                ${page}
            </button>
        `).join('')}
    `;
}

function goToPage(page) {
    const formData = new FormData(document.getElementById('searchForm'));
    formData.append('page', page);
    
    fetch('/gstore/search', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayResults(data.results);
            updatePagination(data.pagination);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error navigating to page');
    });
}

// Add to cart functionality
function addToCart(productId) {
    fetch(`/gstore/add-to-cart/${productId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}

// Add to wishlist functionality
function addToWishlist(productId) {
    fetch(`/gstore/add-to-wishlist/${productId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to wishlist');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to wishlist');
    });
}

// Initialize select2 for multiple selects
$(document).ready(function() {
    $('#categories').select2({
        placeholder: 'Select categories',
        allowClear: true
    });
    
    $('#brands').select2({
        placeholder: 'Select brands',
        allowClear: true
    });
    
    $('#processor').select2({
        placeholder: 'Select processor',
        allowClear: true
    });
    
    $('#ram').select2({
        placeholder: 'Select RAM',
        allowClear: true
    });
    
    $('#storage').select2({
        placeholder: 'Select storage',
        allowClear: true
    });
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
