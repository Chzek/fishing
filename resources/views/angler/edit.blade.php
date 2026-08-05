@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Angler Profile</h1>
                    <p class="text-xs text-slate-500">{{ $angler->fullName }}</p>
                </div>
            </div>
            <a href="/angler" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Return</a>
        </div>

        {!! Form::model($angler, ['url' => 'angler', 'method' => 'put', 'files' => 'true', 'class' => 'space-y-4']) !!}
            {!! Form::hidden('id') !!}

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('firstName', 'First Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('firstName', $angler->firstName, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'required' => true]) !!}
                </div>

                <div class="space-y-1.5">
                    {!! Form::label('middleName', 'Middle Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('middleName', $angler->middleName, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>

                <div class="space-y-1.5">
                    {!! Form::label('lastName', 'Last Name', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::text('lastName', $angler->lastName, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'required' => true]) !!}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    {!! Form::label('user_id', 'User Account', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::select('user_id', $users, $angler->user_id, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500', 'placeholder' => 'Select account link...']) !!}
                </div>

                <div class="space-y-1.5">
                    {!! Form::label('birthdate', 'Birthday', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                    {!! Form::date('birthdate', $angler->birthdate, ['class' => 'w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500']) !!}
                </div>
            </div>

            <div class="space-y-2 pt-1">
                {!! Form::label('avatar', 'Profile Photo Avatar', ['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-700']) !!}
                <div class="flex items-center gap-4">
                    <img src="{{ '/storage/avatars/'.$angler->avatar }}" id="avatar-img-tag" class="w-16 h-16 rounded-full object-cover border border-slate-200 shrink-0" />
                    {!! Form::input('file', 'avatar', '/storage/avatars/'.$angler->avatar, ['class' => 'flex-1 p-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 text-slate-700', 'onChange' => 'readURL(this);']) !!}
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                {!! Form::submit('Save Changes', ['class' => 'flex-1 py-3 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors cursor-pointer']) !!}
                <a href='/angler' class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl border border-slate-200 transition-colors">Cancel</a>
            </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('scripts')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                document.getElementById('avatar-img-tag').setAttribute('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection