import { getThemeTokens } from '../theme';

/**
 * Chart.js plugin to draw center metrics & labels in Doughnut cutouts.
 */
export const centerTextPlugin = {
    id: 'centerText',
    beforeDraw(chart) {
        const { ctx, chartArea } = chart;
        const options = chart.options.plugins?.centerText;

        if (!options || !options.value) {
            return;
        }

        const tokens = getThemeTokens();
        const centerX = (chartArea.left + chartArea.right) / 2;
        const centerY = (chartArea.top + chartArea.bottom) / 2;

        ctx.save();
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        const label = options.label || '';
        const value = String(options.value);
        const subtext = options.subtext || '';

        // Draw top label (e.g. "SPECIES")
        if (label) {
            ctx.font = 'bold 9px ui-sans-serif, system-ui, sans-serif';
            ctx.fillStyle = tokens.textMuted;
            ctx.fillText(label.toUpperCase(), centerX, centerY - 16);
        }

        // Draw center large numeric value (e.g. "5")
        ctx.font = 'bold 20px ui-monospace, SFMono-Regular, monospace';
        ctx.fillStyle = tokens.textPrimary;
        ctx.fillText(value, centerX, label || subtext ? centerY : centerY);

        // Draw bottom subtext (e.g. "36 fish")
        if (subtext) {
            ctx.font = 'bold 10px ui-monospace, SFMono-Regular, monospace';
            ctx.fillStyle = '#0d9488'; // Teal
            ctx.fillText(subtext, centerX, centerY + 16);
        }

        ctx.restore();
    },
};
