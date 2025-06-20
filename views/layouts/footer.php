    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="text-white">About Us</h5>
                    <p class="text-muted">Gideon's Technology provides cutting-edge tech solutions for businesses and individuals, including web development, repair services, and fintech solutions.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-white">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/services" class="text-muted">Services</a></li>
                        <li><a href="/blog" class="text-muted">Blog</a></li>
                        <li><a href="/contact" class="text-muted">Contact Us</a></li>
                        <li><a href="/about" class="text-muted">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-white">Contact</h5>
                    <address class="text-muted">
                        <p><i class="fas fa-map-marker-alt me-2"></i> Kumasi, Ashanti Region, Ghana</p>
                        <p><i class="fas fa-phone me-2"></i> +233-20-285-0251</p>
                        <p><i class="fas fa-envelope me-2"></i> ceo@gideonstechnology.com</p>
                    </address>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row">
                <div class="col-md-8">
                    <p class="text-muted">&copy; <?= date('Y') ?> Gideon's Technology. All rights reserved.</p>
                </div>
                <div class="col-md-4">
                    <ul class="list-inline social-links float-md-end">
                        <li class="list-inline-item">
                            <a href="#" class="text-muted"><i class="fab fa-facebook"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="text-muted"><i class="fab fa-twitter"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="text-muted"><i class="fab fa-linkedin"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Add any custom JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // Enable tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>