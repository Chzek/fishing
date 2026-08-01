@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 font-weight-bold">⚡ Boat Quick Catch Log</h4>
                    <span id="network-status" class="badge badge-light">Checking connection...</span>
                </div>

                <div class="card-body p-4">
                    <div id="quick-catch-alert" class="alert d-none mb-3" role="alert"></div>

                    <form id="quickCatchForm">
                        @csrf
                        <input type="hidden" id="client_id" name="client_id">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="anglers_id" class="font-weight-bold">Angler</label>
                                <select id="anglers_id" name="anglers_id" class="form-control form-control-lg" required>
                                    <option value="">Select Angler...</option>
                                    @foreach($anglers as $angler)
                                        <option value="{{ $angler->id }}">{{ $angler->fullName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="lakes_id" class="font-weight-bold">Lake</label>
                                <select id="lakes_id" name="lakes_id" class="form-control form-control-lg" required>
                                    <option value="">Select Lake...</option>
                                    @foreach($lakes as $lake)
                                        <option value="{{ $lake->id }}">{{ $lake->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fish_breeds_id" class="font-weight-bold">Fish Species</label>
                                <select id="fish_breeds_id" name="fish_breeds_id" class="form-control form-control-lg" required>
                                    <option value="">Select Fish Species...</option>
                                    @foreach($fishBreeds as $breed)
                                        <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="lures_id" class="font-weight-bold">Lure / Bait</label>
                                <select id="lures_id" name="lures_id" class="form-control form-control-lg">
                                    <option value="">Select Lure (Optional)...</option>
                                    @foreach($lures as $lure)
                                        <option value="{{ $lure->id }}">{{ $lure->displayName ?? $lure->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="length" class="font-weight-bold">Length (Inches)</label>
                                <input type="number" step="0.25" id="length" name="length" class="form-control form-control-lg" placeholder="e.g. 18.5" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="weight" class="font-weight-bold">Weight (Pounds - Optional)</label>
                                <input type="number" step="0.1" id="weight" name="weight" class="form-control form-control-lg" placeholder="e.g. 4.2">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="caught" class="font-weight-bold">Date Caught</label>
                                <input type="date" id="caught" name="caught" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="form-group col-md-6 d-flex align-items-end">
                                <div class="custom-control custom-checkbox custom-control-lg mb-2">
                                    <input type="checkbox" class="custom-control-input" id="released" name="released" value="1" checked>
                                    <label class="custom-control-label font-weight-bold text-success" for="released">
                                        🐟 Released Catch
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" id="saveCatchBtn" class="btn btn-success btn-block btn-lg font-weight-bold py-3">
                                💾 Save Catch Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
document.addEventListener('DOMContentLoaded', () => {
    const netStatus = document.getElementById('network-status');
    const updateNetStatus = () => {
        if (navigator.onLine) {
            netStatus.textContent = '🟢 Online (Cabin)';
            netStatus.className = 'badge badge-success';
        } else {
            netStatus.textContent = '⛵ Offline (Boat Mode)';
            netStatus.className = 'badge badge-warning';
        }
    };

    window.addEventListener('online', updateNetStatus);
    window.addEventListener('offline', updateNetStatus);
    updateNetStatus();

    // Restore last selected Angler & Lake preferences from localStorage
    const savedAngler = localStorage.getItem('fishinglog_last_angler');
    const savedLake = localStorage.getItem('fishinglog_last_lake');
    if (savedAngler) document.getElementById('anglers_id').value = savedAngler;
    if (savedLake) document.getElementById('lakes_id').value = savedLake;

    document.getElementById('quickCatchForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = e.target;
        const anglerVal = form.anglers_id.value;
        const lakeVal = form.lakes_id.value;

        // Remember Angler and Lake for next catch
        if (anglerVal) localStorage.setItem('fishinglog_last_angler', anglerVal);
        if (lakeVal) localStorage.setItem('fishinglog_last_lake', lakeVal);

        const catchData = {
            client_id: window.offlineSyncManager ? window.offlineSyncManager.generateUUID() : null,
            anglers_id: parseInt(form.anglers_id.value),
            lakes_id: parseInt(form.lakes_id.value),
            fish_breeds_id: parseInt(form.fish_breeds_id.value),
            lures_id: form.lures_id.value ? parseInt(form.lures_id.value) : null,
            length: parseFloat(form.length.value),
            weight: form.weight.value ? parseFloat(form.weight.value) : null,
            released: form.released.checked ? 1 : 0,
            caught: form.caught.value
        };

        const alertBox = document.getElementById('quick-catch-alert');

        if (navigator.onLine) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch('/api/v1/records', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify(catchData)
                });

                if (response.ok) {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent = '🎉 Catch successfully logged to server!';
                    alertBox.classList.remove('d-none');
                } else {
                    throw new Error('Server error');
                }
            } catch (err) {
                // Fallback to offline local storage
                await window.offlineSyncManager.saveCatchOffline(catchData);
                alertBox.className = 'alert alert-warning';
                alertBox.textContent = '⛵ Saved locally to boat offline queue!';
                alertBox.classList.remove('d-none');
            }
        } else {
            // Save offline directly
            await window.offlineSyncManager.saveCatchOffline(catchData);
            alertBox.className = 'alert alert-warning';
            alertBox.textContent = '⛵ Saved locally to boat offline queue! Will auto-sync at cabin.';
            alertBox.classList.remove('d-none');
        }

        // Reset length and weight for next entry
        form.length.value = '';
        form.weight.value = '';

        setTimeout(() => alertBox.classList.add('d-none'), 3000);
    });
});
@endsection
