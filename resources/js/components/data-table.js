/**
 * Lightweight Alpine.js Table UI Controller
 * Handles column visibility toggling and table density switcher via reactive x-show directives.
 * Searching and sorting are processed on the database via Laravel Pipes.
 */
export default function dataTable(config = {}) {
    return {
        density: config.defaultDensity || 'normal', // 'normal' | 'compact'
        visibleColumns: {},
        columns: config.columns || [],
        showColumnPicker: false,

        init() {
            // Auto-detect columns if not explicitly provided
            if (!this.columns.length) {
                const headerCells = this.$el.querySelectorAll('thead th[data-col]');
                this.columns = Array.from(headerCells).map(th => ({
                    key: th.dataset.col,
                    label: th.dataset.colLabel || th.textContent.trim(),
                    visible: th.dataset.colVisible !== 'false'
                }));
            }

            const initial = {};
            this.columns.forEach(col => {
                initial[col.key] = col.visible !== false;
            });
            this.visibleColumns = initial;
        },

        setDensity(newDensity) {
            this.density = newDensity;
        },

        toggleColumn(colKey) {
            this.visibleColumns[colKey] = !this.isColumnVisible(colKey);
        },

        isColumnVisible(colKey) {
            if (!colKey) return true;
            return this.visibleColumns[colKey] !== false;
        }
    };
}



