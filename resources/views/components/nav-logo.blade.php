<svg viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <defs>
        <linearGradient id="navLostGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#ef4444;stop-opacity:0.9" />
            <stop offset="100%" style="stop-color:#dc2626;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="navFoundGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.9" />
            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
        </linearGradient>
    </defs>
    
    <!-- Magnifying glass -->
    <circle cx="15" cy="20" r="8" fill="none" stroke="url(#navLostGrad)" stroke-width="2"/>
    <line x1="21" y1="26" x2="27" y2="32" stroke="url(#navLostGrad)" stroke-width="2" stroke-linecap="round"/>
    <text x="15" y="24" font-family="Arial" font-size="8" text-anchor="middle" fill="url(#navLostGrad)" font-weight="bold">?</text>
    
    <!-- Text -->
    <text x="35" y="18" font-family="system-ui, sans-serif" font-size="11" font-weight="700" fill="url(#navLostGrad)">LOST</text>
    <text x="35" y="30" font-family="system-ui, sans-serif" font-size="8" fill="currentColor" opacity="0.6">&amp;</text>
    <text x="45" y="30" font-family="system-ui, sans-serif" font-size="11" font-weight="700" fill="url(#navFoundGrad)">FOUND</text>
    
    <!-- Checkmark -->
    <circle cx="105" cy="20" r="8" fill="none" stroke="url(#navFoundGrad)" stroke-width="2"/>
    <path d="m100 20 3 3 5-5" fill="none" stroke="url(#navFoundGrad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>