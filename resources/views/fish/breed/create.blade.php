@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="fish" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Fish Species</h1>
                    <p class="text-xs text-slate-500">Add new fish breed</p>
                </div>
            </div>
            <a href="/fish" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        {!! Form::model($breed, ['url' => 'fish/breed', 'class' => 'space-y-4']) !!}

            <div class="space-y-1.5">
                {!! Form::label('name', 'Species / Breed Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('name', null, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'list' => 'nameList']) !!}

                <datalist id="nameList">
                    @foreach($breeds as $item)
                        <option value="{{ $item->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    {!! Form::label('fish_families_id', 'Fish Family', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    <a href="/fish/family/create" class="text-xs text-teal-600 hover:underline font-bold">+ New Family</a>
                </div>
                {!! Form::select('fish_families_id', $families, null, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
            </div>

            <div class="pt-4 flex items-center gap-3">
                {!! Form::submit('Create Species', ['class' => 'flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer']) !!}
                <a href='/fish' class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
            </div>

        {!! Form::close() !!}

        @if ($errors->any())
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
