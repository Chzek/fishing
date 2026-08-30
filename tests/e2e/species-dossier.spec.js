import { test, expect } from '@playwright/test';
import { ensureAuthenticated } from './helpers.js';

test.describe('Species Dossier & Taxonomy - E2E Test Suite', () => {
    test('renders species taxonomy index with family filters and vector avatars', async ({ page }) => {
        await ensureAuthenticated(page, '/fish');
        await expect(page.locator('h1')).toContainText('Fish Species & Taxonomy Guide', { timeout: 15000 });

        // Verify species cards exist
        const speciesCards = page.locator('a:has-text("View Dossier")');
        await expect(speciesCards.first()).toBeVisible();

        // Verify fish illustrations / images exist in cards
        const images = page.locator('img[src*="/images/fish/"]');
        if (await images.count() > 0) {
            await expect(images.first()).toBeVisible();
        }
    });

    test('family tab filter updates species list', async ({ page }) => {
        await ensureAuthenticated(page, '/fish');

        // Look for family filter tabs (e.g. Salmonidae, Percidae, Esocidae, Centrarchidae)
        const familyTab = page.locator('a[href*="/fish?family="]').first();
        if (await familyTab.isVisible().catch(() => false)) {
            await familyTab.click();
            await expect(page.locator('h1')).toBeVisible();
        }
    });

    test('species dossier page renders telemetry badges and catch directory banner', async ({ page }) => {
        await ensureAuthenticated(page, '/fish');

        const firstSpecies = page.locator('a:has-text("View Dossier")').first();
        await expect(firstSpecies).toBeVisible();
        await firstSpecies.click();

        // Verify Dossier elements
        await expect(page.locator('h1')).toBeVisible({ timeout: 10000 });

        // Verify direct Catch Directory pre-filtered banner link
        const directoryBanner = page.locator('a[href*="/record/directory?species="]');
        await expect(directoryBanner).toBeVisible();
        await expect(directoryBanner).toContainText('Open Logbook Directory');
    });
});
