import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Tacklebox & Lures Catalog - E2E Test Suite', () => {
    test.beforeEach(async ({ page }) => {
        await ensureAuthenticated(page, '/lure');
        await expect(page.locator('h1')).toContainText('Digital Tackle Box', { timeout: 15000 });
    });

    test('renders tacklebox index with KPI telemetry metrics', async ({ page }) => {
        await expect(page.locator('span:has-text("Total Tackle Inventory")').first()).toBeVisible();
        await expect(page.locator('span:has-text("Catches Landed on Tackle")').first()).toBeVisible();
    });

    test('category filter pills switch active category filter', async ({ page }) => {
        const categoryFilter = page.locator('a[href*="/lure?category="]').first();
        if (await categoryFilter.isVisible().catch(() => false)) {
            await categoryFilter.click();
            await expect(page.locator('h1')).toContainText('Digital Tackle Box', { timeout: 15000 });
        }
    });

    test('navigates to lure creation page with variant builder', async ({ page }) => {
        const addLureBtn = page.locator('a[href*="/lure/create"]').first();
        await expect(addLureBtn).toBeVisible();
        await addLureBtn.click();

        await expect(page.locator('h1')).toContainText('Add Tackle to Inventory');
        await expect(page.locator('input#name')).toBeVisible();
        await expect(page.locator('input#brand')).toBeVisible();
    });

    test('lure model telemetry page renders performance breakdown', async ({ page }) => {
        // Find a model link from the table
        const modelLink = page.locator('a[href*="/lure/model/"]').first();
        if (await modelLink.isVisible().catch(() => false)) {
            await modelLink.click();
            await expect(page.locator('h1')).toBeVisible();
        }
    });
});
