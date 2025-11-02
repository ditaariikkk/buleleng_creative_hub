<div class="card-body table-responsive p-0">
    {{-- PERBAIKAN: Menggunakan $tableId agar pencarian di tab berfungsi --}}
    <table class="table table-hover text-nowrap" id="{{ $tableId ?? 'eventTable' }}">
        <thead>
            <tr>
                <th style="width: 50px" class="text-center">No</th>
                <th class="text-center">Nama Kegiatan</th>
                <th class="text-center">Status</th>
                <th class="text-center">Tempat</th>
                <th style="width: 200px" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                {{-- PERBAIKAN: Menggunakan event_id --}}
                <tr class="text-center" id="row-event-{{ $event->event_id }}">
                    <td>{{ $loop->iteration + $events->firstItem() - 1 }}</td>
                    <td>{{ $event->event_title }}</td>
                    <td>
                        @php
                            // Accessor 'event_status' dari Model akan otomatis dipanggil
                            $status = $event->event_status;
                            $badgeClass = 'badge-secondary';
                            if ($status == 'Sedang Berlangsung')
                                $badgeClass = 'badge-success';
                            if ($status == 'Telah Berakhir')
                                $badgeClass = 'badge-danger';
                            if ($status == 'Belum Terlaksana')
                                $badgeClass = 'badge-info';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                    </td>
                    <td>{{ $event->venue->venue_name ?? 'Online' }}</td>
                    <td class="text-center">
                        {{-- PERBAIKAN: Menggunakan event_id dan membuka komentar route --}}
                        <a href="{{ route('admin.events.show', $event->event_id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        {{-- PERBAIKAN: Menggunakan event_id --}}
                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $event->event_id }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        {{-- PERBAIKAN: Menggunakan event_id --}}
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $event->event_id }}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Data acara belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($events->hasPages())
    <div class="card-footer">
        {{ $events->links() }}
    </div>
@endif