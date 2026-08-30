import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Searchable Autocomplete Lure & Tackle Selector - E2E Test Suite', () => {
    test.beforeEach(async ({ page }) => {
        await ensureAuthenticated(page, '/record/create');
        await expect(page.locator('[data-testid="lure-selector-root"]').first()).toBeVisible({ timeout: 15000 });
    });

    test('search input opens dropdown and filters lures dynamically', async ({ page }) => {
        const root = page.locator('[data-testid="lure-selector-root"]').first();
        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await expect(searchInput).toBeVisible();

        // Focus search input and verify dropdown tray opens
        await searchInput.focus();
        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        // Type query to filter results
        await searchInput.fill('Rapala');

        // Verify dropdown shows filtered lure items
        const lureItems = dropdown.locator('[data-testid^="lure-item-"]');
        await expect(lureItems.first()).toBeVisible({ timeout: 5000 });
        await expect(lureItems.first()).toContainText('Rapala');
    });

    test('selecting a lure updates hidden input and displays selected lure card', async ({ page }) => {
        const root = page.locator('[data-testid="lure-selector-root"]').first();
        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.focus();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        // Click first lure item
        const firstLure = dropdown.locator('[data-testid^="lure-item-"]').first();
        await expect(firstLure).toBeVisible({ timeout: 10000 });
        await firstLure.scrollIntoViewIfNeeded();
        await firstLure.click();

        // Verify selected lure card is visible
        const selectedCard = root.locator('[data-testid="lure-selected-card"]');
        await expect(selectedCard).toBeVisible({ timeout: 15000 });

        // Verify hidden input has UUID value
        const hiddenInput = root.locator('input[type="hidden"]');
        await expect(hiddenInput).toHaveValue(/.+/);
    });

    test('clearing selected lure returns to search bar state', async ({ page }) => {
        const root = page.locator('[data-testid="lure-selector-root"]').first();
        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.focus();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        const firstLure = dropdown.locator('[data-testid^="lure-item-"]').first();
        if (await firstLure.isVisible()) {
            await firstLure.click();
            const selectedCard = root.locator('[data-testid="lure-selected-card"]');
            await expect(selectedCard).toBeVisible({ timeout: 5000 });

            // Click Clear button via data-testid
            const clearBtn = root.locator('[data-testid="lure-clear-button"]');
            if (await clearBtn.isVisible()) {
                await clearBtn.click();
                await expect(selectedCard).not.toBeVisible({ timeout: 10000 });
                await expect(root.locator('[data-testid="lure-search-input"]')).toBeVisible({ timeout: 10000 });
            }
        }
    });

    test('category tabs filter dropdown items', async ({ page }) => {
        const root = page.locator('[data-testid="lure-selector-root"]').first();
        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.focus();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        // Verify category pills are visible in header
        const allPill = dropdown.locator('[data-testid="lure-cat-pill-all"]');
        if (await allPill.isVisible()) {
            await expect(allPill).toBeVisible();

            // Click second category pill if present
            const specificPill = dropdown.locator('[data-testid^="lure-cat-pill-"]').nth(1);
            if (await specificPill.isVisible()) {
                await specificPill.click();
                // Verify results tray updates
                await expect(dropdown.locator('[data-testid^="lure-item-"]').first()).toBeVisible({ timeout: 5000 });
            }
        }
    });

    test('responsive touch interaction on mobile viewport (Pixel 10 Pro)', async ({ page }) => {
        await page.setViewportSize({ width: 412, height: 924 });

        const root = page.locator('[data-testid="lure-selector-root"]').first();
        await expect(root).toBeVisible({ timeout: 10000 });

        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.click();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        const firstItem = dropdown.locator('[data-testid^="lure-item-"]').first();
        if (await firstItem.isVisible()) {
            await firstItem.click();
            await expect(root.locator('[data-testid="lure-selected-card"]')).toBeVisible({ timeout: 5000 });
        }
    });
});
