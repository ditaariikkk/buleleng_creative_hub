@extends('adminlte::auth.auth-page', ['auth_type' => 'login']) {{-- Pastikan auth_type benar --}}
@section('title', 'Login - Buleleng Creative Hub')
@section('auth_body')

@php
    // Logika PHP untuk URL tetap sama
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

<div class="card">
    <div class="card-body p-3">

        <div class="text-center">
            <h4 class="mb-2" style="font-weight: 900;">Selamat Datang di Buleleng Creative Hub</h4>
        </div>
        <div class="row align-items-center">

            {{-- Kolom untuk Gambar --}}
            <div class="col-lg-7 text-center d-none d-lg-block">
                <img src="{{ asset('vendor/adminlte/dist/img/Creative_thinking.gif') }}" class="img-fluid"
                    alt="Welcome Image" style="max-width: 380px;">
            </div>

            {{-- Kolom untuk Form Login --}}
            <div class="col-lg-5">
                <h4 class="text-center mb-4" style="font-weight: 700;">Login</h4>
                <form action="{{ $loginUrl }}" method="post">
                    @csrf

                    {{-- Email field --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                        </div>
                        @error('email')
                            {{-- Pesan error kustom atau default --}}
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ str_contains($message, 'required') ? 'Kolom Email wajib diisi.' : 'Email atau Password yang Anda masukkan salah.' }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Password field --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password"
                            class="form-control password-input @error('password') is-invalid @enderror"
                            placeholder="{{ __('adminlte::adminlte.password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text toggle-password" style="cursor: pointer;">
                                <span class="fas fa-eye toggle-icon"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>Kolom Password wajib diisi.</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Remember me & Login button --}}
                    <div class="row align-items-center mt-4">
                        <div class="col-7">
                            <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">{{ __('adminlte::adminlte.remember_me') }}</label>
                            </div>
                        </div>
                        <div class="col-5">
                            <button type=submit class="btn btn-block btn-primary">
                                Login
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Footer Links --}}
                <div class="text-center mt-3">
                    @if($passResetUrl)
                        <p class="mb-0">
                            <a href="{{ $passResetUrl }}">
                                Lupa password?
                            </a>
                        </p>
                    @endif
                    {{-- PERBAIKAN: Link Registrasi ditambahkan --}}
                    @if($registerUrl)
                        <p class="mb-0">
                            <a href="{{ $registerUrl }}">
                                Belum Memiliki Akun? Daftar
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggles = document.querySelectorAll('.toggle-password');
        toggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const container = this.closest('.input-group');
                const passwordInput = container.querySelector('.password-input');
                const toggleIcon = this.querySelector('.toggle-icon');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
        });
    });
</script>
@stop

@section('css')
<style>
    .login-box,
    .register-box {
        width: 850px !important;
        max-width: 95vw;
    }
</style>
@stop