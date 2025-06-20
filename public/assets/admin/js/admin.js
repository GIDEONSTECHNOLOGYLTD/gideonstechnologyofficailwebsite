/**
 * Admin Panel JavaScript
 * Handles sidebar toggle, dropdowns, and other interactive elements
 */

document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const adminWrapper = document.querySelector('.admin-wrapper');
    
    if (sidebarToggle && adminWrapper) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            adminWrapper.classList.toggle('sidebar-collapsed');
            
            // Save sidebar state in localStorage
            const isCollapsed = adminWrapper.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Load saved sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            adminWrapper.classList.add('sidebar-collapsed');
        }
    }
    
    // Initialize dropdowns
    const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdownMenu.classList.toggle('show');
                
                // Close other open dropdowns
                document.querySelectorAll('.dropdown-menu.show')
                    .forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.matches('[data-toggle="dropdown"]')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
    
    // Toggle password visibility
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input && input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else if (input) {
                input.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Handle sidebar active state
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            
            // Expand parent dropdown if exists
            const parentDropdown = link.closest('.dropdown-menu');
            if (parentDropdown) {
                parentDropdown.classList.add('show');
                const dropdownToggle = parentDropdown.previousElementSibling;
                if (dropdownToggle && dropdownToggle.classList.contains('dropdown-toggle')) {
                    dropdownToggle.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
    
    // Initialize form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Handle file input preview
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Choose file';
            const label = this.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
            
            // Image preview
            if (this.files && this.files[0] && this.files[0].type.match('image.*')) {
                const reader = new FileReader();
                const preview = this.closest('.form-group')?.querySelector('.image-preview');
                
                if (preview) {
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">`;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
});

// Helper function to show loading state
function showLoading(button, text = 'Loading...') {
    const btn = button;
    btn.disabled = true;
    btn.dataset.originalHtml = btn.innerHTML;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${text}`;
}

// Helper function to hide loading state
function hideLoading(button) {
    const btn = button;
    btn.disabled = false;
    btn.innerHTML = btn.dataset.originalHtml || '';
}

// Helper function to show toast notifications
function showToast(type, message, title = '') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${title ? `<strong>${title}</strong><br>` : ''}
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    
    // Simple show/hide animation
    setTimeout(() => {
        toastEl.style.opacity = '1';
        toastEl.style.transition = 'opacity 0.5s';
    }, 100);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        toastEl.style.opacity = '0';
        setTimeout(() => {
            toastEl.remove();
        }, 500);
    }, 5000);
}

// Export functions to global scope
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showToast = showToast;
