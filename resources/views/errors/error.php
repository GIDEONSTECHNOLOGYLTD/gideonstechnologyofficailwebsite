<?php
$errorCode = $errorCode ?? '404';
$errorMessage = $errorMessage ?? 'Page Not Found';
$errorDescription = $errorDescription ?? 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.';
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full space-y-8 text-center bg-white p-8 rounded-xl shadow-lg">
        <div class="space-y-4">
            <h1 class="text-8xl font-extrabold text-blue-600 mb-4 animate-pulse"><?php echo $errorCode; ?></h1>
            <h2 class="text-3xl font-bold text-gray-900 mb-4"><?php echo $errorMessage; ?></h2>
            <p class="text-lg text-gray-600 mb-8"><?php echo $errorDescription; ?></p>
            
            <div class="space-y-4">
                <a href="/" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-orange-500 hover:bg-orange-600 transition duration-150 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Return Home
                </a>
                
                <div class="flex justify-center space-x-4 mt-6">
                    <a href="/contact" class="text-blue-600 hover:text-blue-700 transition duration-150">
                        Contact Support
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="/services" class="text-blue-600 hover:text-blue-700 transition duration-150">
                        Our Services
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (isset($debug) && $debug): ?>
        <div class="mt-8 p-4 bg-gray-50 rounded-lg text-left">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Debug Information:</h3>
            <pre class="text-xs text-gray-600 overflow-auto"><?php echo htmlspecialchars($debug); ?></pre>
        </div>
        <?php endif; ?>
    </div>
</div>
