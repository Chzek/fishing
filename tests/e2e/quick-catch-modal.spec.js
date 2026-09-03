import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Global Quick Catch Slide-Over Drawer Modal - E2E Test Suite', () => {
    test('opens slide-over drawer when clicking desktop sidebar Quick Catch button', async ({ page }) => {
        await ensureAuthenticated(page, '/profile');

        // Click sidebar Quick Catch button
        const quickCatchBtn = page.locator('aside button:has-text("Quick Catch")');
        await expect(quickCatchBtn).toBeVisible({ timeout: 10000 });
        await quickCatchBtn.click();

        // Verify slide-over drawer is visible
        const drawerTitle = page.locator('h2:has-text("Quick Catch Logger")');
        await expect(drawerTitle).toBeVisible({ timeout: 5000 });

        // Verify key form inputs inside modal
        await expect(page.locator('#modal_anglers_id')).toBeVisible();
        await expect(page.locator('#modal_lakes_id')).toBeVisible();
        await expect(page.locator('#modal_fish_breeds_id')).toBeVisible();
        await expect(page.locator('#modal_length')).toBeVisible();

        // Close drawer via close button
        const closeBtn = page.locator('button[title*="Close Drawer"]');
        await closeBtn.click();
        await expect(drawerTitle).not.toBeVisible({ timeout: 5000 });
    });

    test('opens and closes slide-over drawer via keyboard shortcuts', async ({ page }) => {
        await ensureAuthenticated(page, '/profile');

        // Press 'q' to open drawer
        await page.keyboard.press('q');
        const drawerTitle = page.locator('h2:has-text("Quick Catch Logger")');
        await expect(drawerTitle).toBeVisible({ timeout: 5000 });

        // Press Escape to close drawer
        await page.keyboard.press('Escape');
        await expect(drawerTitle).not.toBeVisible({ timeout: 5000 });
    });

    test('can fill and submit a quick catch through the global modal', async ({ page }) => {
        await ensureAuthenticated(page, '/profile');

        // Open modal
        const quickCatchBtn = page.locator('aside button:has-text("Quick Catch")');
        await quickCatchBtn.click();
        await expect(page.locator('h2:has-text("Quick Catch Logger")')).toBeVisible({ timeout: 5000 });

        // Select first available lake
        const lakeSelect = page.locator('#modal_lakes_id');
        const lakeOptions = await lakeSelect.locator('option').all();
        if (lakeOptions.length > 1) {
            const secondVal = await lakeOptions[1].getAttribute('value');
            if (secondVal) await lakeSelect.selectOption(secondVal);
        }

        // Select first available species
        const speciesSelect = page.locator('#modal_fish_breeds_id');
        const speciesOptions = await speciesSelect.locator('option').all();
        if (speciesOptions.length > 1) {
            const secondVal = await speciesOptions[1].getAttribute('value');
            if (secondVal) await speciesSelect.selectOption(secondVal);
        }

        // Fill length
        await page.locator('#modal_length').fill('19.5');

        // Submit form
        const submitBtn = page.locator('button:has-text("Log Catch Immediately")');
        await submitBtn.click();

        // Verify success alert or toast message
        await expect(page.locator('text=Catch registered successfully!')).toBeVisible({ timeout: 10000 });
    });
});
