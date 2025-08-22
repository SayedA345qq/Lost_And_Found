<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Claims on My Posts') }}
            </h2>
            <a href="{{ route('claims.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                My Claims
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($claims->count() > 0)
                <div class="space-y-6">
                    @foreach($claims as $claim)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            <a href="{{ route('posts.show', $claim->post) }}" class="hover:text-blue-600">
                                                {{ $claim->post->title }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                       {{ $claim->post->type === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($claim->post->type) }}
                                            </span>
                                            <span>{{ ucfirst($claim->post->category) }}</span>
                                            <span>{{ $claim->post->location }}</span>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4 mb-3">
                                            <p class="font-medium text-gray-900 mb-1">Claim from {{ $claim->user->name }}</p>
                                            <p class="text-gray-700 mb-2">{{ $claim->message }}</p>
                                            @if($claim->contact_info)
                                                <p class="text-sm text-gray-600">Contact: {{ $claim->contact_info }}</p>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500">Submitted {{ $claim->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="ml-4 flex flex-col items-end space-y-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                   {{ $claim->status === 'accepted' ? 'bg-green-100 text-green-800' : 
                                                      ($claim->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
                                        
                                        @if($claim->status === 'pending')
                                            <div class="flex space-x-2">
                                                <form method="POST" action="{{ route('claims.accept', $claim) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            onclick="return confirm('Are you sure you want to accept this claim? This will mark your post as resolved and reject all other pending claims.')"
                                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Accept
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('claims.reject', $claim) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            onclick="return confirm('Are you sure you want to reject this claim?')"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($claim->status === 'accepted')
                                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                        <div class="flex">
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="ml-3">
                                                <p class="text-sm text-green-800">
                                                    You have accepted this claim. Your post has been marked as resolved. You can contact {{ $claim->user->name }} to arrange the return.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($claim->status === 'rejected')
                                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                        <div class="flex">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="ml-3">
                                                <p class="text-sm text-red-800">
                                                    You have rejected this claim.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $claims->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No claims received</h3>
                        <p class="mt-1 text-sm text-gray-500">No one has claimed any of your found items yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('posts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Post Found Item
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>