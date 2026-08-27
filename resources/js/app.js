import Alpine from 'alpinejs';

window.Alpine = Alpine;

import { createIcons, icons } from 'lucide';

// Expose globally so we can re-init after Alpine DOM mutations
window.lucide = { createIcons, icons };

// Run Lucide icon replacement once initially
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Re-run after every Alpine DOM update (covers x-for, x-if, etc.)
document.addEventListener('alpine:initialized', () => {
    // MutationObserver to catch dynamic DOM changes from Alpine
    const observer = new MutationObserver(() => {
        createIcons({ icons });
    });
    observer.observe(document.body, { childList: true, subtree: true });
});

Alpine.start();

import HanziWriter from 'hanzi-writer';
window.HanziWriter = HanziWriter;