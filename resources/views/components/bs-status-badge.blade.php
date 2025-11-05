@props(['status'])
@php
    $colors = [
        'pending' => 'badge-warning',
        'accepted' => 'badge-success',
        'denied' => 'badge-danger',
    ];
    $text = [
        'pending' => 'Tertunda',
        'accepted' => 'Diterima',
        'denied' => 'Ditolak',
    ];
@endphp
<span class="badge {{ $colors[$status] ?? 'badge-secondary' }}" style="font-size: 0.9rem; padding: 0.5em 0.8em;">
    {{ $text[$status] ?? $status }}
</span>