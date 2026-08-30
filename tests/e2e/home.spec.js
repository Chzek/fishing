import { test, expect } from '@playwright/test';

test.describe('Fishing Logbook - Public Landing Page', () => {
    test('loads the homepage with title and hero elements', async ({ page }) => {
        await page.goto('/');

        // Verify page title
        await expect(page).toHaveTitle(/Fishing Logbook/);

        // Check header branding
        await expect(page.locator('header')).toContainText('Fishing Logbook');

        // Check hero section heading and description
        const heroHeading = page.locator('h1');
        await expect(heroHeading).toBeVisible();
        await expect(heroHeading).toContainText('Fishing Logbook');
        await expect(page.locator('main')).toContainText('Precision telemetry & catch logging');

        // Check sign in link or dashboard link
        const authLink = page.locator('header a[href*="login"], header a[href*="profile"]');
        await expect(authLink.first()).toBeVisible();
    });

    test('renders server-side Lucide SVG icons in DOM', async ({ page }) => {
        await page.goto('/');

        // Verify that native Blade Lucide SVG icons were rendered
        const lucideSvgs = page.locator('header svg, main svg');
        await expect(lucideSvgs.first()).toBeVisible();
    });

    test('responsive layout on mobile viewports', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 667 });
        await page.goto('/');

        // Hero and navigation elements should adapt gracefully
        await expect(page.locator('header')).toBeVisible();
        await expect(page.locator('h1')).toBeVisible();
    });
});
