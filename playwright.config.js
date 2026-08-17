import { defineConfig, devices } from '@playwright/test';

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
  /* Run tests in files in parallel */
  fullyParallel: true,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,
  /* Opt out of parallel tests on CI if desired, limit local concurrency for stable sessions */
  workers: process.env.CI ? 1 : 2,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: 'list',
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    /* Base URL to use in actions like `await page.goto('/')`. */
    baseURL: process.env.APP_URL || 'http://localhost',

    /* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },

  /* Configure projects for major browsers & devices */
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'Pixel 10 Pro',
      use: {
        ...pixel10Pro.use,
      },
    },
  ],
});
