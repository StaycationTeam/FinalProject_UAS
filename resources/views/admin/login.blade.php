@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="container">
    <div class="card" style="max-width: 400px; margin: 80px auto;">
        <div class="card-header text-center">
            <h4>Admin Login</h4>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ url('admin/login') }}">
                @csrf

                <div class="form-group mb-3">
                    <label>Password Admin</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
