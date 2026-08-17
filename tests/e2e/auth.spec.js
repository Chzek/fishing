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

        await form.locator('input#email').fill('nonexistent@example.com');
        await form.locator('input#password').fill('wrongpassword');

        // Submit form via Enter keypress
        await form.locator('input#password').press('Enter');

        // Verify that credential validation error appears in the rendered Blade view
        const errorMessage = form.locator('.text-rose-600');
        await expect(errorMessage.first()).toBeVisible({ timeout: 10000 });
        await expect(errorMessage.first()).toContainText(/credentials/i);
    });
});
