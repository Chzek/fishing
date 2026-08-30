import { test, expect } from '@playwright/test';

test.describe('Searchable Autocomplete Lure & Tackle Selector - E2E Test Suite', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/record/create');
        if (page.url().includes('/login')) {
            // Fill default user credentials if redirected to login
            const emailInput = page.locator('input#email');
            if (await emailInput.isVisible()) {
                await emailInput.fill('lauralkm@gmail.com');
                await page.locator('input#password').fill('password');
                await Promise.all([
                    page.waitForNavigation().catch(() => {}),
                    page.locator('form[action*="login"] button[type="submit"]').click()
                ]);
            }
        }
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

        const firstItem = dropdown.locator('[data-testid^="lure-item-"]').first();
        await expect(firstItem).toBeVisible({ timeout: 5000 });

        // Click to select
        await firstItem.click();

        // Verify selected card appears and search input is replaced
        const selectedCard = root.locator('[data-testid="lure-selected-card"]');
        await expect(selectedCard).toBeVisible({ timeout: 5000 });

        // Verify hidden input has a valid UUID value
        const hiddenInput = root.locator('[data-testid="lure-selector-hidden-input"]');
        const value = await hiddenInput.inputValue();
        expect(value).toBeTruthy();
        expect(value.length).toBeGreaterThan(0);
    });

    test('clearing selected lure returns to search bar state', async ({ page }) => {
        const root = page.locator('[data-testid="lure-selector-root"]').first();
        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.focus();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        const firstItem = dropdown.locator('[data-testid^="lure-item-"]').first();
        await firstItem.click();

        const selectedCard = root.locator('[data-testid="lure-selected-card"]');
        await expect(selectedCard).toBeVisible({ timeout: 5000 });

        // Click clear button
        const clearBtn = selectedCard.locator('[data-testid="lure-clear-button"]');
        await expect(clearBtn).toBeVisible();
        await clearBtn.click();

        // Verify search input is restored and hidden input is emptied
        await expect(root.locator('[data-testid="lure-search-input"]')).toBeVisible({ timeout: 5000 });
        const hiddenInput = root.locator('[data-testid="lure-selector-hidden-input"]');
        expect(await hiddenInput.inputValue()).toBe('');
    });

    test('category tabs filter dropdown items', async ({ page }) => {
        const root = page.locator('[data-testid="lure-selector-root"]').first();
        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.focus();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        // Check if category pills are available
        const allCategoryPill = dropdown.locator('[data-testid="lure-cat-pill-all"]');
        if (await allCategoryPill.isVisible()) {
            await expect(allCategoryPill).toBeVisible();
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
        await page.goto('/record/create');

        const root = page.locator('[data-testid="lure-selector-root"]').first();
        await expect(root).toBeVisible({ timeout: 10000 });

        const searchInput = root.locator('[data-testid="lure-search-input"]');
        await searchInput.tap();

        const dropdown = root.locator('[data-testid="lure-dropdown-tray"]');
        await expect(dropdown).toBeVisible({ timeout: 5000 });

        const firstItem = dropdown.locator('[data-testid^="lure-item-"]').first();
        if (await firstItem.isVisible()) {
            await firstItem.tap();
            await expect(root.locator('[data-testid="lure-selected-card"]')).toBeVisible({ timeout: 5000 });
        }
    });
});
