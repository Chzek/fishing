@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="fishing-hook" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Create Tackle / Lure</h1>
                    <p class="text-xs text-slate-500">Add to tackle box inventory</p>
                </div>
            </div>
            <a href="/lure" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        {!! Form::model($lure, ['url' => 'lure', 'class' => 'space-y-4']) !!}

            <div class="space-y-1.5">
                {!! Form::label('name', 'Lure Model / Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('name', null, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'placeholder' => 'e.g. Rapala Shad Rap']) !!}
            </div>

            <div class="space-y-1.5">
                {!! Form::label('color', 'Color Pattern', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('color', null, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'placeholder' => 'e.g. Firetiger / Silver Flash']) !!}
            </div>

            <div class="space-y-1.5">
                {!! Form::label('size', 'Size / Weight', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('size', null, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'placeholder' => 'e.g. 3/8 oz, #7']) !!}
            </div>

            <div class="pt-4 flex items-center gap-3">
                {!! Form::submit('Create Lure', ['class' => 'flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer']) !!}
                <a href='/lure' class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
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
