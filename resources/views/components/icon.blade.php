@props([
    'name' => '',
    'class' => 'w-5 h-5',
    'strokeWidth' => '2',
])

@php
    // Normalisasi nama icon: LayoutDashboard / layout_dashboard -> layout-dashboard
    $normalized = strtolower(trim(preg_replace(['/([a-z\d])([A-Z])/', '/[^a-zA-Z\d]+/'], '$1-$2', $name), '-'));

    // Alias mapping untuk memastikan pemetaan konsisten sesuai instruksi
    $aliases = [
        'dashboard' => 'layout-dashboard',
        'layoutdashboard' => 'layout-dashboard',
        'buku' => 'book-open',
        'book' => 'book-open',
        'bookopen' => 'book-open',
        'siswa' => 'users',
        'user' => 'users',
        'peminjaman' => 'clipboard-list',
        'clipboardlist' => 'clipboard-list',
        'menunggu' => 'clock',
        'antrean' => 'clock',
        'persetujuan' => 'circle-check',
        'selesai' => 'circle-check',
        'lunas' => 'circle-check',
        'check' => 'circle-check',
        'circlecheck' => 'circle-check',
        'denda' => 'wallet',
        'pencarian' => 'search',
        'pengaturan' => 'settings',
        'excel' => 'file-spreadsheet',
        'export-excel' => 'file-spreadsheet',
        'filespreadsheet' => 'file-spreadsheet',
        'tambah' => 'plus',
        'riwayat' => 'history',
        'keluar' => 'log-out',
        'logout' => 'log-out',
        'login' => 'log-in',
        'masuk' => 'log-in',
        'lihat' => 'eye',
        'detail' => 'eye',
        'eye-off' => 'eye-off',
        'eyeoff' => 'eye-off',
        'edit' => 'pencil',
        'ubah' => 'pencil',
        'hapus' => 'trash-2',
        'trash' => 'trash-2',
        'trash2' => 'trash-2',
        'simpan' => 'save',
        'tutup' => 'x',
        'batal' => 'x',
        'close' => 'x',
        'unduh' => 'download',
        'unggah' => 'upload',
        'muat-ulang' => 'refresh-cw',
        'refresh' => 'refresh-cw',
        'refreshcw' => 'refresh-cw',
        'tanggal' => 'calendar-days',
        'calendardays' => 'calendar-days',
        'calendar' => 'calendar-days',
        'dropdown' => 'chevron-down',
        'chevrondown' => 'chevron-down',
        'kembali' => 'arrow-left',
        'arrowleft' => 'arrow-left',
        'tautan-eksternal' => 'external-link',
        'externallink' => 'external-link',
        'peringatan' => 'alert-triangle',
        'terlambat' => 'clock',
        'alert' => 'alert-circle',
        'foto' => 'camera',
    ];

    $key = $aliases[$normalized] ?? $normalized;
@endphp

<svg {{ $attributes->merge([
    'class' => 'lucide lucide-' . $key . ' inline-block shrink-0 ' . $class,
    'xmlns' => 'http://www.w3.org/2000/svg',
    'viewBox' => '0 0 24 24',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'stroke-width' => $strokeWidth,
    'stroke-linecap' => 'round',
    'stroke-linejoin' => 'round',
    'aria-hidden' => 'true'
]) }}>
    @switch($key)
        @case('layout-dashboard')
            <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
            @break

        @case('book-open')
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
            @break

        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            @break

        @case('user')
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            @break

        @case('clipboard-list')
            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>
            @break

        @case('clock')
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            @break

        @case('circle-check')
            <circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>
            @break

        @case('wallet')
            <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
            @break

        @case('search')
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            @break

        @case('settings')
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>
            @break

        @case('file-spreadsheet')
            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h2"/><path d="M14 13h2"/><path d="M8 17h2"/><path d="M14 17h2"/>
            @break

        @case('plus')
            <path d="M5 12h14"/><path d="M12 5v14"/>
            @break

        @case('filter')
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            @break

        @case('history')
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>
            @break

        @case('log-out')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
            @break

        @case('log-in')
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/>
            @break

        @case('eye')
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
            @break

        @case('eye-off')
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>
            @break

        @case('pencil')
            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/>
            @break

        @case('trash-2')
            <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/>
            @break

        @case('save')
            <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/>
            @break

        @case('x')
            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
            @break

        @case('download')
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
            @break

        @case('upload')
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>
            @break

        @case('refresh-cw')
            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>
            @break

        @case('calendar-days')
            <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>
            @break

        @case('chevron-down')
            <path d="m6 9 6 6 6-6"/>
            @break

        @case('arrow-left')
            <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
            @break

        @case('arrow-right')
            <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
            @break

        @case('external-link')
            <path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            @break

        @case('alert-circle')
        @case('circle-alert')
            <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>
            @break

        @case('alert-triangle')
        @case('triangle-alert')
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/>
            @break

        @case('info')
            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
            @break

        @case('camera')
            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>
            @break

        @case('menu')
            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
            @break

        @case('lock')
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            @break

        @case('sparkles')
            <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
            @break

        @case('check')
            <polyline points="20 6 9 17 4 12"/>
            @break

        @case('file-text')
            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>
            @break

        @case('package')
            <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
            @break

        @default
            <!-- Default Circle -->
            <circle cx="12" cy="12" r="10"/>
    @endswitch
</svg>
