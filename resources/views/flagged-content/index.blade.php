<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Flagged Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Information Banner -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Content Flagged by Community</h3>
                        <p class="mt-1 text-sm text-yellow-700">
                            The content below has been flagged by community members and is currently hidden from public view. 
                            You can restore content if you believe it was flagged incorrectly.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flagged Posts -->
            @if($flaggedPosts->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Flagged Posts ({{ $flaggedPosts->count() }})</h3>
                        <div class="space-y-4">
                            @foreach($flaggedPosts as $post)
                                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $post->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($post->description, 100) }}</p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-2">
                                                <span>{{ $post->flag_count }} reports</span>
                                                <span>Flagged {{ $post->updated_at->diffForHumans() }}</span>
                                                <span class="capitalize">{{ $post->type }} • {{ $post->category }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <form method="POST" action="{{ route('flagged-content.restore-post', $post) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to restore this post? This will make it visible to the public again.')"
                                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Restore
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Flagged Comments -->
            @if($flaggedComments->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Flagged Comments ({{ $flaggedComments->count() }})</h3>
                        <div class="space-y-4">
                            @foreach($flaggedComments as $comment)
                                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-gray-900">{{ $comment->message }}</p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-2">
                                                <span>{{ $comment->flag_count }} reports</span>
                                                <span>Flagged {{ $comment->updated_at->diffForHumans() }}</span>
                                                <span>On post: {{ $comment->post->title }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <form method="POST" action="{{ route('flagged-content.restore-comment', $comment) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to restore this comment?')"
                                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Restore
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Flagged Messages -->
            @if($flaggedMessages->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Flagged Messages ({{ $flaggedMessages->count() }})</h3>
                        <div class="space-y-4">
                            @foreach($flaggedMessages as $message)
                                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-gray-900">{{ Str::limit($message->message, 100) }}</p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-2">
                                                <span>{{ $message->flag_count }} reports</span>
                                                <span>Flagged {{ $message->updated_at->diffForHumans() }}</span>
                                                <span>To: {{ $message->receiver->name }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <form method="POST" action="{{ route('flagged-content.restore-message', $message) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to restore this message?')"
                                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Restore
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- No Flagged Content -->
            @if($flaggedPosts->count() === 0 && $flaggedComments->count() === 0 && $flaggedMessages->count() === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No flagged content</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any content that has been flagged by the community.</p>
                        <div class="mt-6">
                            <a href="{{ route('posts.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Browse Posts
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>