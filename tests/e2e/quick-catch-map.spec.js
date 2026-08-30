import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Boat Quick Catch & Hydrographic Map Explorer - E2E Test Suite', () => {
    test('renders quick catch logger form with autocomplete lure selector', async ({ page }) => {
        await ensureAuthenticated(page, '/record/quick');
        await expect(page.locator('h1')).toContainText('Boat Quick Catch Log', { timeout: 15000 });

        // Verify key logger inputs
        await expect(page.locator('select[name="anglers_id"]')).toBeVisible();
        await expect(page.locator('select[name="lakes_id"]')).toBeVisible();
        await expect(page.locator('select[name="fish_breeds_id"]')).toBeVisible();
        await expect(page.locator('input[name="length"]')).toBeVisible();

        // Verify Livewire Lure Selector is mounted in quick catch form
        const lureSelector = page.locator('[data-testid="lure-selector-root"]');
        await expect(lureSelector).toBeVisible();
        const lureSearchInput = page.locator('[data-testid="lure-search-input"]');
        await expect(lureSearchInput).toBeVisible();
    });

    test('interacts with autocomplete lure selector in quick catch form', async ({ page }) => {
        await ensureAuthenticated(page, '/record/quick');

        const lureSearchInput = page.locator('[data-testid="lure-search-input"]');
        await lureSearchInput.scrollIntoViewIfNeeded();
        await lureSearchInput.click();

        const dropdown = page.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        // Select first available lure
        const firstLure = dropdown.locator('[data-testid^="lure-item-"]').first();
        if (await firstLure.isVisible()) {
            await firstLure.scrollIntoViewIfNeeded();
            await firstLure.click();
            await expect(page.locator('[data-testid="lure-selected-card"]')).toBeVisible({ timeout: 10000 });
        }
    });

    test('renders Leaflet Map Explorer and telemetry overlay', async ({ page }) => {
        await ensureAuthenticated(page, '/map/explorer');
        await expect(page.locator('h1')).toContainText('Lake Explorer', { timeout: 15000 });

        // Verify Leaflet map container is initialized
        const leafletMap = page.locator('#explorer-map');
        await expect(leafletMap).toBeVisible({ timeout: 10000 });
    });

    test('renders offline map pack download manager', async ({ page }) => {
        await ensureAuthenticated(page, '/map/offline');
        await expect(page.locator('h1')).toContainText('Offline Map Region Downloader', { timeout: 15000 });
    });
});
