<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-6xl font-bold text-gray-900 mb-4">{{ config('app.name') }}</h1>
            <p class="text-xl text-gray-600 mb-8">
                Help reunite people with their lost belongings and find owners for found items.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-red-500 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.5-.816-6.207-2.175.168-.288.336-.576.504-.864C7.798 10.64 9.798 10 12 10s4.202.64 5.703 1.961c.168.288.336.576.504.864A7.962 7.962 0 0112 15z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Lost Something?</h3>
                    <p class="text-gray-600 mb-4">Post details about your lost item and let the community help you find it.</p>
                    <a href="{{ route('posts.index', ['type' => 'lost']) }}" class="text-red-600 hover:text-red-800 font-medium">
                        View Lost Items →
                    </a>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-green-500 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Found Something?</h3>
                    <p class="text-gray-600 mb-4">Help return found items to their rightful owners by posting them here.</p>
                    <a href="{{ route('posts.index', ['type' => 'found']) }}" class="text-green-600 hover:text-green-800 font-medium">
                        View Found Items →
                    </a>
                </div>
            </div>
            
            <div class="space-y-4">
                <a href="{{ route('posts.index') }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-200">
                    Browse All Items
                </a>
                
                @auth
                    <div class="mt-4">
                        <a href="{{ route('posts.create') }}" 
                           class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg mr-4">
                            Post New Item
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                            Dashboard
                        </a>
                    </div>
                @else
                    <div class="mt-4 space-x-4">
                        <a href="{{ route('login') }}" 
                           class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="inline-block border border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>