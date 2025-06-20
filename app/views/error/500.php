<?php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Server Error - Gideons Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
        <h1 class="text-4xl font-bold text-red-600 mb-4">500</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Internal Server Error</h2>
        <p class="text-gray-600 mb-6">Something went wrong on our end. We're working to fix it.</p>
        <a href="/" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-colors">
            Go Home
        </a>
    </div>
</body>
</html>
