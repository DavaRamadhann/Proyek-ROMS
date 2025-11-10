@extends('layout.app')
@section('title', 'Verifikasi Kode - ROMS')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3>Verifikasi Kode Reset</h3>
            </div>
            <div class="card-body">
                <p>Kami telah mengirimkan kode 6 digit ke <strong>{{ $email }}</strong>. Masukkan kode di bawah ini.</p>
                
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.check') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode Verifikasi (6 Digit)</label>
                        <input type="text" name="code" id="code" class="form-control" required autofocus maxlength="6" pattern="[0-9]{6}" placeholder="000000">
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="background-color: #84994F; border:none;">Verifikasi Kode</button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}">Kirim ulang kode?</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection