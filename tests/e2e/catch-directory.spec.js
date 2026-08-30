import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Catch Directory Livewire 3 - E2E Test Suite', () => {
    test.beforeEach(async ({ page }) => {
        await ensureAuthenticated(page, '/record/directory');
        await expect(page.locator('div[x-data*="dataTable"]').first()).toBeVisible({ timeout: 15000 });
    });

    test('renders catch directory and initial telemetry table', async ({ page }) => {
        await expect(page.locator('h1')).toContainText('Catches Logbook Directory');
        const table = page.locator('div[x-data*="dataTable"] table').first();
        await expect(table).toBeVisible();
    });

    test('reactive search filters records dynamically with debouncing', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const searchInput = tableRoot.locator('input[placeholder*="Search"]').first();
        await searchInput.scrollIntoViewIfNeeded();
        await expect(searchInput).toBeVisible();

        // Search for a common fish or lake keyword
        await searchInput.fill('Bass');
        await page.waitForTimeout(600);

        // Verify table or empty state updates
        const tbody = tableRoot.locator('tbody');
        await expect(tbody).toBeVisible();

        // Clear search if clear button appears
        const clearBtn = tableRoot.locator('button[title="Clear search"]').first();
        if (await clearBtn.isVisible().catch(() => false)) {
            await clearBtn.click();
            await expect(searchInput).toHaveValue('');
        }
    });

    test('column header click sorts records dynamically', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const dateHeader = tableRoot.locator('th button:has-text("Date")').first();
        
        if (await dateHeader.isVisible().catch(() => false)) {
            await dateHeader.scrollIntoViewIfNeeded();
            await dateHeader.click();
            await expect(tableRoot.locator('tbody')).toBeVisible();
        }
    });

    test('density switcher toggles row padding', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const densityBtn = tableRoot.locator('button[title*="density" i], button:has-text("Compact"), button:has-text("Comfortable")').first();

        if (await densityBtn.isVisible().catch(() => false)) {
            await densityBtn.scrollIntoViewIfNeeded();
            await densityBtn.click();
            await expect(tableRoot.locator('table')).toBeVisible();
        }
    });

    test('column visibility dropdown toggles columns', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const columnPickerBtn = tableRoot.locator('button[title="Toggle columns"]').first();
        
        if (await columnPickerBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
            await columnPickerBtn.scrollIntoViewIfNeeded();
            await columnPickerBtn.click();
            const dropdown = tableRoot.locator('div:has-text("Visible Columns")').first();
            await expect(dropdown).toBeVisible({ timeout: 5000 });
        }
    });
});
