<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Goodbye</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="5;url=review.php">
</head>
<body class="bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-2xl p-10 max-w-md text-center animate-fade-in">
        <div class="text-6xl mb-4">👋</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Your account has been deleted</h1>
        <p class="text-gray-600 mb-6">
            We're sad to see you go. Redirecting to the homepage...
        </p>
        <a href="index.php" class="inline-block px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
            Go Now
        </a>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }
    </style>
</body>
</html>
