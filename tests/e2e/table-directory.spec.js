import { test, expect } from '@playwright/test';

test.describe('Fishing Logbook - Data Table & Pipeline Features', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/record/directory');
        if (page.url().includes('/login')) {
            await page.locator('input#email').fill('lauralkm@gmail.com');
            await page.locator('input#password').fill('password');
            await Promise.all([
                page.waitForNavigation().catch(() => {}),
                page.locator('form[action*="login"] button[type="submit"]').click()
            ]);
        }
    });

    test('unified search bar submits search queries and supports inline clear', async ({ page }) => {
        const searchInput = page.locator('input[name="search"]').first();
        const searchSubmitBtn = page.locator('button[type="submit"]:has-text("Search")').first();

        await expect(searchInput).toBeVisible();
        await expect(searchSubmitBtn).toBeVisible();

        // Perform search query
        await searchInput.fill('Walleye');
        await searchSubmitBtn.click();
        await page.waitForURL((url) => url.searchParams.get('search') === 'Walleye', { timeout: 10000 });

        expect(page.url()).toContain('search=Walleye');

        // Locate inline clear button inside search bar
        const clearBtn = page.locator('a[title="Clear search"]').first();
        await expect(clearBtn).toBeVisible();

        // Click clear button and verify search parameter is removed
        await clearBtn.click();
        await page.waitForURL((url) => !url.searchParams.has('search'), { timeout: 10000 });

        expect(page.url()).not.toContain('search=Walleye');
    });

    test('column visibility dropdown toggles table headers and data cells', async ({ page }) => {
        // Open Column Picker dropdown by matching icon or text
        const columnPickerBtn = page.locator('button').filter({ has: page.locator('svg.lucide-columns-3, i[data-lucide="columns-3"], span:has-text("Columns")') }).first();
        await expect(columnPickerBtn).toBeVisible();
        await columnPickerBtn.click();

        // Locate Weight column checkbox
        const weightCheckbox = page.locator('label:has-text("Weight") input[type="checkbox"]').first();
        await expect(weightCheckbox).toBeVisible();
        await expect(weightCheckbox).toBeChecked();

        // Uncheck Weight column
        await weightCheckbox.uncheck();

        // Verify Weight <th> element is hidden in DOM (x-show -> display: none)
        const weightTh = page.locator('th[data-col="weight"]').first();
        await expect(weightTh).toBeHidden();
    });

    test('tri-state column sorting cycles through Asc -> Desc -> Reset', async ({ page }) => {
        const dateHeaderLink = page.locator('th[data-col="date"] a').first();
        await expect(dateHeaderLink).toBeVisible();

        // First Click: Ascending sort
        await dateHeaderLink.click();
        await page.waitForURL((url) => url.searchParams.get('sort_by') === 'date' && url.searchParams.get('sort_order') === 'asc', { timeout: 10000 });
        expect(page.url()).toContain('sort_by=date');
        expect(page.url()).toContain('sort_order=asc');

        // Second Click: Descending sort
        await dateHeaderLink.click();
        await page.waitForURL((url) => url.searchParams.get('sort_by') === 'date' && url.searchParams.get('sort_order') === 'desc', { timeout: 10000 });
        expect(page.url()).toContain('sort_by=date');
        expect(page.url()).toContain('sort_order=desc');

        // Third Click: Reset (Tri-State 3rd state - clears explicit sort_by param)
        await dateHeaderLink.click();
        await page.waitForURL((url) => !url.searchParams.has('sort_by'), { timeout: 10000 });
        expect(page.url()).not.toContain('sort_by=date');
    });

    test('multi-column sorting with Shift+Click appends sort stack and displays sequence badges without opening new window', async ({ page, context }) => {
        let newPageOpened = false;
        context.on('page', () => { newPageOpened = true; });

        const dateHeaderLink = page.locator('th[data-col="date"] a').first();
        const lengthHeaderLink = page.locator('th[data-col="length"] a').first();

        // Single Click Date Header (First sort)
        await dateHeaderLink.click();
        await page.waitForURL((url) => url.searchParams.get('sort_by') === 'date', { timeout: 10000 });
        expect(page.url()).toContain('sort_by=date');

        // Shift+Click Length Header (Multi-sort append)
        await lengthHeaderLink.click({ modifiers: ['Shift'] });
        await page.waitForURL((url) => url.searchParams.get('sort_by') === 'date,length', { timeout: 10000 });

        // Verify URL contains multi-sort comma-separated columns
        expect(page.url()).toContain('sort_by=date%2Clength');
        expect(newPageOpened).toBe(false);

        // Verify exact sequence badge font-mono elements (1 and 2) are rendered
        const badge1 = page.locator('th[data-col="date"] span.font-mono').first();
        const badge2 = page.locator('th[data-col="length"] span.font-mono').first();
        await expect(badge1).toBeVisible();
        await expect(badge2).toBeVisible();
        await expect(badge1).toHaveText('1');
        await expect(badge2).toHaveText('2');
    });
});
