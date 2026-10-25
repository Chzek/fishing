/**
 * Telemetry v2 Chart Design Tokens & Theme Configuration
 * Complies with the Fishing Logbook dark slate/teal outdoor design system.
 */

export const THEME_PALETTE = {
    teal: {
        solid: '#0d9488',
        light: '#14b8a6',
        gradientStart: '#14b8a6',
        gradientEnd: '#0d9488',
        bg: 'rgba(20, 184, 166, 0.2)',
        border: '#14b8a6',
    },
    sky: {
        solid: '#0284c7',
        light: '#38bdf8',
        gradientStart: '#38bdf8',
        gradientEnd: '#0284c7',
        bg: 'rgba(56, 189, 248, 0.2)',
        border: '#38bdf8',
    },
    emerald: {
        solid: '#059669',
        light: '#34d399',
        gradientStart: '#34d399',
        gradientEnd: '#059669',
        bg: 'rgba(52, 211, 153, 0.2)',
        border: '#34d399',
    },
    amber: {
        solid: '#d97706',
        light: '#fbbf24',
        gradientStart: '#fbbf24',
        gradientEnd: '#d97706',
        bg: 'rgba(251, 191, 36, 0.2)',
        border: '#fbbf24',
    },
    purple: {
        solid: '#7c3aed',
        light: '#a78bfa',
        gradientStart: '#a78bfa',
        gradientEnd: '#7c3aed',
        bg: 'rgba(167, 139, 250, 0.2)',
        border: '#a78bfa',
    },
    indigo: {
        solid: '#4f46e5',
        light: '#818cf8',
        gradientStart: '#818cf8',
        gradientEnd: '#4f46e5',
        bg: 'rgba(129, 140, 248, 0.2)',
        border: '#818cf8',
    },
    rose: {
        solid: '#e11d48',
        light: '#f87171',
        gradientStart: '#f87171',
        gradientEnd: '#e11d48',
        bg: 'rgba(248, 113, 113, 0.2)',
        border: '#f87171',
    },
    slate: {
        solid: '#64748b',
        light: '#94a3b8',
        gradientStart: '#94a3b8',
        gradientEnd: '#64748b',
        bg: 'rgba(148, 163, 184, 0.2)',
        border: '#94a3b8',
    },
};

export const SERIES_COLORS = [
    THEME_PALETTE.teal.light,
    THEME_PALETTE.sky.light,
    THEME_PALETTE.amber.light,
    THEME_PALETTE.emerald.light,
    THEME_PALETTE.purple.light,
    THEME_PALETTE.indigo.light,
    THEME_PALETTE.rose.light,
    THEME_PALETTE.slate.light,
];

export function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

/**
 * Resolve theme colors dynamically based on dark / light mode
 */
export function getThemeTokens() {
    const isDark = isDarkMode();
    return {
        isDark,
        textPrimary: isDark ? '#f8fafc' : '#0f172a',
        textSecondary: isDark ? '#94a3b8' : '#64748b',
        textMuted: isDark ? '#64748b' : '#94a3b8',
        gridLine: isDark ? 'rgba(51, 65, 85, 0.45)' : 'rgba(226, 232, 240, 0.8)',
        borderColor: isDark ? '#1e293b' : '#e2e8f0',
        cardBg: isDark ? '#0f172a' : '#ffffff',
        tooltipBg: '#0f172a',
        tooltipBorder: isDark ? '#334155' : '#1e293b',
        tooltipText: '#ffffff',
        tooltipSubtext: '#94a3b8',
    };
}

/**
 * Common Chart.js base options configured for Telemetry v2 design
 */
export function createBaseOptions(customOptions = {}) {
    const tokens = getThemeTokens();
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: prefersReducedMotion ? false : {
            duration: 500,
            easing: 'easeOutQuart',
        },
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: false, // We use rich HTML Blade badge legends for better mobile UX
            },
            tooltip: {
                enabled: true,
                backgroundColor: tokens.tooltipBg,
                borderColor: tokens.tooltipBorder,
                borderWidth: 1,
                cornerRadius: 10,
                padding: {
                    top: 8,
                    right: 12,
                    bottom: 8,
                    left: 12,
                },
                titleColor: tokens.tooltipText,
                titleFont: {
                    family: 'ui-sans-serif, system-ui, sans-serif',
                    size: 11,
                    weight: 'bold',
                },
                bodyColor: tokens.tooltipSubtext,
                bodyFont: {
                    family: 'ui-monospace, monospace',
                    size: 11,
                    weight: '600',
                },
                boxPadding: 4,
                usePointStyle: true,
            },
        },
        ...customOptions,
    };
}
