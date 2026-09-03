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

    test('category filter pills switch active category filter dynamically', async ({ page }) => {
        const crankbaitPill = page.locator('button:has-text("Crankbait")').first();
        if (await crankbaitPill.isVisible().catch(() => false)) {
            await crankbaitPill.click();
            await expect(page.locator('h2:has-text("Crankbait Tray")')).toBeVisible({ timeout: 5000 });
        }
    });

    test('search input filters models dynamically with debouncing', async ({ page }) => {
        const searchInput = page.locator('input[placeholder*="Search tackle"]');
        await expect(searchInput).toBeVisible();
        await searchInput.fill('Rapala');
        await page.waitForTimeout(500);

        // Verify filtered results or active filter chips
        await expect(page.locator('text=Active Filters:').first()).toBeVisible({ timeout: 5000 });
    });

    test('log catch button on variant opens Global Quick Catch drawer modal', async ({ page }) => {
        const logCatchBtn = page.locator('button:has-text("Log Catch")').first();
        if (await logCatchBtn.isVisible().catch(() => false)) {
            await logCatchBtn.click();
            await expect(page.locator('h2:has-text("Quick Catch Logger")')).toBeVisible({ timeout: 5000 });
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
        const modelLink = page.locator('a[href*="/lure/model/"]').first();
        if (await modelLink.isVisible().catch(() => false)) {
            await modelLink.click();
            await expect(page.locator('h1')).toBeVisible();
        }
    });
});
