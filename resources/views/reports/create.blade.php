<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Report {{ ucfirst($type) }}</h3>
                        <p class="text-gray-600">
                            Help us maintain a safe and respectful community by reporting inappropriate content.
                            All reports are reviewed and appropriate action will be taken.
                        </p>
                    </div>

                    <!-- Item Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">You are reporting:</h4>
                        @if($type === 'post')
                            <div class="flex items-start space-x-4">
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ asset('storage/' . $item->images[0]) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-16 h-16 object-cover rounded-lg">
                                @endif
                                <div>
                                    <p class="font-medium">{{ $item->title }}</p>
                                    <p class="text-sm text-gray-600">{{ Str::limit($item->description, 100) }}</p>
                                    <p class="text-xs text-gray-500">By {{ $item->user->name }}</p>
                                </div>
                            </div>
                        @elseif($type === 'comment')
                            <div>
                                <p class="text-sm text-gray-700">{{ $item->message }}</p>
                                <p class="text-xs text-gray-500 mt-1">By {{ $item->user->name }} on {{ $item->post->title }}</p>
                            </div>
                        @elseif($type === 'message')
                            <div>
                                <p class="text-sm text-gray-700">{{ Str::limit($item->message, 100) }}</p>
                                <p class="text-xs text-gray-500 mt-1">From {{ $item->sender->name }}</p>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="reportable_type" value="{{ $reportableType }}">
                        <input type="hidden" name="reportable_id" value="{{ $item->id }}">

                        <!-- Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for reporting *</label>
                            <div class="space-y-2">
                                @foreach($reasons as $key => $label)
                                    <label class="flex items-center">
                                        <input type="radio" name="reason" value="{{ $key }}" {{ old('reason') === $key ? 'checked' : '' }} required
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Additional Details (Optional)</label>
                            <textarea name="description" id="description" rows="4"
                                      placeholder="Please provide any additional context or details about why you're reporting this content..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">This information helps our moderators understand the issue better.</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warning -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Please note:</strong> False reports may result in restrictions on your account. 
                                        Only report content that genuinely violates our community guidelines.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-between">
                            <button type="button" onclick="history.back()" 
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>