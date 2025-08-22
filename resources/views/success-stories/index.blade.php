<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Success Stories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-green-400 to-blue-500 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-white text-center">
                    <h1 class="text-4xl font-bold mb-4">🎉 Success Stories</h1>
                    <p class="text-xl mb-6">Celebrating reunions and the power of community</p>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-center">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $stats['resolved_posts'] }}</div>
                            <div class="text-sm">Items Reunited</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $stats['success_rate'] }}%</div>
                            <div class="text-sm">Success Rate</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $stats['lost_items_resolved'] }}</div>
                            <div class="text-sm">Lost Items Found</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $stats['found_items_resolved'] }}</div>
                            <div class="text-sm">Found Items Claimed</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $stats['active_users'] }}</div>
                            <div class="text-sm">Active Helpers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Stories Grid -->
            @if($successStories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($successStories as $story)
                        @php
                            $acceptedClaim = $story->claims->where('status', 'accepted')->first();
                            $acceptedFoundNotification = $story->foundNotifications->where('status', 'accepted')->first();
                        @endphp
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                            <div class="relative">
                                @if($story->images && count($story->images) > 0)
                                    <img src="{{ asset('storage/' . $story->images[0]) }}" 
                                         alt="{{ $story->title }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Success Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ✅ Reunited
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="flex items-center space-x-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                               {{ $story->type === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($story->type) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($story->category) }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $story->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($story->description, 100) }}</p>
                                
                                <div class="border-t pt-4">
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $story->user->name }}</p>
                                            <p>{{ $story->location }}</p>
                                        </div>
                                        
                                        @if($story->type === 'found' && $acceptedClaim)
                                            <!-- Found item that was claimed -->
                                            <div class="text-right">
                                                <p class="font-medium text-green-600">Claimed by</p>
                                                <p>{{ $acceptedClaim->user->name }}</p>
                                            </div>
                                        @elseif($story->type === 'lost' && $acceptedFoundNotification)
                                            <!-- Lost item that was found -->
                                            <div class="text-right">
                                                <p class="font-medium text-orange-600">Found by</p>
                                                <p>{{ $acceptedFoundNotification->finder->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="text-xs text-gray-400">
                                            Resolved {{ $story->updated_at->diffForHumans() }}
                                        </span>
                                        <a href="{{ route('success-stories.show', $story) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Read Story →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $successStories->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No success stories yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Be the first to create a success story by helping someone find their lost item!</p>
                        <div class="mt-6">
                            <a href="{{ route('posts.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Browse Items
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Call to Action -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Help Create More Success Stories!</h3>
                <p class="text-blue-700 mb-4">Every item returned is a story of community kindness. Join us in reuniting people with their belongings.</p>
                <div class="space-x-4">
                    <a href="{{ route('posts.index', ['type' => 'lost']) }}" 
                       class="inline-block bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Help Find Lost Items
                    </a>
                    <a href="{{ route('posts.index', ['type' => 'found']) }}" 
                       class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Claim Found Items
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>