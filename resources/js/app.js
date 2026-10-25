import './bootstrap';
import './image-compressor';
import dataTable from './components/data-table';
import { telemetryChart } from './charts';

// Register custom Alpine components
const registerAlpineComponents = () => {
    if (window.Alpine) {
        window.Alpine.data('dataTable', dataTable);
        window.Alpine.data('telemetryChart', telemetryChart);
    }
};

document.addEventListener('alpine:init', registerAlpineComponents);
document.addEventListener('livewire:init', registerAlpineComponents);
if (window.Alpine) {
    registerAlpineComponents();
}

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
