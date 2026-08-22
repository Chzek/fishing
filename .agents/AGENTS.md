# Project Rules & Development Workflow

- **Environment & Execution**:
  - The host machine does not have a global PHP CLI. Always execute PHP, Artisan, and Composer commands via Laravel Sail (e.g., `./vendor/bin/sail artisan ...`, `./vendor/bin/sail test`).
  - Ensure Sail is running (`./vendor/bin/sail up -d`) before executing commands if it is down.

- **Branching & Testing Workflow**: At the start of any new work or task on this project:
  1. Switch to `master` and pull the latest changes (`git checkout master && git pull`).
  2. Create a new dedicated feature branch for the task (e.g., `git checkout -b <feature-or-fix-name>`).
  3. Execute all code changes, edits, and tests (`./vendor/bin/sail test`) on the feature branch.
  4. When writing feature tests for custom Blade components (`$this->blade(...)`), test for unescaped value strings (e.g. `$view->assertSee('14.5')`) to account for Blade's automatic HTML entity encoding (`&quot;`) on attribute strings.
  5. Ensure all tests pass cleanly before merging into `master`.
  6. After merging into `master`, push the commits (`git push`) to keep the repository synchronized across machines.


- **Database Safety & Backups**:
  - Never execute destructive database commands (such as `migrate:fresh`, `migrate:reset`, `migrate:refresh`, or `db:wipe`) without explicit confirmation, as this is a live/shared database environment.
  - Before executing schema/data migrations, bulk data imports, baseline sync pulls, or any destructive operations, always create a timestamped database backup in `database/backups/` using `mysqldump` / Sail.
