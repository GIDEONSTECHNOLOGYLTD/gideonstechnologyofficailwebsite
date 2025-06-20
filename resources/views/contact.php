<?php
$success = $success ?? false;
$errors = $errors ?? [];
?>

<div class="container mx-auto px-4 py-16 mt-16">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8 text-center">Contact Us</h1>
        <p class="text-xl text-gray-600 mb-12 text-center">Get in touch with our team for any inquiries or support.</p>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8 text-center">
            <h2 class="text-2xl font-semibold text-green-700 mb-2">Thank You!</h2>
            <p class="text-green-600">Your message has been sent successfully. We'll get back to you shortly.</p>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
            <h3 class="text-red-700 font-semibold mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-red-600">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/contact" method="POST" enctype="multipart/form-data" class="bg-white shadow-lg rounded-lg p-8">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" id="phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                    <textarea name="message" id="message" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.doc,.docx,.txt"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Max file size: 5MB. Allowed types: PDF, DOC, DOCX, TXT</p>
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full bg-orange-500 text-white py-3 px-6 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-150">
                        Send Message
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                <p class="text-gray-600">
                    Email: info@gideonstechnology.com<br>
                    Phone: +233 55 823 4403
                </p>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Office Location</h3>
                <p class="text-gray-600">
                    123 Tech Street<br>
                    Accra, Ghana
                </p>
            </div>
        </div>
    </div>
</div>
