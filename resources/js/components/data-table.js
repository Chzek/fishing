/**
 * Lightweight Alpine.js Table UI Controller
 * Handles column visibility toggling and table density switcher with localStorage persistence.
 */
export default function dataTable(config = {}) {
    return {
        tableId: config.tableId || 'default_table',
        density: config.defaultDensity || 'normal',
        visibleColumns: {},
        columns: config.columns || [],
        showColumnPicker: false,

        init() {
            // Restore saved density preference if present
            const savedDensity = localStorage.getItem(`fishing_table_density_${this.tableId}`);
            if (savedDensity) {
                this.density = savedDensity;
            }

            // Auto-detect columns if not explicitly provided
            if (!this.columns.length) {
                const headerCells = this.$el.querySelectorAll('thead th[data-col]');
                this.columns = Array.from(headerCells).map(th => ({
                    key: th.dataset.col,
                    label: th.dataset.colLabel || th.textContent.trim(),
                    visible: th.dataset.colVisible !== 'false'
                }));
            }

            // Build default visibility object
            const defaults = {};
            this.columns.forEach(col => {
                defaults[col.key] = col.visible !== false;
            });

            // Restore saved column visibility preferences from localStorage
            const savedCols = localStorage.getItem(`fishing_table_cols_${this.tableId}`);
            if (savedCols) {
                try {
                    const parsed = JSON.parse(savedCols);
                    this.visibleColumns = { ...defaults, ...parsed };
                } catch (e) {
                    this.visibleColumns = defaults;
                }
            } else {
                this.visibleColumns = defaults;
            }
        },

        setDensity(newDensity) {
            this.density = newDensity;
            localStorage.setItem(`fishing_table_density_${this.tableId}`, newDensity);
        },

        toggleColumn(colKey) {
            this.visibleColumns[colKey] = !this.isColumnVisible(colKey);
            localStorage.setItem(`fishing_table_cols_${this.tableId}`, JSON.stringify(this.visibleColumns));
        },

        isColumnVisible(colKey) {
            if (!colKey) return true;
            return this.visibleColumns[colKey] !== false;
        },

        resetColumnState() {
            const defaults = {};
            this.columns.forEach(col => {
                defaults[col.key] = col.visible !== false;
            });
            this.visibleColumns = defaults;
            localStorage.removeItem(`fishing_table_cols_${this.tableId}`);
        }
    };
}
