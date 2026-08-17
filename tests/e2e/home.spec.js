import { test, expect } from '@playwright/test';

test.describe('Fishing Logbook - Public Landing Page', () => {
    test('loads the homepage with title and hero elements', async ({ page }) => {
        await page.goto('/');

        // Verify page title
        await expect(page).toHaveTitle(/Fishing Logbook/);

        // Check header branding
        await expect(page.locator('header')).toContainText('Fishing Logbook');
        await expect(page.locator('header')).toContainText('Telemetry & Field Logger');

        // Check hero section heading
        const heroHeading = page.locator('h1');
        await expect(heroHeading).toBeVisible();
        await expect(heroHeading).toContainText('Log Catches & Explore Waters');

        // Check login link is present
        const loginLink = page.getByRole('link', { name: /login/i });
        await expect(loginLink).toBeVisible();
        await expect(loginLink).toHaveAttribute('href', /.*login.*/);
    });

    test('renders Lucide icons in DOM', async ({ page }) => {
        await page.goto('/');

        // Verify that Lucide SVG icons were rendered
        const lucideSvgs = page.locator('svg.lucide');
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
