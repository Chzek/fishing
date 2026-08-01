/**
 * FishingLog Offline Sync Manager
 * Handles IndexedDB storage, Client UUID generation, and Background/Manual Sync.
 */

const DB_NAME = 'FishingLogOfflineDB';
const DB_VERSION = 1;
const STORE_NAME = 'pending_catches';
const REF_STORE = 'reference_data';

class OfflineSyncManager {
  constructor() {
    this.db = null;
    this.isSyncing = false;
    this.initDB();
  }

  generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  async initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, DB_VERSION);

      request.onupgradeneeded = (event) => {
        const db = event.target.result;
        if (!db.objectStoreNames.contains(STORE_NAME)) {
          db.createObjectStore(STORE_NAME, { keyPath: 'client_id' });
        }
        if (!db.objectStoreNames.contains(REF_STORE)) {
          db.createObjectStore(REF_STORE, { keyPath: 'id' });
        }
      };

      request.onsuccess = (event) => {
        this.db = event.target.result;
        this.updateSyncBadge();
        this.fetchAndCacheReferenceData();
        resolve(this.db);
      };

      request.onerror = (event) => {
        console.error('IndexedDB init error:', event.target.error);
        reject(event.target.error);
      };
    });
  }

  async saveCatchOffline(catchData) {
    if (!this.db) await this.initDB();

    if (!catchData.client_id) {
      catchData.client_id = this.generateUUID();
    }
    catchData.queued_at = new Date().toISOString();

    return new Promise((resolve, reject) => {
      const tx = this.db.transaction(STORE_NAME, 'readwrite');
      const store = tx.objectStore(STORE_NAME);
      const req = store.put(catchData);

      req.onsuccess = () => {
        this.updateSyncBadge();
        resolve(catchData);
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async getPendingCatches() {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const tx = this.db.transaction(STORE_NAME, 'readonly');
      const store = tx.objectStore(STORE_NAME);
      const req = store.getAll();

      req.onsuccess = () => resolve(req.result || []);
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async removePendingCatch(clientId) {
    if (!this.db) await this.initDB();

    return new Promise((resolve, reject) => {
      const tx = this.db.transaction(STORE_NAME, 'readwrite');
      const store = tx.objectStore(STORE_NAME);
      const req = store.delete(clientId);

      req.onsuccess = () => {
        this.updateSyncBadge();
        resolve();
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async updateSyncBadge() {
    const pending = await this.getPendingCatches();
    const badgeEl = document.getElementById('offline-sync-badge');
    const badgeCountEl = document.getElementById('offline-sync-count');

    if (badgeEl && badgeCountEl) {
      if (pending.length > 0) {
        badgeCountEl.textContent = pending.length;
        badgeEl.style.display = 'inline-block';
      } else {
        badgeEl.style.display = 'none';
      }
    }
  }

  async fetchAndCacheReferenceData() {
    if (!navigator.onLine) return;

    try {
      const response = await fetch('/api/v1/reference-data');
      if (response.ok) {
        const data = await response.json();
        localStorage.setItem('fishinglog_ref_data', JSON.stringify(data));
      }
    } catch (err) {
      console.log('Using cached reference data due to offline status:', err);
    }
  }

  getCachedReferenceData() {
    const raw = localStorage.getItem('fishinglog_ref_data');
    return raw ? JSON.parse(raw) : null;
  }

  async syncNow() {
    if (this.isSyncing) return;
    if (!navigator.onLine) {
      alert('You are currently offline. Connect to Wi-Fi/cellular network to sync catches.');
      return;
    }

    this.isSyncing = true;
    const pending = await this.getPendingCatches();

    if (pending.length === 0) {
      this.isSyncing = false;
      return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let syncedCount = 0;

    for (const record of pending) {
      try {
        const res = await fetch('/api/v1/records', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
          },
          body: JSON.stringify(record)
        });

        if (res.ok || res.status === 200 || res.status === 201) {
          await this.removePendingCatch(record.client_id);
          syncedCount++;
        }
      } catch (err) {
        console.error('Error syncing record:', record.client_id, err);
      }
    }

    this.isSyncing = false;
    this.updateSyncBadge();

    if (syncedCount > 0) {
      const banner = document.getElementById('offline-sync-alert');
      if (banner) {
        banner.textContent = `🎉 Successfully synced ${syncedCount} catch(es) to server!`;
        banner.classList.remove('d-none');
        setTimeout(() => banner.classList.add('d-none'), 4000);
      }
    }
  }
}

window.offlineSyncManager = new OfflineSyncManager();

window.addEventListener('online', () => {
  console.log('Network connected. Triggering auto-sync...');
  window.offlineSyncManager.syncNow();
});
