<div class="inline-flex items-center gap-1">
    @if(view()->exists(implode(".", [$name, 'edit'])))
        @if(!isset($user) || (isset($user) && Auth::id() == $user))
            <a href="/{{ $name }}/{{ $identifier }}/edit" title="Edit" class="p-1.5 rounded-lg text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition-colors">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
            </a>
        @endif
    @endif
    @if(view()->exists(implode(".", [$name, 'show'])))
        <a href="/{{ $name }}/{{ $identifier }}" title="View Detail" class="p-1.5 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-50 transition-colors">
            <i data-lucide="eye" class="w-4 h-4"></i>
        </a>
    @endif
    @if(view()->exists(implode(".", [$name, 'profile'])))
        <a href="/{{ $name }}/{{ $identifier }}/profile" title="Angler Profile" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
            <i data-lucide="user-check" class="w-4 h-4"></i>
        </a>
    @endif
</div>