import { defineConfig, devices } from '@playwright/test';
import path from 'path';

/**
 * Custom Device Configuration for Google Pixel 10 Pro
 * Viewport: 412 x 924 @ 3.5x DPR, Mobile & Touch Enabled
 */
const pixel10Pro = {
  name: 'Pixel 10 Pro',
  use: {
    userAgent: 'Mozilla/5.0 (Linux; Android 15; Pixel 10 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36',
    viewport: { width: 412, height: 924 },
    deviceScaleFactor: 3.5,
    isMobile: true,
    hasTouch: true,
    defaultBrowserType: 'chromium',
  },
};

/**
 * See https://playwright.dev/docs/test-configuration.
 */
export default defineConfig({
  testDir: './tests/e2e',
  globalSetup: './tests/e2e/global-setup.js',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  /* Set workers: 1 for local Sail execution to avoid Docker MySQL session locking */
  workers: 1,
  reporter: 'list',
  use: {
    baseURL: process.env.APP_URL || 'http://localhost',
    storageState: path.join(process.cwd(), 'playwright', '.auth', 'user.json'),
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'Pixel 10 Pro',
      use: { ...pixel10Pro.use },
    },
  ],
});
