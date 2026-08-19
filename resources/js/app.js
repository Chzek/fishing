import './bootstrap';
import './image-compressor';
import dataTable from './components/data-table';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

Alpine.data('dataTable', dataTable);
window.Alpine = Alpine;
Alpine.start();

// Initialize Lucide icons on page load and dynamically after DOM updates
window.initLucideIcons = () => {
    createIcons({ icons });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initLucideIcons();
});

// Global Ctrl+K / Cmd+K Search & Command Palette Focus Listener
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        e.stopPropagation();

        const searchInput = document.querySelector('input[name="q"]') || document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.focus();
            if (typeof searchInput.select === 'function') {
                searchInput.select();
            }
        } else {
            window.location.href = '/search';
        }
    }
});
