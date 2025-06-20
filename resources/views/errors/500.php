<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>
        <p class="text-xl text-gray-600 mb-4">Internal Server Error</p>
        <?php if (isset($error)): ?>
            <p class="text-gray-500 mb-8"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <a href="/" class="text-green-500 hover:text-green-600">Return Home →</a>
    </div>
</div>
