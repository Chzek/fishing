import { execSync } from 'child_process';

function runArtisan(command) {
    try {
        execSync(`php artisan ${command}`, { stdio: 'pipe' });
    } catch {
        try {
            execSync(`./vendor/bin/sail artisan ${command}`, { stdio: 'pipe' });
        } catch {
            // Ignore if artisan CLI is unavailable in current environment
        }
    }
}

/**
 * Global Teardown for Playwright E2E Tests:
 * Automatically cleans up test catches, expeditions, and records logged by the test user.
 */
async function globalTeardown() {
    runArtisan('test:clean-e2e-data');
}

export default globalTeardown;
