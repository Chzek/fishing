import { test, expect } from '@playwright/test';

test.describe('Global Navigation & Shortcuts', () => {
    test('search route redirects unauthenticated users to login', async ({ page }) => {
        await page.goto('/search');
        // Unauthenticated access to /search triggers auth middleware redirect to /login
        await expect(page).toHaveURL(/.*login.*/);
    });

    test('loads client-side Alpine and UI scripts', async ({ page }) => {
        await page.goto('/');
        await expect(page.locator('header')).toBeVisible();

        // Verify Alpine and Lucide initialized
        const isAlpineLoaded = await page.evaluate(() => typeof window.Alpine !== 'undefined');
        expect(isAlpineLoaded).toBe(true);

        const isLucideLoaded = await page.evaluate(() => typeof window.initLucideIcons === 'function');
        expect(isLucideLoaded).toBe(true);
    });
});
