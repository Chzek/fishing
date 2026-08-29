import { test, expect } from '@playwright/test';

test.describe('Generic Livewire Data Table - End-to-End Test Suite', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/record/directory');
        if (page.url().includes('/login')) {
            await page.locator('input#email').fill('lauralkm@gmail.com');
            await page.locator('input#password').fill('password');
            await Promise.all([
                page.waitForNavigation().catch(() => {}),
                page.locator('form[action*="login"] button[type="submit"]').click()
            ]);
        }
        await expect(page.locator('div[x-data*="dataTable"] table').first()).toBeVisible({ timeout: 15000 });
    });

    test('reactive search filters rows dynamically with debouncing', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const searchInput = tableRoot.locator('input[placeholder*="Search"]').first();
        await expect(searchInput).toBeVisible();

        // Type search query for a species in seeded DB
        await searchInput.fill('Splake');

        // Verify table contains filtered results
        await expect(tableRoot.locator('tbody')).toContainText('Splake', { timeout: 10000 });

        // Locate clear search button inside the search input wrapper
        const clearBtn = tableRoot.locator('button[title="Clear search"]').first();
        await expect(clearBtn).toBeVisible({ timeout: 10000 });

        // Click clear button and verify search is emptied
        await clearBtn.click();
        await expect(searchInput).toHaveValue('', { timeout: 10000 });
    });

    test('column visibility dropdown toggles columns and hides table cells', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const columnPickerBtn = tableRoot.locator('button[title="Toggle columns"]').first();
        await expect(columnPickerBtn).toBeVisible();
        await columnPickerBtn.click();

        // Locate Weight column checkbox in dropdown
        const weightCheckbox = tableRoot.locator('label:has-text("Weight") input[type="checkbox"]').first();
        await expect(weightCheckbox).toBeVisible();
        await expect(weightCheckbox).toBeChecked();

        // Uncheck Weight column
        await weightCheckbox.uncheck();

        // Verify Weight <th> is hidden in DOM
        const weightTh = tableRoot.locator('th[data-col="weight"]').first();
        await expect(weightTh).toBeHidden();

        // Check it back on
        await weightCheckbox.check();
        await expect(weightTh).toBeVisible();
    });

    test('density switcher toggles compact and comfortable row padding', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const compactBtn = tableRoot.locator('button[title="Compact density"]').first();
        const comfortableBtn = tableRoot.locator('button[title="Comfortable density"]').first();

        await expect(compactBtn).toBeVisible();
        await expect(comfortableBtn).toBeVisible();

        // Switch to compact density
        await compactBtn.click();
        const firstTd = tableRoot.locator('tbody tr td').first();
        await expect(firstTd).toHaveClass(/py-2/);

        // Switch back to comfortable density
        await comfortableBtn.click();
        await expect(firstTd).toHaveClass(/py-3\.5/);
    });

    test('header click supports tri-state sorting (Asc -> Desc -> Reset)', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const weightHeader = tableRoot.locator('th[data-col="weight"]').first();
        await expect(weightHeader).toBeVisible();

        // 1. First Click on non-default column: Ascending sort
        await weightHeader.click();
        await expect(weightHeader.getByText('▲', { exact: true })).toBeVisible({ timeout: 10000 });

        // 2. Second Click: Descending sort
        await weightHeader.click();
        await expect(weightHeader.getByText('▼', { exact: true })).toBeVisible({ timeout: 10000 });

        // 3. Third Click: Tri-state reset
        await weightHeader.click();
        await expect(weightHeader.getByText('▲', { exact: true })).toBeHidden({ timeout: 10000 });
        await expect(weightHeader.getByText('▼', { exact: true })).toBeHidden({ timeout: 10000 });
    });

    test('shift+click enables multi-column sorting stack with sequence badges', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const lengthHeader = tableRoot.locator('th[data-col="length"]').first();
        const weightHeader = tableRoot.locator('th[data-col="weight"]').first();

        // Single click length header
        await lengthHeader.click();
        await expect(lengthHeader.getByText('▲', { exact: true })).toBeVisible({ timeout: 10000 });

        // Shift+click weight header
        await weightHeader.click({ modifiers: ['Shift'] });
        await expect(weightHeader.getByText('▲', { exact: true })).toBeVisible({ timeout: 10000 });

        // Sequence badge 1 on length and 2 on weight
        const badge1 = lengthHeader.locator('span.font-mono').first();
        const badge2 = weightHeader.locator('span.font-mono').first();
        await expect(badge1).toHaveText('1', { timeout: 10000 });
        await expect(badge2).toHaveText('2', { timeout: 10000 });
    });

    test('date range popover selects timeframe presets', async ({ page }) => {
        const tableRoot = page.locator('div[x-data*="dataTable"]').first();
        const dateFilterBtn = tableRoot.locator('button:has-text("All Dates"), button:has-text("Today"), button:has-text("Last 7 Days")').first();
        await expect(dateFilterBtn).toBeVisible();
        await dateFilterBtn.click();

        // Select 'Last 7 Days' preset option
        const last7DaysBtn = tableRoot.locator('button:has-text("Last 7 Days")').first();
        await expect(last7DaysBtn).toBeVisible();
        await last7DaysBtn.click();

        // Verify button text updates
        await expect(tableRoot.locator('button:has-text("Last 7 Days")').first()).toBeVisible({ timeout: 10000 });
    });
});
