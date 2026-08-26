---
name: nas-sync-architect
description: Specialized Remote Synchronization Agent for managing two-way NAS push/pull payloads, base64 media asset synchronization, queueable background jobs (SyncNasJob), and sync state invariants across all 13 database models in the Fishing Logbook application.
---

# NAS Sync Architect Skill

This skill governs two-way remote synchronization between the local Fishing Logbook instance and the Synology NAS server via [`NasSyncService.php`](file:///home/gmroczek/git/fishing/app/Services/NasSyncService.php).

---

## Core Sync Invariants

1. **`HasUuidAndSyncTracking` Trait Enforcement**:
   - All 13 synced models (`User`, `Angler`, `Lake`, `FishFamily`, `FishBreed`, `Lure`, `Record`, `Expedition`, `Crew`, `Post`, `FishingZone`, `FishingRule`, `Photo`) MUST use `HasUuidAndSyncTracking`.
   - Modifying a model sets `sync_status = 'pending'`, triggering sync inclusion on next run.
2. **Payload Contracts (`/api/v1/sync/push` & `/api/v1/sync/pull`)**:
   - Binary media files (`photos`, `anglers` avatars, `fish_breeds` images) are transmitted as `file_base64`, `avatar_base64`, or `image_base64`.
   - Chunk sizes MUST remain throttled (`photos`, `fish_breeds`, `anglers` chunk size = 2; standard models = 50) to prevent NAS request payload limits.
3. **Queueing Strategy (`SyncNasJob`)**:
   - Never execute `NasSyncService::sync()` synchronously inside interactive web HTTP requests.
   - Always dispatch `SyncNasJob::dispatch()` to run background sync asynchronously with automatic retry policy.
4. **Test Environment Safety**:
   - Always use `Http::fake()` when testing NAS sync interactions so local test runs never send HTTP requests to the live NAS instance.
