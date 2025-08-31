<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage My Posts') }}
            </h2>
            <a href="{{ route('posts.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Post
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('posts.my-posts') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" 
                                       name="search" 
                                       id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Title, description, location..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Types</option>
                                    <option value="lost" {{ request('type') === 'lost' ? 'selected' : '' }}>Lost</option>
                                    <option value="found" {{ request('type') === 'found' ? 'selected' : '' }}>Found</option>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                            {{ ucfirst($category) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                                <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Title</option>
                                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                            <a href="{{ route('posts.my-posts') }}" class="text-gray-600 hover:text-gray-900">
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($posts->count() > 0)
                        <!-- Bulk Actions -->
                        <div class="mb-6 flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Select All</span>
                                </label>
                                <button type="button" 
                                        id="bulk-delete-btn" 
                                        onclick="handleBulkDelete()"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    Delete Selected
                                </button>
                                <span id="selected-count" class="text-sm text-gray-600">0 selected</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                Total: {{ $posts->total() }} posts
                            </div>
                        </div>

                        <!-- Posts List -->
                        <form id="bulk-delete-form" method="POST" action="{{ route('posts.bulk-delete') }}">
                            @csrf
                            @method('DELETE')
                            
                            <div class="space-y-4">
                                @foreach($posts as $post)
                                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex items-start space-x-4">
                                            <!-- Checkbox -->
                                            <div class="flex-shrink-0 pt-1">
                                                <input type="checkbox" 
                                                       name="post_ids[]" 
                                                       value="{{ $post->id }}" 
                                                       class="post-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>

                                            <!-- Post Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <h4 class="font-medium text-gray-900 truncate">
                                                        <a href="{{ route('posts.show', $post) }}" class="hover:text-blue-600">
                                                            {{ $post->title }}
                                                        </a>
                                                    </h4>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                               {{ $post->type === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ ucfirst($post->type) }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                               {{ $post->status === 'resolved' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $post->status)) }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ ucfirst($post->category) }}
                                                    </span>
                                                </div>
                                                
                                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($post->description, 150) }}</p>
                                                
                                                <div class="flex items-center text-xs text-gray-500 space-x-4">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $post->location }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $post->created_at->diffForHumans() }}
                                                    </span>
                                                    @if($post->type === 'found')
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                            </svg>
                                                            {{ $post->claims->count() }} claims
                                                        </span>
                                                    @else
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l6.586 6.586a2 2 0 002.828 0l6.586-6.586A2 2 0 0019.414 5H4.828a2 2 0 00-1.414 2z"></path>
                                                            </svg>
                                                            {{ $post->foundNotifications ? $post->foundNotifications->count() : 0 }} found notifications
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex-shrink-0 flex space-x-2">
                                                <a href="{{ route('posts.show', $post) }}" 
                                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    View
                                                </a>
                                                <a href="{{ route('posts.edit', $post) }}" 
                                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Edit
                                                </a>
                                                <button type="button" 
                                                        onclick="confirmDeletePost({{ $post->id }}, '{{ addslashes($post->title) }}')"
                                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['search', 'status', 'type', 'category']))
                                    Try adjusting your filters or <a href="{{ route('posts.my-posts') }}" class="text-blue-600 hover:text-blue-900">clear all filters</a>.
                                @else
                                    Get started by creating your first post.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('posts.create') }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Create Post
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Select All functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.post-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        // Individual checkbox functionality
        document.querySelectorAll('.post-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.post-checkbox');
            const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCount = document.getElementById('selected-count');
            const selectAll = document.getElementById('select-all');

            // Update selected count
            selectedCount.textContent = `${checkedBoxes.length} selected`;

            // Enable/disable bulk delete button
            bulkDeleteBtn.disabled = checkedBoxes.length === 0;

            // Update select all checkbox state
            if (checkedBoxes.length === 0) {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            } else if (checkedBoxes.length === checkboxes.length) {
                selectAll.indeterminate = false;
                selectAll.checked = true;
            } else {
                selectAll.indeterminate = true;
                selectAll.checked = false;
            }
        }

        // Bulk delete functionality
        function handleBulkDelete() {
            const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
            if (checkedBoxes.length === 0) return;
            
            confirmBulkDelete(checkedBoxes.length);
        }
    </script>
</x-app-layout>