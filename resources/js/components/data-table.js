/**
 * Lightweight Alpine.js Table UI Controller
 * Handles column visibility toggling and table density switcher.
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

            this.$nextTick(() => {
                this.applyColumnVisibility();
            });
        },

        setDensity(newDensity) {
            this.density = newDensity;
        },

        toggleColumn(colKey) {
            this.visibleColumns = {
                ...this.visibleColumns,
                [colKey]: !this.isColumnVisible(colKey)
            };
            this.applyColumnVisibility();
        },

        isColumnVisible(colKey) {
            return this.visibleColumns[colKey] !== false;
        },

        applyColumnVisibility() {
            this.$nextTick(() => {
                Object.keys(this.visibleColumns).forEach(colKey => {
                    const isVis = this.isColumnVisible(colKey);
                    const cells = this.$el.querySelectorAll(`[data-col="${colKey}"]`);
                    cells.forEach(el => {
                        if (isVis) {
                            el.style.removeProperty('display');
                        } else {
                            el.style.setProperty('display', 'none', 'important');
                        }
                    });
                });
            });
        }
    };
}


