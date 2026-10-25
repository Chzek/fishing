import {
    Chart,
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    DoughnutController,
    LineController,
    RadarController,
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    TimeScale,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import { getThemeTokens, createBaseOptions, SERIES_COLORS } from './theme';
import { centerTextPlugin } from './plugins/center-text';

// Register necessary Chart.js controllers, elements, and scales for tree-shaking
Chart.register(
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    DoughnutController,
    LineController,
    RadarController,
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    TimeScale,
    Tooltip,
    Legend,
    Filler,
    centerTextPlugin
);

export function telemetryChart(initialConfig = {}) {
    return {
        chart: null,
        themeObserver: null,
        chartConfig: initialConfig,

        init() {
            this.$nextTick(() => {
                this.mountChart();
            });

            // Observe dark mode class changes on <html> to dynamically re-theme
            this.themeObserver = new MutationObserver((mutations) => {
                for (const mutation of mutations) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        this.updateTheme();
                    }
                }
            });

            this.themeObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class'],
            });
        },

        mountChart() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            // Ensure previous instance is cleaned up if re-mounting
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            const tokens = getThemeTokens();
            const type = this.chartConfig.type || 'doughnut';
            const userOptions = this.chartConfig.options || {};
            const baseOptions = createBaseOptions(userOptions);

            // Configure type-specific defaults
            if (type === 'doughnut') {
                baseOptions.cutout = userOptions.cutout || '70%';
                baseOptions.plugins = baseOptions.plugins || {};
                if (this.chartConfig.centerText) {
                    baseOptions.plugins.centerText = this.chartConfig.centerText;
                }
                
                // Add default border styling to doughnut slices
                if (this.chartConfig.data?.datasets) {
                    this.chartConfig.data.datasets.forEach(ds => {
                        ds.borderWidth = ds.borderWidth ?? 3;
                        ds.borderColor = ds.borderColor ?? (tokens.isDark ? '#0f172a' : '#ffffff');
                        ds.hoverOffset = ds.hoverOffset ?? 6;
                    });
                }
            } else if (type === 'bar' || type === 'line') {
                baseOptions.scales = baseOptions.scales || {};
                
                // Default X scale styling
                baseOptions.scales.x = {
                    grid: {
                        display: false,
                        color: tokens.gridLine,
                    },
                    border: {
                        color: tokens.gridLine,
                    },
                    ticks: {
                        color: tokens.textSecondary,
                        font: {
                            family: 'ui-monospace, monospace',
                            size: 10,
                        },
                    },
                    ...baseOptions.scales.x,
                };

                // Default Y scale styling
                baseOptions.scales.y = {
                    beginAtZero: true,
                    grid: {
                        color: tokens.gridLine,
                    },
                    border: {
                        color: 'transparent',
                    },
                    ticks: {
                        color: tokens.textSecondary,
                        font: {
                            family: 'ui-monospace, monospace',
                            size: 10,
                        },
                        precision: 0,
                    },
                    ...baseOptions.scales.y,
                };
            } else if (type === 'radar') {
                baseOptions.scales = baseOptions.scales || {};
                baseOptions.scales.r = {
                    angleLines: {
                        color: tokens.gridLine,
                    },
                    grid: {
                        color: tokens.gridLine,
                    },
                    pointLabels: {
                        color: tokens.textSecondary,
                        font: {
                            family: 'ui-sans-serif, system-ui, sans-serif',
                            size: 10,
                            weight: 'bold',
                        },
                    },
                    ticks: {
                        display: false,
                        backdropColor: 'transparent',
                    },
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ...baseOptions.scales.r,
                };
            }

            // Create Chart instance
            this.chart = new Chart(canvas, {
                type,
                data: this.chartConfig.data || { labels: [], datasets: [] },
                options: baseOptions,
            });
        },

        updateTheme() {
            if (!this.chart) return;
            const tokens = getThemeTokens();
            const type = this.chartConfig.type || 'doughnut';

            // Update tooltip styles
            if (this.chart.options.plugins?.tooltip) {
                this.chart.options.plugins.tooltip.backgroundColor = tokens.tooltipBg;
                this.chart.options.plugins.tooltip.borderColor = tokens.tooltipBorder;
                this.chart.options.plugins.tooltip.titleColor = tokens.tooltipText;
                this.chart.options.plugins.tooltip.bodyColor = tokens.tooltipSubtext;
            }

            // Update scale colors if applicable
            if (type === 'bar' || type === 'line') {
                if (this.chart.options.scales?.x) {
                    this.chart.options.scales.x.grid.color = tokens.gridLine;
                    this.chart.options.scales.x.border.color = tokens.gridLine;
                    this.chart.options.scales.x.ticks.color = tokens.textSecondary;
                }
                if (this.chart.options.scales?.y) {
                    this.chart.options.scales.y.grid.color = tokens.gridLine;
                    this.chart.options.scales.y.ticks.color = tokens.textSecondary;
                }
            } else if (type === 'radar') {
                if (this.chart.options.scales?.r) {
                    this.chart.options.scales.r.angleLines.color = tokens.gridLine;
                    this.chart.options.scales.r.grid.color = tokens.gridLine;
                    this.chart.options.scales.r.pointLabels.color = tokens.textSecondary;
                }
            } else if (type === 'doughnut') {
                this.chart.data.datasets.forEach(ds => {
                    ds.borderColor = tokens.isDark ? '#0f172a' : '#ffffff';
                });
            }

            this.chart.update('none'); // Update smoothly without replaying full entrance animation
        },

        updateData(newData) {
            if (!this.chart) return;
            this.chart.data = newData;
            this.chart.update();
        },

        destroy() {
            if (this.themeObserver) {
                this.themeObserver.disconnect();
                this.themeObserver = null;
            }
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        },
    };
}
