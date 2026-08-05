@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Lure</h1>
                    <p class="text-xs text-slate-500">Lure #{{ $lure->id }}</p>
                </div>
            </div>
            <a href="/lure" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Cancel</a>
        </div>

        {!! Form::model($lure, ['url' => 'lure', 'method' => 'put', 'class' => 'space-y-4']) !!}
            {!! Form::hidden('id') !!}

            <div class="space-y-1.5">
                {!! Form::label('name', 'Lure Model / Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('name', $lure->name, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'list' => 'nameList']) !!}

                <datalist id="nameList">
                    @foreach($lureNames as $item)
                        <option value="{{ $item->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                {!! Form::label('color', 'Color Pattern', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('color', $lure->color, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'list' => 'colorList']) !!}

                <datalist id="colorList">
                    @foreach($lureColors as $item)
                        <option value="{{ $item->color }}">
                    @endforeach
                </datalist>
            </div>

            <div class="space-y-1.5">
                {!! Form::label('size', 'Size / Weight', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                {!! Form::text('size', $lure->size, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'list' => 'sizeList']) !!}

                <datalist id="sizeList">
                    @foreach($lureSizes as $item)
                        <option value="{{ $item->size }}">
                    @endforeach
                </datalist>
            </div>

            <div class="pt-4 flex items-center gap-3">
                {!! Form::submit('Save Changes', ['class' => 'flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer']) !!}
                <a href='/lure' class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
            </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection
