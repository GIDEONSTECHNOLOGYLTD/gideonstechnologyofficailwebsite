<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideons Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col pt-48">
    <nav class="bg-white shadow-lg fixed w-full top-0 z-40 pt-4" x-data="{ isOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="flex items-center space-x-2">
                            <span class="font-bold text-xl text-blue-600">Gideons Technology</span>
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/services" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50 transition duration-150">Services</a>
                    <a href="/about" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50 transition duration-150">About</a>
                    <a href="/contact" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50 transition duration-150">Contact</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 transition duration-150">Dashboard</a>
                        <a href="/auth/logout" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-red-600 transition duration-150">Logout</a>
                    <?php else: ?>
                        <a href="/auth/login" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-orange-500 transition duration-150">Login</a>
                        <a href="/auth/register" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 transition duration-150">Register</a>
                    <?php endif; ?>
                </div>
                <div class="-mr-2 flex items-center md:hidden">
                    <button @click="isOpen = !isOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': isOpen, 'inline-flex': !isOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !isOpen, 'inline-flex': isOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div :class="{'block': isOpen, 'hidden': !isOpen}" class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/services" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50 transition duration-150">Services</a>
                <a href="/about" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50 transition duration-150">About</a>
                <a href="/contact" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-orange-500 hover:bg-gray-50 transition duration-150">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-orange-500 hover:bg-orange-600 transition duration-150">Dashboard</a>
                    <a href="/auth/logout" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 transition duration-150">Logout</a>
                <?php else: ?>
                    <a href="/auth/login" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-orange-500 transition duration-150">Login</a>
                    <a href="/auth/register" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-orange-500 hover:bg-orange-600 transition duration-150">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow mt-16">
        <?php echo $content ?? ''; ?>
    </main>

    <footer class="bg-white shadow-lg mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; <?php echo date('Y'); ?> Gideons Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
