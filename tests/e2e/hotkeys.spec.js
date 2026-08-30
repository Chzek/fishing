import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Global Navigation & Shortcuts', () => {
    test('search route redirects unauthenticated users to login', async ({ page }) => {
        await page.goto('/search');
        await expect(page).toHaveURL(/.*login.*/);
    });

    test('loads application layout and header elements', async ({ page }) => {
        await ensureAuthenticated(page, '/profile');
        await expect(page.locator('#app')).toBeVisible();
    });
});
