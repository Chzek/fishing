import { test, expect } from '@playwright/test';

test.describe('Fishing Logbook - Authentication Flows', () => {
    test('renders login form with expected inputs', async ({ page }) => {
        await page.goto('/login');

        // Check form elements
        await expect(page.locator('h1')).toContainText('Welcome Back');
        const form = page.locator('form[action*="login"]');
        await expect(form.locator('input#email')).toBeVisible();
        await expect(form.locator('input#password')).toBeVisible();
        await expect(form.locator('input#remember')).toBeAttached();
        await expect(form.locator('button[type="submit"]')).toBeVisible();
    });

    test('shows error message on failed login attempt', async ({ page }) => {
        await page.goto('/login');
        const form = page.locator('form[action*="login"]');
        await expect(form.locator('input#email')).toBeVisible();

        const uniqueFailedEmail = `failed-${Date.now()}-${Math.random().toString(36).substring(2, 6)}@example.com`;
        await form.locator('input#email').fill(uniqueFailedEmail);
        await form.locator('input#password').fill('wrongpassword');

        // Submit form
        await form.locator('button[type="submit"]').click();

        // Verify that credential validation error appears in the rendered Blade view
        const errorMessage = page.locator('.text-rose-600');
        await expect(errorMessage.first()).toBeVisible({ timeout: 15000 });
        await expect(errorMessage.first()).toContainText(/credentials/i);
    });
});
