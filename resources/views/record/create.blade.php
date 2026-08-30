@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <x-lucide-plus-circle class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Logbook Record</h1>
                    <p class="text-xs text-slate-500">Log a new catch entry</p>
                </div>
            </div>
            <a href="/record" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        <form action="{{ url('/record') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="anglers_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Angler</label>
                    <select id="anglers_id" name="anglers_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($anglers as $val => $label)
                            <option value="{{ $val }}" {{ old('anglers_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="lakes_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lake / Water</label>
                    <select id="lakes_id" name="lakes_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($lakes as $val => $label)
                            <option value="{{ $val }}" {{ old('lakes_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="fish_breeds_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Fish Species</label>
                    <select id="fish_breeds_id" name="fish_breeds_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($fishes as $val => $label)
                            <option value="{{ $val }}" {{ old('fish_breeds_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-1.5">
                    <label for="lures_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Lure / Bait</label>
                    <select id="lures_id" name="lures_id" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        @foreach($lures as $val => $label)
                            <option value="{{ $val }}" {{ old('lures_id') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="caught" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Catch Date</label>
                    <input type="date" id="caught" name="caught" value="{{ old('caught', date('Y-m-d')) }}" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="weight" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Weight (lbs)</label>
                    <input type="text" id="weight" name="weight" value="{{ old('weight') }}" placeholder="e.g. 4.25" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="length" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Length (in)</label>
                    <input type="text" id="length" name="length" value="{{ old('length') }}" placeholder="e.g. 19.5" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="temperature" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Water Temp (°F)</label>
                    <input type="text" id="temperature" name="temperature" value="{{ old('temperature') }}" placeholder="e.g. 68" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="space-y-2 pt-1 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Exact Catch GPS Pinpoint (Optional)</label>
                    <button type="button" onclick="acquireDeviceGPS()" class="text-xs font-bold text-teal-600 hover:text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200 transition-colors">📡 Query Device GPS</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="latitude" class="block text-[11px] font-semibold text-slate-600">Latitude (°N)</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="e.g. 48.012345" class="w-full h-10 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-xs focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    </div>
                    <div class="space-y-1.5">
                        <label for="longitude" class="block text-[11px] font-semibold text-slate-600">Longitude (°W)</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="e.g. -84.821945" class="w-full h-10 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-xs focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    </div>
                </div>
            </div>

            <div class="space-y-1.5 pt-1">
                <label for="released" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Released</label>
                <select id="released" name="released" class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="0" {{ old('released') == '0' ? 'selected' : '' }}>No (Kept)</option>
                    <option value="1" {{ old('released') == '1' ? 'selected' : '' }}>Yes (Released)</option>
                </select>
            </div>

            <!-- Optional Catch Photos Upload Component -->
            <div class="pt-2 border-t border-slate-100">
                <x-photo-upload-input 
                    name="photos[]" 
                    id="record-create-photos" 
                    label="Optional Catch Photos" 
                    hint="Take a picture on the boat or select from album. Auto-compressed for offline speed." 
                />
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer">Create Record</button>
            </div>

        </form>

        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-4 space-y-1">
                <strong class="font-bold">Please correct the errors below:</strong>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function acquireDeviceGPS() {
        if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            alert('🔒 Browsers block GPS on HTTP over LAN.\n\nTo enable GPS on your mobile device:\n1. Use HTTPS or localhost, OR\n2. In Chrome on mobile, visit chrome://flags/#unsafely-treat-insecure-origin-as-secure and add http://' + location.hostname);
            return;
        }

        if (!navigator.geolocation) {
            alert('Hardware GPS is not supported on this browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('latitude').value = pos.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = pos.coords.longitude.toFixed(6);
            },
            (err) => {
                alert('Could not acquire GPS position: ' + (err.message || 'Permission denied. Ensure HTTPS or Chrome flags allow location.'));
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    }
</script>
@endsection
