@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('title', 'Register - Buleleng Creative Hub')
@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
@php(    $login_url = $login_url ? route($login_url) : '' )
@php(    $register_url = $register_url ? route($register_url) : '' )
@else
@php(    $login_url = $login_url ? url($login_url) : '' )
@php(    $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_body')
<div class="card">
    <div class="card-body p-3">

        <div class="text-center">
            <h4 class="mb-2" style="font-weight: 700;">Bergabung dengan Buleleng Creative Hub</h4>
        </div>
        <div class="row align-items-center">

            {{-- Kolom untuk Gambar (Sama seperti Login) --}}
            <div class="col-lg-7 text-center d-none d-lg-block">
                <img src="{{ asset('vendor/adminlte/dist/img/Creative_thinking.gif') }}" class="img-fluid"
                    alt="Register Image" style="max-width: 380px;">
            </div>

            {{-- Kolom untuk Form Register --}}
            <div class="col-lg-5">
                <h4 class="text-center mb-4" style="font-weight: 700;">Registrasi Akun Baru</h4>
                <form action="{{ $register_url }}" method="post">
                    @csrf

                    {{-- Name field --}}
                    <div class="input-group mb-3">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>
                        @error('name') <span class="invalid-feedback"
                        role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Email field --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                        </div>
                        @error('email') <span class="invalid-feedback"
                        role="alert"><strong>{{ $message }}</strong></span> @enderror
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
                        @error('password') <span class="invalid-feedback"
                        role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    {{-- Confirm password field --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control password-input"
                            placeholder="{{ __('adminlte::adminlte.retype_password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text toggle-password" style="cursor: pointer;">
                                <span class="fas fa-eye toggle-icon"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Register button --}}
                    <button type="submit"
                        class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                        <span class="fas fa-user-plus"></span>
                        {{ __('adminlte::adminlte.register') }}
                    </button>
                </form>

                {{-- Footer Link --}}
                <div class="text-center mt-3">
                    <p class="mb-0">
                        <a href="{{ $login_url }}">
                            Sudah Punya Akun? Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
{{-- Salin JavaScript yang sama dari login.blade.php untuk toggle password --}}
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
{{-- Gunakan CSS yang sama dari login.blade.php untuk lebar box --}}
<style>
    .login-box,
    .register-box {
        width: 850px !important;
        max-width: 95vw;
    }
</style>
@stop