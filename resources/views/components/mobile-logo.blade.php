<div {{ $attributes->merge(['class' => 'flex items-center space-x-2']) }}>
    <div class="flex items-center space-x-1">
        <!-- Lost icon -->
        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
                <text x="11" y="15" font-size="8" text-anchor="middle" fill="currentColor">?</text>
            </svg>
        </div>
        
        <!-- Text -->
        <span class="font-bold text-gray-800">
            <span class="text-red-600">LOST</span>
            <span class="text-gray-500 text-sm">&</span>
            <span class="text-green-600">FOUND</span>
        </span>
        
        <!-- Found icon -->
        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
    </div>
</div>