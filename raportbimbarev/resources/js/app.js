import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Iconify web component with offline Lucide icon data
import { addCollection } from 'iconify-icon';
import lucideData from '@iconify-json/lucide/icons.json';
addCollection(lucideData);

// Chart.js exposed as global for inline scripts
import Chart from 'chart.js/auto';
window.Chart = Chart;
