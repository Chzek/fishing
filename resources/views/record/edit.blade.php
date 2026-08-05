@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Logbook Record</h1>
                    <p class="text-xs text-slate-500">Record #{{ $record->id }}</p>
                </div>
            </div>
            <a href="/record" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        {!! Form::model($record, ['url' => 'record', 'method' => 'put', 'class' => 'space-y-4']) !!}
            {!! Form::hidden('id') !!}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('anglers_id', 'Angler', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::select('anglers_id', $anglers, $record->anglers_id, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>

                <div class="space-y-1.5">
                    {!! Form::label('lakes_id', 'Lake / Water', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::select('lakes_id', $lakes, $record->lakes_id, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('fish_breeds_id', 'Fish Species', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::select('fish_breeds_id', $fishes, $record->fish_breeds_id, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>
                
                <div class="space-y-1.5">
                    {!! Form::label('lures_id', 'Lure / Bait', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::select('lures_id', $lures, $record->lures_id, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('caught', 'Catch Date', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::date('caught', $record->caught, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>

                <div class="space-y-1.5">
                    {!! Form::label('weight', 'Weight (lbs)', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('weight', $record->weight, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('length', 'Length (in)', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('length', $record->length, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>

                <div class="space-y-1.5">
                    {!! Form::label('temperature', 'Water Temp (°F)', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('temperature', $record->temperature, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 font-mono text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>
            </div>

            <div class="space-y-1.5 pt-1">
                {!! Form::label('released', 'Released', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::select('released', [ '0' => 'No (Kept)', '1' => 'Yes (Released)'], $record->released, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
            </div>

            <div class="flex items-center gap-3 pt-4">
                {!! Form::submit('Save Changes', ['class' => 'flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer']) !!}
                <a href='/record' class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
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
