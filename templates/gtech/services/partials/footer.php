    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget about-widget">
                            <h4 class="widget-title">About Gideon's Technology</h4>
                            <p>We provide innovative technology solutions and services to help businesses and individuals excel in the digital world.</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="footer-widget links-widget">
                            <h4 class="widget-title">Services</h4>
                            <ul class="footer-links">
                                <li><a href="/services/web-development">Web Development</a></li>
                                <li><a href="/services/mobile-app-development">Mobile Apps</a></li>
                                <li><a href="/gtech/services/repair">Tech Repair</a></li>
                                <li><a href="/services/cloud-solutions">Cloud Solutions</a></li>
                                <li><a href="/services/it-consulting">IT Consulting</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="footer-widget links-widget">
                            <h4 class="widget-title">Company</h4>
                            <ul class="footer-links">
                                <li><a href="/about">About Us</a></li>
                                <li><a href="/team">Our Team</a></li>
                                <li><a href="/careers">Careers</a></li>
                                <li><a href="/blog">Blog</a></li>
                                <li><a href="/contact">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget contact-widget">
                            <h4 class="widget-title">Contact Information</h4>
                            <ul class="contact-info">
                                <li><i class="fas fa-map-marker-alt"></i> 123 Tech Avenue, Silicon Valley, CA</li>
                                <li><i class="fas fa-phone-alt"></i> (555) 123-4567</li>
                                <li><i class="fas fa-envelope"></i> support@gideons-tech.com</li>
                                <li><i class="fas fa-clock"></i> Monday - Friday: 9:00 AM - 6:00 PM</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="copyright">&copy; <?= date('Y') ?> Gideon's Technology. All Rights Reserved.</p>
                    </div>
                    <div class="col-md-6">
                        <ul class="footer-bottom-links">
                            <li><a href="/privacy-policy">Privacy Policy</a></li>
                            <li><a href="/terms-of-service">Terms of Service</a></li>
                            <li><a href="/sitemap">Sitemap</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>
    
    <!-- JavaScript Libraries -->
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/scripts.js"></script>
    
    <!-- Custom page scripts -->
    <?php if (isset($customScripts)): ?>
    <script>
        <?= $customScripts ?>
    </script>
    <?php endif; ?>
    
</body>
</html>