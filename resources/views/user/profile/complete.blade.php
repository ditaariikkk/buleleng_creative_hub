@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('title', 'Lengkapi Profil Anda')

@section('auth_header')
    {{-- Header tetap ada di atas modal --}}
    <h1>Lengkapi Profil Anda</h1>
    <p class="login-box-msg">Isi profil Anda untuk mendapatkan rekomendasi terbaik dari kami.</p>
@stop

@section('auth_body')
    {{-- Seluruh form sekarang ada di dalam modal --}}
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document"> {{-- modal-lg agar lebih lebar --}}
            <div class="modal-content">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Profil Pengguna</h5>
                        {{-- Tidak ada tombol close header agar user wajib mengisi --}}
                    </div>

                    <div class="modal-body">
                        {{-- Langkah 1: Informasi Dasar --}}
                        <div id="step1">
                            <p class="text-center text-bold">Langkah 1 dari 2: Informasi Dasar</p>
                            <hr>
                            <div class="form-group">
                                <label for="bio">Bio Singkat</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio', $profile->bio ?? '') }}</textarea>
                                @error('bio') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone_number">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $profile->phone_number ?? '') }}" placeholder="0812..." required>
                                @error('phone_number') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="portfolio_url">Link Portofolio (jika ada)</label>
                                <input type="url" class="form-control @error('portfolio_url') is-invalid @enderror" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $profile->portfolio_url ?? '') }}" placeholder="https://behance.net/namaanda">
                                @error('portfolio_url') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>

                            <div class="form-group">
                                {{-- Pastikan nama input 'user_photo' sesuai dengan controller --}}
                                <label for="user_photo">Unggah Foto Profil (Opsional)</label>
                                <input type="file" class="form-control-file @error('user_photo') is-invalid @enderror" id="user_photo" name="user_photo" accept="image/*">
                                @error('user_photo') <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                        </div>

                        {{-- Langkah 2: Minat & Kebutuhan --}}
                        <div id="step2" style="display: none;">
                            <p class="text-center text-bold">Langkah 2 dari 2: Minat & Kebutuhan</p>
                            <hr>
                            <div class="form-group">
                                <label>Pilih Sub Sektor Kreatif Anda <span class="text-danger">*</span></label>
                                <div class="row p-2 border rounded @error('sub_sectors') border-danger @enderror" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($subSectors as $sub)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                {{-- Gunakan name="sub_sectors[]" --}}
                                                <input class="form-check-input sub-sector-check" type="checkbox" name="sub_sectors[]" value="{{ $sub->sub_sector_id }}" id="sub_{{ $sub->sub_sector_id }}"
                                                    {{-- Perbaiki old() check untuk array --}}
                                                    {{ in_array($sub->sub_sector_id, old('sub_sectors', $profile->creativeSubSectors->pluck('sub_sector_id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sub_{{ $sub->sub_sector_id }}">{{ $sub->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                 {{-- Tampilkan error validasi Laravel --}}
                                 @error('sub_sectors') <span class="text-danger small"><strong>Pilih minimal satu sub sektor.</strong></span> @enderror
                                 {{-- Placeholder untuk error JS --}}
                                 <div id="sub_sector_error" class="text-danger small" style="display: none;">Pilih minimal satu sub sektor.</div>
                            </div>

                            <div class="form-group">
                                <label>Apa yang Anda Butuhkan? <span class="text-danger">*</span></label>
                                <div class="row p-2 border rounded @error('user_needs') border-danger @enderror" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($needs as $need)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                 {{-- Gunakan name="user_needs[]" --}}
                                                <input class="form-check-input user-need-check" type="checkbox" name="user_needs[]" value="{{ $need->need_id }}" id="need_{{ $need->need_id }}"
                                                     {{-- Perbaiki old() check untuk array, asumsi PK UserNeed 'need_id'--}}
                                                     {{ in_array($need->need_id, old('user_needs', $profile->userNeeds->pluck('need_id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="need_{{ $need->need_id }}">{{ $need->need_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Tampilkan error validasi Laravel --}}
                                @error('user_needs') <span class="text-danger small"><strong>Pilih minimal satu kebutuhan.</strong></span> @enderror
                                 {{-- Placeholder untuk error JS --}}
                                <div id="user_need_error" class="text-danger small" style="display: none;">Pilih minimal satu kebutuhan.</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                         <button type="button" class="btn btn-secondary" id="btn-prev-step" style="display: none;"><i class="fas fa-arrow-left"></i> Kembali</button>
                         <div> 
                            <button type="button" class="btn btn-primary" id="btn-next-step">Lanjut <i class="fas fa-arrow-right"></i></button>
                            <button type="submit" class="btn btn-success" id="btn-save-profile" style="display: none;"><i class="fas fa-check"></i> Simpan Profil</button>
                         </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    body.register-page { align-items: flex-start!important; padding-top: 2rem; padding-bottom: 2rem; }
    .modal-body { max-height: calc(100vh - 210px); overflow-y: auto; }
    .modal-dialog { margin-top: 5vh; }
    .border.rounded { max-height: 200px; overflow-y: auto; }
</style>
@stop

@section('js')
{{-- Pastikan SweetAlert ter-load, bisa dari AdminLTE config atau manual include --}}
{{-- Contoh jika perlu manual: <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}} 
<script>
    $(document).ready(function() {
        $('#profileModal').modal({ backdrop: 'static', keyboard: false, show: true });

        // Tampilkan SweetAlert jika ada error dari session controller
        @if(session('profile_update_error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('profile_update_error') }}",
                type: 'error', // type untuk SweetAlert v8 (sesuaikan jika versi Anda berbeda)
                // icon: 'error' // icon untuk SweetAlert v9+
            });
        @endif
        
        // Tentukan step awal berdasarkan adanya error validasi Laravel
        let initialStep = 1;
        @if ($errors->has('sub_sectors') || $errors->has('user_needs'))
            initialStep = 2;
        @endif
        
        let currentStep = initialStep; 
        
        function goToStep(step) {
            currentStep = step;
            $('#step1').toggle(step === 1);
            $('#step2').toggle(step === 2);
            $('#btn-next-step').toggle(step === 1);
            $('#btn-prev-step').toggle(step === 2);
            $('#btn-save-profile').toggle(step === 2);
             $('#profileModalLabel').text('Profil Pengguna - ' + (step === 1 ? 'Informasi Dasar' : 'Minat & Kebutuhan')); 
        }

        // Tampilkan step awal
         goToStep(currentStep);

        $('#btn-next-step').on('click', function() {
            let isValid = true;
            // Hanya cek field required di step 1
            if (!$('#phone_number').val()) { 
                $('#phone_number').addClass('is-invalid');
                isValid = false;
            } else {
                 $('#phone_number').removeClass('is-invalid');
            }
           
            if (!isValid) {
                 // Ganti alert dengan pesan yang lebih spesifik jika perlu
                 alert('Harap isi Nomor HP.'); 
                 return; 
            }
            goToStep(2);
        });

        $('#btn-prev-step').on('click', function() {
            goToStep(1);
        });

        // Validasi Checkbox Step 2 sebelum submit form
        $('form').on('submit', function(e) { // Lebih baik menargetkan form langsung
             let subSectorChecked = $('input.sub-sector-check:checked').length > 0;
             let userNeedChecked = $('input.user-need-check:checked').length > 0;
             
             $('#sub_sector_error').toggle(!subSectorChecked);
             $('#user_need_error').toggle(!userNeedChecked);

             if (currentStep === 2 && (!subSectorChecked || !userNeedChecked)) {
                 e.preventDefault(); // Hentikan submit HANYA jika di step 2 dan validasi gagal
                 // Ganti alert dengan Swal jika preferensi
                 alert('Harap pilih minimal satu Sub Sektor dan satu Kebutuhan.');
             }
             // Jika valid atau di step 1, form akan tersubmit
        });

         // Hapus pesan error JS saat checkbox dipilih
         $('input.sub-sector-check').on('change', function() { if ($('input.sub-sector-check:checked').length > 0) $('#sub_sector_error').hide(); });
         $('input.user-need-check').on('change', function() { if ($('input.user-need-check:checked').length > 0) $('#user_need_error').hide(); });

         // Hapus kelas is-invalid saat input required di step 1 diisi
         $('#phone_number').on('input', function() { if ($(this).val()) $(this).removeClass('is-invalid'); });

    });
</script>
@stop

