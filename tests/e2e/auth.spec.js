import { test, expect } from '@playwright/test';

test.describe('Fishing Logbook - Authentication Flows', () => {
    test('renders login form with expected inputs', async ({ browser }) => {
        const context = await browser.newContext({ storageState: { cookies: [], origins: [] } });
        const page = await context.newPage();
        await page.goto('/login');

        // Check form elements
        await expect(page.locator('h1')).toContainText('Welcome Back');
        const form = page.locator('form[action*="login"]');
        await expect(form.locator('input#email')).toBeVisible();
        await expect(form.locator('input#password')).toBeVisible();
        await expect(form.locator('input#remember')).toBeAttached();
        await expect(form.locator('button[type="submit"]')).toBeVisible();
        await context.close();
    });

    test('validates required fields on client-side form', async ({ browser }) => {
        const context = await browser.newContext({ storageState: { cookies: [], origins: [] } });
        const page = await context.newPage();
        await page.goto('/login');

        const emailInput = page.locator('input#email');
        await expect(emailInput).toHaveAttribute('required', '');

        const passwordInput = page.locator('input#password');
        await expect(passwordInput).toHaveAttribute('required', '');
        await context.close();
    });
});
