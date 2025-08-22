<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Claims') }}
            </h2>
            <a href="{{ route('claims.received') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Claims on My Posts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter and Sort Controls -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('claims.index') }}" class="flex flex-wrap gap-4 items-end">
                        <!-- Search -->
                        <div class="flex-1 min-w-64">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ $search }}" 
                                   placeholder="Search by post title or description..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Filter by Status -->
                        <div>
                            <label for="filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="filter" id="filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Claims</option>
                                <option value="pending" {{ $filter === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ $filter === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort</label>
                            <select name="sort" id="sort" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                        </div>

                        <!-- Clear -->
                        @if($search || $filter !== 'all' || $sort !== 'latest')
                            <div>
                                <a href="{{ route('claims.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

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
                                        <p class="text-gray-600 mb-3">{{ Str::limit($claim->message, 200) }}</p>
                                        @if($claim->contact_info)
                                            <p class="text-sm text-gray-500 mb-2">Contact: {{ $claim->contact_info }}</p>
                                        @endif
                                        <p class="text-sm text-gray-500">Submitted {{ $claim->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                   {{ $claim->status === 'accepted' ? 'bg-green-100 text-green-800' : 
                                                      ($claim->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
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
                                                    Your claim has been accepted! You can now contact the post owner to arrange pickup/return.
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
                                                    Your claim has been rejected by the post owner.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                        <div class="flex">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-800">
                                                    Your claim is pending review by the post owner.
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.5-.816-6.207-2.175.168-.288.336-.576.504-.864C7.798 10.64 9.798 10 12 10s4.202.64 5.703 1.961c.168.288.336.576.504.864A7.962 7.962 0 0112 15z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No claims yet</h3>
                        <p class="mt-1 text-sm text-gray-500">You haven't made any claims on found items yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('posts.index', ['type' => 'found']) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Browse Found Items
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>