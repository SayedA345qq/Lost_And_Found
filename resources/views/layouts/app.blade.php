<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Session Messages for JavaScript -->
        @if(session('success'))
            <meta name="success-message" content="{{ session('success') }}">
        @endif
        @if(session('error'))
            <meta name="error-message" content="{{ session('error') }}">
        @endif

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Confirmation System -->
        <script src="{{ asset('js/confirmations.js') }}"></script>
        
        <!-- Real-time notification script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const badge = document.getElementById('unread-badge');
                if (badge) {
                    const updateUrl = badge.dataset.url;
                    
                    function updateNotificationCount() {
                        fetch(updateUrl)
                            .then(response => response.json())
                            .then(data => {
                                badge.textContent = data.count;
                                badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
                            })
                            .catch(error => console.error('Error updating notification count:', error));
                    }
                    
                    // Update every 30 seconds
                    setInterval(updateNotificationCount, 30000);
                    
                    // Initial update
                    updateNotificationCount();
                }
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            
            <!-- Global Confirmation Modal -->
            <x-confirmation-modal />
        </div>
    </body>
</html>