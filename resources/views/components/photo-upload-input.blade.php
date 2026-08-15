@props([
    'name' => 'photos[]',
    'id' => 'photo-uploader-' . uniqid(),
    'multiple' => true,
    'label' => 'Optional Photos',
    'hint' => 'Take or upload photos. Automatically compressed for offline speed.',
    'directCamera' => true,
])

<div id="{{ $id }}" class="space-y-2.5">
    <div class="flex items-center justify-between">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            {{ $label }}
        </label>
        <span class="photo-status-text text-[11px] text-teal-600 font-medium hidden"></span>
    </div>

    <!-- Drop & Select Zone -->
    <div class="photo-drop-zone border-2 border-dashed border-slate-200 hover:border-teal-400 bg-slate-50/50 hover:bg-teal-50/20 rounded-2xl p-5 text-center transition-all cursor-pointer relative group">
        <input 
            type="file" 
            name="{{ $name }}" 
            accept="image/*" 
            {{ $multiple ? 'multiple' : '' }} 
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
        >

        <div class="space-y-2 pointer-events-none">
            <div class="w-10 h-10 rounded-xl bg-white text-slate-400 group-hover:text-teal-600 group-hover:bg-teal-50 shadow-xs border border-slate-200/80 group-hover:border-teal-200 flex items-center justify-center mx-auto transition-colors">
                <i data-lucide="camera" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-700 group-hover:text-teal-900">
                    Click to browse or take photo
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $hint }}</p>
            </div>
        </div>
    </div>

    <!-- Live Preview Grid -->
    <div class="photo-preview-grid grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5 hidden">
        <!-- Javascript renders preview cards here -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.initPhotoUploader === 'function') {
            window.initPhotoUploader('{{ $id }}', {
                maxWidth: 1600,
                maxHeight: 1600,
                quality: 0.82
            });
        }
    });
</script>
