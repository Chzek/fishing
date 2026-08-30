import { expect } from '@playwright/test';

/**
 * Ensures the session is authenticated as a user.
 * If redirected to /login, fills credentials and logs in.
 */
export async function ensureAuthenticated(page, targetUrl = '/') {
    await page.goto(targetUrl);
    if (page.url().includes('/login')) {
        const emailInput = page.locator('input#email');
        if (await emailInput.isVisible({ timeout: 5000 }).catch(() => false)) {
            await emailInput.fill('lauralkm@gmail.com');
            const pwdInput = page.locator('input#password');
            await pwdInput.fill('password');
            const form = page.locator('form[action*="login"]');
            await form.evaluate(el => el.requestSubmit());
            await page.waitForURL(url => !url.pathname.includes('/login'), { timeout: 15000 }).catch(() => {});
            if (targetUrl !== '/' && !page.url().includes(targetUrl)) {
                await page.goto(targetUrl);
            }
        }
    }
}
