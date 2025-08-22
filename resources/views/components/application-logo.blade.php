<svg viewBox="0 0 240 60" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <defs>
        <linearGradient id="lostGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#ef4444;stop-opacity:0.8" />
            <stop offset="100%" style="stop-color:#dc2626;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="foundGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.8" />
            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
        </linearGradient>
    </defs>
    
    <!-- Lost section background -->
    <rect x="5" y="5" width="50" height="50" rx="25" fill="url(#lostGradient)" opacity="0.1"/>
    
    <!-- Magnifying glass -->
    <circle cx="30" cy="25" r="10" fill="none" stroke="url(#lostGradient)" stroke-width="2.5"/>
    <line x1="37" y1="32" x2="45" y2="40" stroke="url(#lostGradient)" stroke-width="2.5" stroke-linecap="round"/>
    
    <!-- Question mark in magnifying glass -->
    <path d="M27 21c0-1.5 1-2.5 2.5-2.5s2.5 1 2.5 2.5c0 1-0.5 1.5-1.5 2l-0.5 0.5v1.5m0 2h.01" 
          fill="none" stroke="url(#lostGradient)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
    
    <!-- Main text -->
    <g font-family="system-ui, -apple-system, sans-serif">
        <!-- LOST text -->
        <text x="70" y="25" font-size="16" font-weight="700" fill="currentColor">
            <tspan fill="url(#lostGradient)">LOST</tspan>
        </text>
        
        <!-- & symbol -->
        <text x="70" y="40" font-size="12" font-weight="400" fill="currentColor" opacity="0.6">&amp;</text>
        
        <!-- FOUND text -->
        <text x="70" y="52" font-size="16" font-weight="700" fill="currentColor">
            <tspan fill="url(#foundGradient)">FOUND</tspan>
        </text>
    </g>
    
    <!-- Found section background -->
    <rect x="185" y="5" width="50" height="50" rx="25" fill="url(#foundGradient)" opacity="0.1"/>
    
    <!-- Checkmark circle -->
    <circle cx="210" cy="30" r="10" fill="none" stroke="url(#foundGradient)" stroke-width="2.5"/>
    <path d="m205 30 3 3 6-6" fill="none" stroke="url(#foundGradient)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    
    <!-- Decorative dots -->
    <circle cx="160" cy="20" r="1.5" fill="currentColor" opacity="0.3"/>
    <circle cx="165" cy="30" r="1.5" fill="currentColor" opacity="0.4"/>
    <circle cx="160" cy="40" r="1.5" fill="currentColor" opacity="0.3"/>
</svg>