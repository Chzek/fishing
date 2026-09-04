/**
 * Navigates directly to target URL utilizing pre-authenticated storageState.
 * If redirected to /login for any reason, performs fallback authentication.
 */
export async function ensureAuthenticated(page, targetUrl = '/') {
    await page.goto(targetUrl);
    if (page.url().includes('/login')) {
        const emailInput = page.locator('input#email');
        if (await emailInput.isVisible({ timeout: 3000 }).catch(() => false)) {
            await emailInput.fill('test.playwright@fishinglogbook.local');
            const pwdInput = page.locator('input#password');
            await pwdInput.fill('password');
            const form = page.locator('form[action*="login"]');
            await form.evaluate(el => el.requestSubmit());
            await page.waitForURL(url => !url.pathname.includes('/login'), { timeout: 10000 }).catch(() => {});
            if (targetUrl !== '/' && !page.url().includes(targetUrl)) {
                await page.goto(targetUrl);
            }
        }
    }
}
