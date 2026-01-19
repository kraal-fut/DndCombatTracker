<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'D&D Combat Tracker')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    <style>
        body {
            background-color: #111827;
            color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <nav class="bg-gray-800 shadow-lg border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('combats.index') }}" class="text-2xl font-bold text-red-500 hover:text-red-400 transition">
                            ⚔️ D&D Combat Tracker
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('combats.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
                        All Combats
                    </a>
                    <a href="{{ route('combats.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition shadow-lg">
                        New Combat
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @session('success')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded shadow-lg">
                {{ session('success') }}
            </div>
        </div>
    @endsession

    @session('error')
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded shadow-lg">
                {{ session('error') }}
            </div>
        </div>
    @endsession

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
</body>
</html>
