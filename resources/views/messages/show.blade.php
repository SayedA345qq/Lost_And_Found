<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Conversation with {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-600">
                    About: <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-900">{{ $post->title }}</a>
                </p>
            </div>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('messages.clear-conversation', [$post, $user]) }}" class="inline" onsubmit="return false;">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            onclick="confirmClearConversation(this.form)"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Clear Conversation
                    </button>
                </form>
                <a href="{{ route('messages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Messages
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Post Reference -->
                <div class="p-4 bg-gray-50 border-b">
                    <div class="flex items-center space-x-4">
                        @if($post->images && count($post->images) > 0)
                            <img src="{{ asset('storage/' . $post->images[0]) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-16 h-16 object-cover rounded-lg">
                        @endif
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $post->title }}</h3>
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                           {{ $post->type === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($post->type) }}
                                </span>
                                <span>{{ $post->location }}</span>
                                <span>{{ $post->date_lost_found->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="p-6">
                    @if($messages->count() > 0)
                        <div class="space-y-4 mb-6" style="max-height: 400px; overflow-y: auto;" id="messagesContainer">
                            @foreach($messages as $message)
                                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-900' }}">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                                {{ $message->sender->name }}
                                            </span>
                                            <span class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }} ml-2">
                                                {{ $message->created_at->format('M d, g:i A') }}
                                            </span>
                                        </div>
                                        <p class="text-sm">{{ $message->message }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No messages yet. Start the conversation!</p>
                        </div>
                    @endif

                    <!-- Message Form -->
                    <form method="POST" action="{{ route('messages.store', [$post, $user]) }}" class="border-t pt-4">
                        @csrf
                        <div class="flex space-x-4">
                            <textarea name="message" rows="3" placeholder="Type your message..." required
                                      class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('message') border-red-500 @enderror"></textarea>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Send
                            </button>
                        </div>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</x-app-layout>