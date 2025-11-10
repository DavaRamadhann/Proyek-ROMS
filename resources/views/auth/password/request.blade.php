@extends('layout.app')
@section('title', 'Lupa Password - ROMS')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3>Lupa Password</h3>
            </div>
            <div class="card-body">
                <p>Masukkan email Anda. Kami akan mengirimkan kode 6 digit untuk me-reset password Anda.</p>
                
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #FCB53B; border:none;">Kirim Kode Reset</button>
                </form>
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}">← Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection