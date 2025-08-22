<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Success Story') }}
            </h2>
            <a href="{{ route('success-stories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Success Stories
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Success Banner -->
            <div class="bg-gradient-to-r from-green-400 to-blue-500 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-white text-center">
                    <div class="text-6xl mb-4">🎉</div>
                    <h1 class="text-3xl font-bold mb-2">Success Story</h1>
                    <p class="text-xl">{{ $post->title }} has been reunited!</p>
                </div>
            </div>

            <!-- Story Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Post Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex space-x-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                       {{ $post->type === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($post->type) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($post->category) }}
                            </span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ Resolved
                        </span>
                    </div>

                    <!-- Images -->
                    @if($post->images && count($post->images) > 0)
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($post->images as $image)
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-full h-64 object-cover rounded-lg">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Original Post</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $post->description }}</p>
                    </div>

                    <!-- Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Location</h4>
                            <p class="text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $post->location }}
                            </p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Date {{ $post->type === 'lost' ? 'Lost' : 'Found' }}</h4>
                            <p class="text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1V8a1 1 0 011-1h3z"></path>
                                </svg>
                                {{ $post->date_lost_found->format('F d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Story Details -->
            @if($acceptedClaim || $acceptedFoundNotification)
                <div class="bg-green-50 border border-green-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4">🎯 How It Was Resolved</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-white p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-900 mb-2">Original Poster</h4>
                                <p class="text-gray-700">{{ $post->user->name }}</p>
                                <p class="text-sm text-gray-500">Posted {{ $post->created_at->format('F d, Y') }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                @if($acceptedClaim)
                                    <!-- Found item that was claimed -->
                                    <h4 class="font-semibold text-gray-900 mb-2">Claimer</h4>
                                    <p class="text-gray-700">{{ $acceptedClaim->user->name }}</p>
                                    <p class="text-sm text-gray-500">Claimed {{ $acceptedClaim->created_at->format('F d, Y') }}</p>
                                @elseif($acceptedFoundNotification)
                                    <!-- Lost item that was found -->
                                    <h4 class="font-semibold text-gray-900 mb-2">Finder</h4>
                                    <p class="text-gray-700">{{ $acceptedFoundNotification->finder->name }}</p>
                                    <p class="text-sm text-gray-500">Found {{ $acceptedFoundNotification->created_at->format('F d, Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg">
                            @if($acceptedClaim)
                                <h4 class="font-semibold text-gray-900 mb-2">Claim Message</h4>
                                <p class="text-gray-700 italic">"{{ $acceptedClaim->message }}"</p>
                                @if($acceptedClaim->contact_info)
                                    <p class="text-sm text-gray-500 mt-2">Contact: {{ $acceptedClaim->contact_info }}</p>
                                @endif
                            @elseif($acceptedFoundNotification)
                                <h4 class="font-semibold text-gray-900 mb-2">Found Message</h4>
                                <p class="text-gray-700 italic">"{{ $acceptedFoundNotification->message }}"</p>
                                <p class="text-sm text-gray-500 mt-2">Found at: {{ $acceptedFoundNotification->found_location }}</p>
                                <p class="text-sm text-gray-500">Contact: {{ $acceptedFoundNotification->contact_info }}</p>
                            @endif
                        </div>

                        <div class="mt-6 p-4 bg-green-100 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-green-900">Successfully Reunited!</p>
                                    <p class="text-green-700 text-sm">Resolved on {{ $post->updated_at->format('F d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 text-sm font-bold">1</span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Item {{ $post->type === 'lost' ? 'Lost' : 'Found' }}</p>
                                <p class="text-sm text-gray-500">{{ $post->date_lost_found->format('F d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <span class="text-yellow-600 text-sm font-bold">2</span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Posted on {{ config('app.name') }}</p>
                                <p class="text-sm text-gray-500">{{ $post->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($acceptedClaim)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 text-sm font-bold">3</span>
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">Claim Submitted</p>
                                    <p class="text-sm text-gray-500">{{ $acceptedClaim->created_at->format('F d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @elseif($acceptedFoundNotification)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-orange-600 text-sm font-bold">3</span>
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">Found Notification Submitted</p>
                                    <p class="text-sm text-gray-500">{{ $acceptedFoundNotification->created_at->format('F d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 text-sm font-bold">✓</span>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">Successfully Reunited!</p>
                                <p class="text-sm text-gray-500">{{ $post->updated_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            @if($post->comments->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">💬 Community Comments</h3>
                        <div class="space-y-4">
                            @foreach($post->comments->where('is_flagged', false) as $comment)
                                <div class="border-l-4 border-gray-200 pl-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $comment->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 mt-2">{{ $comment->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Call to Action -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Inspired by this success story?</h3>
                <p class="text-blue-700 mb-4">Help create more happy endings by joining our community of helpers!</p>
                <div class="space-x-4">
                    <a href="{{ route('posts.index') }}" 
                       class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Browse Items
                    </a>
                    <a href="{{ route('success-stories.index') }}" 
                       class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        More Success Stories
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>