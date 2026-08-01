# Project Rules & Development Workflow

- **Branching & Testing Workflow**: At the start of any new work or task on this project:
  1. Switch to `master` and pull the latest changes (`git checkout master && git pull`).
  2. Create a new dedicated feature branch for the task (e.g., `git checkout -b <feature-or-fix-name>`).
  3. Execute all code changes, edits, and tests (`./vendor/bin/sail test`) on the feature branch.
  4. Ensure all tests pass cleanly before merging into `master`.
  5. After merging into `master`, push the commits (`git push`) to keep the repository synchronized across machines.
