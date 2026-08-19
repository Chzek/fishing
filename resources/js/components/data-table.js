/**
 * Reusable Alpine.js Data Table Controller
 * Provides real-time local search, multi-column sorting (Shift+Click for secondary sorts),
 * column visibility controls, density switcher, and dynamic aggregate summaries.
 */
export default function dataTable(config = {}) {
    return {
        search: '',
        density: config.defaultDensity || 'normal', // 'normal' | 'compact'
        sortQueue: [], // Array of { col: string, dir: 'asc'|'desc', type: 'text'|'number'|'date' }
        visibleColumns: {},
        columns: config.columns || [],
        totalRows: 0,
        visibleRowCount: 0,
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

            this.columns.forEach(col => {
                this.visibleColumns[col.key] = col.visible !== false;
            });

            this.$nextTick(() => {
                this.cacheOriginalIndices();
                this.updateCounts();
                this.applyColumnVisibility();
            });

            // Watch search changes
            this.$watch('search', () => {
                this.filterRows();
            });
        },

        cacheOriginalIndices() {
            const tbody = this.getTableBody();
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr[data-table-row]'));
            rows.forEach((row, i) => {
                if (!row.dataset.originalIndex) {
                    row.dataset.originalIndex = i;
                }
            });
        },

        getTableBody() {
            return this.$refs.tbody || this.$el.querySelector('tbody');
        },

        // Toggle Sort on a Column (Single Click = Primary sort, Shift+Click / Meta = Multi sort)
        toggleSort(colKey, type = 'text', event = null) {
            const isMulti = event && (event.shiftKey || event.metaKey || event.ctrlKey);
            const existingIdx = this.sortQueue.findIndex(s => s.col === colKey);

            if (!isMulti) {
                if (existingIdx !== -1) {
                    const currentDir = this.sortQueue[existingIdx].dir;
                    if (currentDir === 'asc') {
                        this.sortQueue = [{ col: colKey, dir: 'desc', type }];
                    } else if (currentDir === 'desc') {
                        this.sortQueue = [];
                    }
                } else {
                    this.sortQueue = [{ col: colKey, dir: 'asc', type }];
                }
            } else {
                if (existingIdx !== -1) {
                    const currentDir = this.sortQueue[existingIdx].dir;
                    if (currentDir === 'asc') {
                        this.sortQueue[existingIdx].dir = 'desc';
                    } else {
                        this.sortQueue.splice(existingIdx, 1);
                    }
                } else {
                    this.sortQueue.push({ col: colKey, dir: 'asc', type });
                }
            }

            this.applySortToDom();
            this.reinitLucideIcons();
        },

        getSortInfo(colKey) {
            const idx = this.sortQueue.findIndex(s => s.col === colKey);
            if (idx === -1) return null;
            return {
                priority: idx + 1,
                dir: this.sortQueue[idx].dir,
                isMulti: this.sortQueue.length > 1
            };
        },

        clearSort() {
            this.sortQueue = [];
            this.resetDomOrder();
            this.reinitLucideIcons();
        },

        clearSearch() {
            this.search = '';
            this.filterRows();
        },

        setDensity(newDensity) {
            this.density = newDensity;
        },

        // Filter Rows in Real-Time
        filterRows() {
            const tbody = this.getTableBody();
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr[data-table-row]'));
            const term = this.search.trim().toLowerCase();

            let visible = 0;
            rows.forEach(row => {
                const text = (row.dataset.searchContent || row.textContent).toLowerCase();
                const matches = !term || text.includes(term);
                row.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            this.visibleRowCount = visible;
            this.totalRows = rows.length;

            const emptyEl = this.$refs.emptyState || this.$el.querySelector('[data-table-empty]');
            if (emptyEl) {
                emptyEl.style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
            }

            this.updateAggregates();
        },

        // Sort DOM rows based on sortQueue
        applySortToDom() {
            const tbody = this.getTableBody();
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr[data-table-row]'));
            if (!rows.length) return;

            if (this.sortQueue.length === 0) {
                this.resetDomOrder();
                return;
            }

            rows.sort((a, b) => {
                for (const sort of this.sortQueue) {
                    const cellA = a.querySelector(`[data-col="${sort.col}"]`);
                    const cellB = b.querySelector(`[data-col="${sort.col}"]`);
                    
                    const valA = cellA ? (cellA.dataset.sortVal ?? cellA.textContent.trim()) : '';
                    const valB = cellB ? (cellB.dataset.sortVal ?? cellB.textContent.trim()) : '';

                    let cmp = 0;
                    if (sort.type === 'number') {
                        const numA = parseFloat(String(valA).replace(/[^0-9.-]+/g, '')) || 0;
                        const numB = parseFloat(String(valB).replace(/[^0-9.-]+/g, '')) || 0;
                        cmp = numA - numB;
                    } else if (sort.type === 'date') {
                        const dateA = new Date(valA).getTime() || 0;
                        const dateB = new Date(valB).getTime() || 0;
                        cmp = dateA - dateB;
                    } else {
                        cmp = String(valA).localeCompare(String(valB), undefined, { numeric: true, sensitivity: 'base' });
                    }

                    if (cmp !== 0) {
                        return sort.dir === 'asc' ? cmp : -cmp;
                    }
                }
                return (parseInt(a.dataset.originalIndex, 10) || 0) - (parseInt(b.dataset.originalIndex, 10) || 0);
            });

            rows.forEach(row => tbody.appendChild(row));
        },

        resetDomOrder() {
            const tbody = this.getTableBody();
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr[data-table-row]'));
            rows.sort((a, b) => (parseInt(a.dataset.originalIndex, 10) || 0) - (parseInt(b.dataset.originalIndex, 10) || 0));
            rows.forEach(row => tbody.appendChild(row));
        },

        toggleColumn(colKey) {
            this.visibleColumns[colKey] = !this.visibleColumns[colKey];
            this.applyColumnVisibility();
        },

        isColumnVisible(colKey) {
            return this.visibleColumns[colKey] !== false;
        },

        applyColumnVisibility() {
            Object.keys(this.visibleColumns).forEach(colKey => {
                const isVis = this.visibleColumns[colKey];
                const cells = this.$el.querySelectorAll(`[data-col="${colKey}"]`);
                cells.forEach(el => {
                    el.style.display = isVis ? '' : 'none';
                });
            });
        },

        updateCounts() {
            const tbody = this.getTableBody();
            if (!tbody) return;
            const rows = tbody.querySelectorAll('tr[data-table-row]');
            this.totalRows = rows.length;
            this.visibleRowCount = rows.length;
            this.filterRows();
        },

        updateAggregates() {
            const aggregates = this.$el.querySelectorAll('[data-aggregate-for]');
            if (!aggregates.length) return;

            const tbody = this.getTableBody();
            if (!tbody) return;
            const visibleRows = Array.from(tbody.querySelectorAll('tr[data-table-row]')).filter(r => r.style.display !== 'none');

            aggregates.forEach(aggEl => {
                const col = aggEl.dataset.aggregateFor;
                const type = aggEl.dataset.aggregateType || 'avg'; // 'sum' | 'avg' | 'count'
                let sum = 0;
                let count = 0;

                visibleRows.forEach(row => {
                    const cell = row.querySelector(`[data-col="${col}"]`);
                    if (cell) {
                        const raw = cell.dataset.sortVal ?? cell.textContent.trim();
                        const num = parseFloat(String(raw).replace(/[^0-9.-]+/g, ''));
                        if (!isNaN(num)) {
                            sum += num;
                            count++;
                        }
                    }
                });

                if (type === 'count') {
                    aggEl.textContent = count;
                } else if (type === 'sum') {
                    aggEl.textContent = sum.toFixed(1);
                } else if (type === 'avg') {
                    aggEl.textContent = count > 0 ? (sum / count).toFixed(1) : '—';
                }
            });
        },

        reinitLucideIcons() {
            if (typeof window.initLucideIcons === 'function') {
                this.$nextTick(() => {
                    window.initLucideIcons();
                });
            }
        }
    };
}
