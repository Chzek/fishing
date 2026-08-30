import './bootstrap';
import './image-compressor';
import dataTable from './components/data-table';
// Register custom Alpine components
document.addEventListener('livewire:init', () => {
    if (window.Alpine) {
        window.Alpine.data('dataTable', dataTable);
    }
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
