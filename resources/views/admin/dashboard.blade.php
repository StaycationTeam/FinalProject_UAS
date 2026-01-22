@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    <div class="dashboard-header">
        <h2>Admin Dashboard</h2>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-card">
            <h4>Building Settings</h4>
            <p>Edit base production & cost setiap building.</p>
            <a href="{{ url('admin/buildings') }}" class="btn btn-primary">
                Manage Buildings
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.logout') }}" style="margin-top: 20px;">
        @csrf
        <button class="btn btn-danger">
            Logout
        </button>
    </form>

</div>
@endsection
