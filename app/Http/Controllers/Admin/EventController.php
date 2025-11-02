<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreativeSubSector;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Menampilkan halaman index event.
     */
    public function index()
    {
        $venues = Venue::orderBy('venue_name', 'asc')->get();
        $creativeSubSectors = CreativeSubSector::orderBy('name')->get();

        $offlineEvents = Event::with('venue')
            ->where('event_type', 'offline')
            ->latest('event_id')
            ->paginate(10, ['*'], 'offline_page');

        $onlineEvents = Event::with('venue')
            ->where('event_type', 'online')
            ->latest('event_id')
            ->paginate(10, ['*'], 'online_page');

        return view('admin.events.index', compact('offlineEvents', 'onlineEvents', 'venues', 'creativeSubSectors'));
    }

    /**
     * Menyimpan event baru dari modal AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'event_type' => 'required|in:online,offline',
            'venue_id' => 'required_if:event_type,Offline|nullable|string',
            'new_venue_name' => 'required_if:venue_id,other|nullable|string|max:255|unique:venues,venue_name',
            'new_address' => 'required_if:venue_id,other|nullable|string',
            'new_capacity' => 'required_if:venue_id,other|nullable|integer|min:1',
            'new_contact' => 'required_if:venue_id,other|nullable|string|max:255',
            'sub_sectors' => 'required|array|min:1', // Validasi sub sektor
            'sub_sectors.*' => 'exists:creative_sub_sectors,sub_sector_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $venueId = null;

        if ($validated['event_type'] == 'offline') {
            if ($validated['venue_id'] == 'other') {
                try {
                    $newVenue = Venue::create([
                        'venue_name' => $validated['new_venue_name'],
                        'address' => $validated['new_address'],
                        'capacity' => $validated['new_capacity'],
                        'contact' => $validated['new_contact'],
                    ]);
                    $venueId = $newVenue->venue_id; // Asumsi PK Venue adalah 'venue_id'
                } catch (\Exception $e) {
                    Log::error('Gagal membuat venue baru: ' . $e->getMessage());
                    return response()->json(['error_message' => 'Gagal membuat venue baru. Pastikan nama unik dan DB Anda memiliki kolom (address, capacity, contact).'], 500);
                }
            } else {
                $venueId = $validated['venue_id'];
            }
        }

        try {
            $eventData = [
                'event_title' => $validated['event_title'],
                'description' => $validated['description'],
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'event_type' => $validated['event_type'],
                'venue_id' => $venueId,
            ];

            $event = Event::create($eventData);

            // Sync Sub Sektor setelah event dibuat
            if ($event && isset($validated['sub_sectors'])) {
                $event->creativeSubSectors()->sync($validated['sub_sectors']);
            }

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan event: ' . $e->getMessage());
            // Hapus venue baru jika event gagal dibuat (rollback sederhana)
            if ($validated['venue_id'] == 'other' && isset($newVenue)) {
                try {
                    $newVenue->delete();
                } catch (\Exception $delErr) {
                    Log::error('Gagal rollback venue: ' . $delErr->getMessage());
                }
            }
            return response()->json(['error_message' => 'Gagal menyimpan data acara. Error DB: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Acara berhasil ditambahkan.']);
    }

    /**
     * Mengambil data event untuk modal edit (termasuk relasi).
     */
    public function edit(Event $event)
    {
        $event->load('venue', 'creativeSubSectors'); // Muat relasi untuk edit
        return response()->json($event);
    }

    /**
     * Memperbarui event dari modal AJAX.
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'event_type' => 'required|in:online,offline',
            'venue_id' => 'required_if:event_type,Offline|nullable|string',
            // Rule unique harus mengabaikan venue yang sudah ada jika namanya sama
            'new_venue_name' => 'required_if:venue_id,other|nullable|string|max:255|unique:venues,venue_name',
            'new_address' => 'required_if:venue_id,other|nullable|string',
            'new_capacity' => 'required_if:venue_id,other|nullable|integer|min:1',
            'new_contact' => 'required_if:venue_id,other|nullable|string|max:255',
            'sub_sectors' => 'required|array|min:1',
            'sub_sectors.*' => 'exists:creative_sub_sectors,sub_sector_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $venueId = null;

        if ($validated['event_type'] == 'offline') {
            if ($validated['venue_id'] == 'other') {
                try {
                    $newVenue = Venue::create([
                        'venue_name' => $validated['new_venue_name'],
                        'address' => $validated['new_address'],
                        'capacity' => $validated['new_capacity'],
                        'contact' => $validated['new_contact'],
                    ]);
                    $venueId = $newVenue->venue_id; // Asumsi PK Venue adalah 'venue_id'
                } catch (\Exception $e) {
                    Log::error('Gagal membuat venue baru (update): ' . $e->getMessage());
                    return response()->json(['error_message' => 'Gagal membuat venue baru. Pastikan nama unik.'], 500);
                }
            } else {
                $venueId = $validated['venue_id'];
            }
        }

        try {
            $eventData = [
                'event_title' => $validated['event_title'],
                'description' => $validated['description'],
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'event_type' => $validated['event_type'],
                'venue_id' => $venueId,
            ];

            $event->update($eventData);

            // Sync Sub Sektor setelah event diupdate
            if (isset($validated['sub_sectors'])) {
                $event->creativeSubSectors()->sync($validated['sub_sectors']);
            } else {
                $event->creativeSubSectors()->detach(); // Hapus semua jika tidak ada yang dikirim
            }

        } catch (\Exception $e) {
            Log::error('Gagal mengupdate event: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal mengupdate data acara.'], 500);
        }

        return response()->json(['success' => 'Acara berhasil diperbarui.']);
    }

    /**
     * Menghapus event.
     */
    public function destroy(Event $event)
    {
        try {
            $event->creativeSubSectors()->detach(); // Hapus relasi pivot dulu
            $event->delete();
            return response()->json(['success' => 'Acara berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus event: ' . $e->getMessage());
            return response()->json(['error_message' => 'Gagal menghapus data acara.'], 500);
        }
    }

    /**
     * Menampilkan halaman detail event.
     */
    public function show(Event $event)
    {
        // Eager load relasi yang diperlukan untuk halaman show
        $event->load(['venue', 'creativeSubSectors']);
        return view('admin.events.show', compact('event'));
    }
}

