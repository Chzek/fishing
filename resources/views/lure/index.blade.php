@extends('layouts.app')

@section('content')
    <livewire:tacklebox.lure-catalog :initial-category="request('category', 'all')" />
@endsection
