import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import HanziWriter from 'hanzi-writer';
import './notifications';
import './sound-engine';
import './tone-pinyin';
import { PetEventBus } from './pet/PetEventBus';
import { PetAudioEngine } from './pet/PetAudioEngine';
import { PetVoiceManager } from './pet/PetVoiceManager';
import { PetLifeEngine } from './pet/PetLifeEngine';
import { petFloatingCompanion } from './pet/pet-companion';

window.Alpine = Alpine;
window.HanziWriter = HanziWriter;
window.PetEventBus = PetEventBus;
window.PetAudioEngine = PetAudioEngine;
window.PetVoiceManager = PetVoiceManager;
window.PetLifeEngine = PetLifeEngine;
window.petFloatingCompanion = petFloatingCompanion;

Alpine.data('petFloatingCompanion', (cfg) => petFloatingCompanion(cfg));

// Safe wrapper for createIcons that automatically provides all icons if not explicitly passed
const safeCreateIcons = (options = {}) => {
    return createIcons({
        icons: (options && options.icons) ? options.icons : icons,
        ...(options || {}),
    });
};

window.lucide = {
    createIcons: safeCreateIcons,
    icons,
};

// Global helper so Alpine or any script can manually refresh icons if needed
window.refreshIcons = safeCreateIcons;

// Render icons on initial DOM load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => safeCreateIcons());
} else {
    safeCreateIcons();
}

// Re-render once when Alpine initializes
document.addEventListener('alpine:initialized', () => safeCreateIcons());

Alpine.start();