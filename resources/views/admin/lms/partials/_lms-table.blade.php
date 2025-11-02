<div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap" id="{{ $tableId ?? 'lmsTable' }}">
        <thead>
            <tr>
                <th style="width: 50px" class="text-center">No</th>
                <th class="text-center">Nama Konten</th>
                <th class="text-center">Deskripsi</th>
                <th style="width: 200px" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contents as $content)
                <tr class="text-center" id="row-content-{{ $content->content_id }}">
                    {{-- Menggunakan $loop->iteration untuk nomor urut per halaman --}}
                    <td>{{ $loop->iteration + ($contents->currentPage() - 1) * $contents->perPage() }}</td>
                    <td>{{ Str::limit($content->content_title, 40) }}</td>
                    <td>{{ Str::limit($content->description, 40) }}</td>
                    <td class="text-center">
                        {{-- Logika Tombol Detail (URL vs File) --}}
                        @php
                            $isUrl = filter_var($content->source, FILTER_VALIDATE_URL);
                            // Jika bukan URL, anggap itu path file dan buat URL storage
                            $detailUrl = $isUrl ? $content->source : ($content->source ? asset('storage/' . $content->source) : '#');
                            $target = $isUrl ? '_blank' : '_blank'; // Buka di tab baru untuk keduanya
                            $iconClass = $isUrl ? 'fa-external-link-alt' : 'fa-eye'; // Ikon berbeda
                            $title = $isUrl ? 'Buka Link Eksternal' : 'Lihat File';
                        @endphp
                        {{-- Nonaktifkan tombol jika source tidak valid/kosong --}}
                        <a href="{{ $detailUrl }}" target="{{ $target }}"
                            class="btn btn-sm btn-info {{ $detailUrl === '#' ? 'disabled' : '' }}"
                            title="{{ $detailUrl === '#' ? 'Sumber tidak tersedia' : $title }}">

                            <i class="fas {{ $iconClass }}"></i> Lihat
                        </a>
                        {{-- Akhir Logika Tombol Detail --}}

                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $content->content_id }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $content->content_id }}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Data media pembelajaran belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($contents->hasPages())
    <div class="card-footer"> {{ $contents->links() }} </div>
@endif