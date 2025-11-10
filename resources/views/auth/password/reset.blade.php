@extends('layout.app')
@section('title', 'Password Baru - ROMS')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3>Buat Password Baru</h3>
            </div>
            <div class="card-body">
                <p>Verifikasi berhasil untuk <strong>{{ $email }}</strong>. Silakan buat password baru Anda.</p>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="background-color: #B45253; border:none;">Simpan Password Baru</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection