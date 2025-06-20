<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/config/contact.php';
require_once 'includes/db.php';
require_once 'includes/mailer.php';

$page_title = "Contact Us - " . SITE_NAME;
$meta_description = "Get in touch with Gideons Technology for all your technology needs. We're here to help!";
$meta_keywords = "contact, support, technology services, web development, fintech, IT services";

require_once 'includes/header.php';

// Get service and plan from URL parameters
$selected_service = $_GET['service'] ?? '';
$selected_plan = $_GET['plan'] ?? '';
$selected_solution = $_GET['solution'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($message)) $errors[] = "Message is required";
    if (strlen($message) > MAX_MESSAGE_LENGTH) $errors[] = "Message is too long";
    if (!empty($service) && !array_key_exists($service, SERVICES)) $errors[] = "Invalid service selected";
    
    // Handle file upload if present
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file type and size
        if (!in_array($ext, ALLOWED_FILE_TYPES)) {
            $errors[] = "Invalid file type. Allowed types: " . implode(', ', ALLOWED_FILE_TYPES);
        } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
            $errors[] = "File is too large. Maximum size allowed is " . (MAX_UPLOAD_SIZE / 1024 / 1024) . "MB";
        } else {
            // Generate safe filename
            $filename = uniqid('attachment_') . '.' . $ext;
            $uploadPath = __DIR__ . '/uploads/' . $filename;
            
            // Create uploads directory if it doesn't exist
            if (!file_exists(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0755, true);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $errors[] = "Failed to upload file";
            } else {
                $attachment = $uploadPath;
            }
        }
    }
    
    if (empty($errors)) {
        // Store in database
        $stmt = $pdo->prepare("
            INSERT INTO contact_submissions (name, email, phone, service, message, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        if ($stmt->execute([$name, $email, $phone, $service, $message])) {
            // Send email notification
            $mailer = new Mailer();
            $emailData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'service' => $service ? SERVICES[$service] : 'Not specified',
                'message' => $message
            ];
            
            if ($mailer->sendContactForm($emailData)) {
                $success = true;
            } else {
                $errors[] = "Failed to send email notification";
            }
        } else {
            $errors[] = "Failed to submit form";
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1>Contact Us</h1>
                <p class="lead">Get in touch with our team for any inquiries or support.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <h4>Thank You!</h4>
                        <p>Your message has been sent successfully. We'll get back to you shortly.</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h4>Please fix the following errors:</h4>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            <div class="invalid-feedback">Please provide your name.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            <div class="invalid-feedback">Please provide a valid email.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="service" class="form-label">Service Interested In</label>
                            <select class="form-select" id="service" name="service">
                                <option value="">Select a service...</option>
                                <option value="web-dev" <?php echo $selected_service === 'web-dev' ? 'selected' : ''; ?>>
                                    Web Development
                                </option>
                                <option value="fintech" <?php echo $selected_service === 'fintech' ? 'selected' : ''; ?>>
                                    Fintech Solutions
                                </option>
                                <option value="general-tech" <?php echo $selected_service === 'general-tech' ? 'selected' : ''; ?>>
                                    General Tech
                                </option>
                                <option value="repair" <?php echo $selected_service === 'repair' ? 'selected' : ''; ?>>
                                    Repair Services
                                </option>
                                <option value="videographics" <?php echo $selected_service === 'videographics' ? 'selected' : ''; ?>>
                                    Video & Graphics
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php 
                                echo htmlspecialchars($message ?? '');
                                if ($selected_service && $selected_plan) {
                                    echo "\n\nI'm interested in the " . htmlspecialchars($selected_plan) . " plan for " . htmlspecialchars($selected_service) . " services.";
                                }
                                if ($selected_solution) {
                                    echo "\n\nI'm interested in the " . htmlspecialchars($selected_solution) . " solution.";
                                }
                            ?></textarea>
                            <div class="invalid-feedback">Please provide your message.</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="attachment" class="form-label">Attachment (optional)</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                            <div class="form-text">Allowed file types: <?php echo implode(', ', array_map(function($type) { return str_replace(['application/', 'image/'], '', $type); }, ALLOWED_FILE_TYPES)); ?>. Max size: <?php echo MAX_FILE_SIZE / (1024 * 1024); ?>MB</div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="contact-info bg-light p-4 rounded">
                    <h3>Contact Information</h3>
                    <p>Feel free to reach out to us through any of these channels:</p>
                    
                    <div class="mb-4">
                        <h5><i class="fas fa-map-marker-alt text-primary me-2"></i> Address</h5>
                        <p class="mb-0">123 Tech Street<br>Accra, Ghana</p>
                    </div>

                    <div class="mb-4">
                        <h5><i class="fas fa-phone text-primary me-2"></i> Phone</h5>
                        <p class="mb-0">
                            <a href="tel:+233558234403" class="text-dark">+233 55 823 4403</a>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5><i class="fas fa-envelope text-primary me-2"></i> Email</h5>
                        <p class="mb-0">
                            <a href="mailto:info@gideonstechnology.com" class="text-dark">info@gideonstechnology.com</a>
                        </p>
                    </div>

                    <div class="social-links">
                        <h5><i class="fas fa-share-alt text-primary me-2"></i> Follow Us</h5>
                        <a href="#" class="text-dark me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-dark me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-dark me-2"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container-fluid p-0">
        <div class="map-container">
            <!-- Replace with your Google Maps embed code -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.9735392908837!2d-0.1870821851905456!3d5.6144165959750375!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9c7ebaeabe93%3A0x9e3b5b4716e4be69!2sAccra%2C%20Ghana!5e0!3m2!1sen!2sus!4v1650964148000!5m2!1sen!2sus"
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
