# Testing & Laravel Sail Rules

**Scope**: `tests/**`

## Invariants

1. **Containerized Execution via Laravel Sail**:
   - The host machine does not have a global PHP CLI. Always execute tests through Sail: `./vendor/bin/sail test` or `./vendor/bin/sail test --filter=<TestName>`.
   - Never run raw `phpunit` or `php artisan test` on the host machine.

2. **PHPUnit 11 Architecture & Transactions**:
   - Use PHPUnit 11 with `#[Test]` attribute or `test_` prefixed method names.
   - All feature and integration tests must use the `Illuminate\Foundation\Testing\DatabaseTransactions` trait for fast, isolated rollbacks.

3. **Isolated Test Fixtures**:
   - Do not create mutable database rows inside test `setUp()` methods. Build self-contained model fixtures directly inside individual test methods using model factories.

4. **Strict Assertions**:
   - Prefer strict assertions (`$this->assertSame()`, `$this->assertDatabaseHas()`, `$this->assertDatabaseCount()`) over loose `$this->assertEquals()`.
   - When asserting rendered custom Blade component HTML (`$this->blade(...)`), assert against unescaped value strings to account for Blade's automatic HTML entity encoding (`&quot;`).

5. **Test Scope & Regression Coverage**:
   - Add or update feature tests for all business logic and model changes.
   - Layout-only or pure copy edits do not require new test cases.
