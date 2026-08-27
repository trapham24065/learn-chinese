import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import HanziWriter from 'hanzi-writer';

window.Alpine = Alpine;
window.lucide = { createIcons, icons };
window.HanziWriter = HanziWriter;

// Safe icon rendering helper
function renderIcons() {
    createIcons({ icons });
}

// Global helper so Alpine or any script can manually refresh icons if needed
window.refreshIcons = renderIcons;

// Render icons on initial DOM load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderIcons);
} else {
    renderIcons();
}

// Re-render once when Alpine initializes
document.addEventListener('alpine:initialized', renderIcons);

Alpine.start();