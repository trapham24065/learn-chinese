import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { createIcons, icons } from 'lucide';
createIcons({ icons });

import HanziWriter from 'hanzi-writer';
window.HanziWriter = HanziWriter;