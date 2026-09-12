# Synology NAS Synchronization Rules

**Scope**: `app/Services/*Sync*`, `app/Http/Controllers/Api/v1/*Sync*`

## Invariants

1. **Two-Way Sync Lifecycle & State Invariants**:
   - Maintain `sync_status` (`pending_upstream`, `synced`) and `synced_at` timestamps across all 13 database models.
   - When pulling records from the remote NAS server, preserve local IDs and synced statuses.
   - Use last-write-wins timestamp resolution for concurrent model updates.

2. **Chunked Binary Media Streaming**:
   - Media asset synchronization between local instances and the Synology NAS must use chunked binary streaming via `MediaSyncManager` (1 MB slices via `/api/v1/sync/media/chunk` and `/api/v1/sync/media/download`).
   - Never load entire binary files into in-memory base64 strings during synchronization.

3. **SHA-256 Deduplication & Checksum Integrity**:
   - Always verify remote media SHA-256 hashes (`/api/v1/sync/media/verify`) prior to transferring binary chunks to avoid redundant network traffic.
   - Validate checksum integrity on the receiving node after chunk reassembly before committing storage files.
