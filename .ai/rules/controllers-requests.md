# HTTP Controllers, Form Requests & API Rules

**Scope**: `app/Http/Controllers/**`, `app/Http/Requests/**`, `app/Http/Resources/**`

## Invariants

1. **Thin Controllers**:
   - HTTP controllers in `app/Http/Controllers/` must remain lightweight HTTP coordinators.
   - Controllers should only authorize the request, extract validated input, delegate mutation logic to an Action class or Domain Service, and return a view or redirect response.
   - Never write raw SQL queries, multi-step transaction loops, or complex business math directly inside controller actions.

2. **Form Request Validation**:
   - Extract validation into dedicated `FormRequest` classes under `app/Http/Requests/` (e.g. `StoreRecordRequest`, `UpdateLakeRequest`).
   - Avoid inline `$request->validate([...])` in controller methods.
   - Form requests must define `authorize(): bool` with explicit role/policy checks and `rules(): array` with standard Laravel validation rules.

3. **API Endpoints & Eloquent Resources**:
   - All JSON API routes under `/api/v1/` must return structured Eloquent API Resources (`app/Http/Resources/`).
   - API Resources must be annotated with `/** @mixin \Fishinglog\Models\<Model> */` for static analysis support.
   - Include spatial coordinates and ISO 8601 formatted timestamps in API JSON responses.
