import './bootstrap';
import './image-compressor';
import dataTable from './components/data-table';
import { createIcons, icons } from 'lucide';

// Register custom Alpine components with Livewire 3's bundled Alpine
document.addEventListener('livewire:init', () => {
    if (window.Alpine) {
        window.Alpine.data('dataTable', dataTable);
    }
});

// Fallback for non-Livewire pages
if (window.Alpine) {
    window.Alpine.data('dataTable', dataTable);
} else if (typeof window.Livewire === 'undefined') {
    import('alpinejs').then(({ default: Alpine }) => {
        if (!window.Alpine) {
            window.Alpine = Alpine;
            Alpine.data('dataTable', dataTable);
            Alpine.start();
        }
    });
}

// Initialize Lucide icons on page load and dynamically after DOM updates
window.initLucideIcons = () => {
    createIcons({ icons });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initLucideIcons();
});

// Re-initialize icons after Livewire DOM morph updates
document.addEventListener('livewire:initialized', () => {
    window.initLucideIcons();
});
document.addEventListener('livewire:navigated', () => {
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
