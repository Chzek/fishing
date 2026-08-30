import { chromium } from '@playwright/test';
import fs from 'fs';
import path from 'path';

/**
 * Global Setup for Playwright E2E Tests:
 * Authenticates a test user once and saves session cookies to playwright/.auth/user.json
 * to eliminate per-test login overhead.
 */
async function globalSetup(config) {
    const baseURL = config.projects[0].use.baseURL || 'http://localhost';
    const authDir = path.join(process.cwd(), 'playwright', '.auth');
    if (!fs.existsSync(authDir)) {
        fs.mkdirSync(authDir, { recursive: true });
    }
    const storageStatePath = path.join(authDir, 'user.json');

    const browser = await chromium.launch();
    const page = await browser.newPage();

    await page.goto(`${baseURL}/login`);
    await page.locator('input#email').fill('lauralkm@gmail.com');
    await page.locator('input#password').fill('password');
    const form = page.locator('form[action*="login"]');
    await form.evaluate(el => el.requestSubmit());
    await page.waitForURL(url => !url.pathname.includes('/login'), { timeout: 15000 });

    await page.context().storageState({ path: storageStatePath });
    await browser.close();
}

export default globalSetup;
