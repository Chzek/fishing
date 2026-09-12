# Project Rules Index

This file maps repository path globs to specific architectural rule files. Before planning or modifying files in these paths, load and follow every matching rule.

| Path Glob | Rule File | Description |
| :--- | :--- | :--- |
| `app/Models/**` | [models.md](file:///home/gmroczek/git/fishing/.ai/rules/models.md) | UUID primary keys, Spatial Point geometry, casts(), SoftDeletes |
| `database/migrations/**` | [models.md](file:///home/gmroczek/git/fishing/.ai/rules/models.md) | Migration standards, column types, spatial definitions, non-destructive safety |
| `app/Actions/**` | [actions-services.md](file:///home/gmroczek/git/fishing/.ai/rules/actions-services.md) | Single-purpose Action classes (`execute()`), transaction boundaries |
| `app/Services/**` | [actions-services.md](file:///home/gmroczek/git/fishing/.ai/rules/actions-services.md) | Constructor-injected domain services, business logic encapsulation |
| `app/Http/Controllers/**` | [controllers-requests.md](file:///home/gmroczek/git/fishing/.ai/rules/controllers-requests.md) | Thin controllers, Action delegation, response formatting |
| `app/Http/Requests/**` | [controllers-requests.md](file:///home/gmroczek/git/fishing/.ai/rules/controllers-requests.md) | FormRequest authorization and validation rules |
| `app/Http/Resources/**` | [controllers-requests.md](file:///home/gmroczek/git/fishing/.ai/rules/controllers-requests.md) | JSON API Resource schemas and GeoJSON compatibility |
| `app/Livewire/**` | [livewire-frontend.md](file:///home/gmroczek/git/fishing/.ai/rules/livewire-frontend.md) | Livewire 3 event contracts, debounced inputs, reactive UI |
| `resources/views/**` | [livewire-frontend.md](file:///home/gmroczek/git/fishing/.ai/rules/livewire-frontend.md) | Blade components, Alpine.js integration, Tailwind tokens |
| `tests/**` | [testing-sail.md](file:///home/gmroczek/git/fishing/.ai/rules/testing-sail.md) | Sail container execution, PHPUnit 11 conventions, strict assertions, isolated fixtures |
| `app/Services/*Sync*` | [nas-sync.md](file:///home/gmroczek/git/fishing/.ai/rules/nas-sync.md) | Two-way remote NAS sync, chunked binary media streaming, sync status tracking |
| `app/Http/Controllers/Api/v1/*Sync*` | [nas-sync.md](file:///home/gmroczek/git/fishing/.ai/rules/nas-sync.md) | NAS sync endpoints, token authentication, last-write-wins resolution |
